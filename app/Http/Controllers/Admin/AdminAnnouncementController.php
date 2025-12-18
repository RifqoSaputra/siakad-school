<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SIAKAD\SCHOOL\Pengumuman;
use App\Models\SIAKAD\SCHOOL\PengumumanAttachment;
use App\Models\SIAKAD\SCHOOL\PengumumanUser;
use App\Models\SIAKAD\SCHOOL\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AdminAnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $this->backfillSentAnnouncements();
        $this->finalizeDueAnnouncements();

        $dateRange = $request->query('date_range', 'any');
        $target = $request->query('target_role', 'any');
        $status = $request->query('status', 'any');

        $primaryKey = Pengumuman::primaryKeyColumn();
        $baseColumns = [
            'judul',
            'isi_pengumuman',
            'target_role',
            'status',
            'scheduled_at',
            'sent_at',
            'created_at',
        ];
        $selectColumns = $primaryKey ? array_merge([$primaryKey], $baseColumns) : ['*'];
        $hasNewAttachmentCols = Pengumuman::usesNewAttachmentColumns();

        $pengumuman = Pengumuman::select($selectColumns)
            ->with(['attachments' => function ($q) use ($hasNewAttachmentCols) {
                $q->select($hasNewAttachmentCols ? [
                    'id',
                    'pengumuman_id',
                    'file_name',
                    'file_path',
                    'file_size',
                    'file_mime',
                ] : [
                    'id',
                    'pengumuman_id',
                    'nama_file',
                    'path',
                    'size',
                    'mime_type',
                ]);
            }])
            ->filterDateRange($dateRange)
            ->filterTargetRole($target)
            ->filterStatus($status)
            ->orderByRaw("CASE WHEN status = 'draft' THEN 0 WHEN status = 'scheduled' THEN 1 ELSE 2 END")
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.admin.pengumuman.index', compact('pengumuman', 'dateRange', 'target', 'status'));
    }

    public function show(Pengumuman $pengumuman)
    {
        $pengumuman->load('attachments', 'admin');
        return response()->json([
            'id' => $pengumuman->getKey(),
            'judul' => $pengumuman->judul,
            'isi_pengumuman' => nl2br(e($pengumuman->isi_pengumuman)),
            'target_label' => $pengumuman->targetLabel(),
            'status_label' => $pengumuman->statusLabel(),
            'status' => $pengumuman->status,
            'meta_date' => $pengumuman->metaDateLong(),
            'attachments' => $pengumuman->attachments->map(function ($att) {
                $mime = $att->file_mime ?? $att->mime_type ?? '';
                $icon = str_starts_with($mime, 'image') ? 'image' : 'picture_as_pdf';
                $label = $att->file_name ?? $att->nama_file ?? basename($att->file_path ?? $att->path ?? 'Lampiran');
                return [
                    'icon' => $icon,
                    'label' => $label,
                    'size' => $att->file_size ?? $att->size,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);
        $status = $request->input('action_status', 'draft');

        $payload = [
            'judul' => $data['judul'],
            'isi_pengumuman' => $data['isi_pengumuman'],
            'target_role' => $data['target_role'],
            'status' => $status,
            'scheduled_at' => !empty($data['scheduled_at']) ? Carbon::parse($data['scheduled_at']) : null,
            'sent_at' => $status === 'sent' ? now() : null,
            'created_by' => Auth::user()->users_id,
        ];

        $announcement = Pengumuman::create($payload);
        $this->handleAttachments($request, $announcement);
        $this->syncRecipients($announcement);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $data = $this->validatePayload($request);
        $status = $request->input('action_status', $pengumuman->status);
        $wasSent = $pengumuman->status === 'sent';

        $payload = [
            'judul' => $data['judul'],
            'isi_pengumuman' => $data['isi_pengumuman'],
            'target_role' => $data['target_role'],
            'status' => $status,
            'scheduled_at' => !empty($data['scheduled_at']) ? Carbon::parse($data['scheduled_at']) : null,
            'sent_at' => $status === 'sent' ? now() : $pengumuman->sent_at,
        ];

        if (!$pengumuman->created_by) {
            $payload['created_by'] = Auth::user()->users_id;
        }

        $pengumuman->update($payload);
        $this->removeAttachments($request, $pengumuman);
        $this->handleAttachments($request, $pengumuman);
        if ($pengumuman->status === 'sent' && !$wasSent) {
            $this->syncRecipients($pengumuman);
        }

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        if (!in_array($pengumuman->status, ['draft', 'scheduled'])) {
            return redirect()->route('admin.pengumuman.index')->with('error', 'Hanya draf atau jadwal yang dapat dihapus.');
        }

        $pengumuman->attachments->each(function ($att) {
            if ($att->file_path) {
                Storage::disk('public')->delete($att->file_path);
            }
            $att->delete();
        });

        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman dihapus.');
    }

    private function validatePayload(Request $request): array
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'isi_pengumuman' => ['required', 'string', 'max:2000'],
            'target_role' => ['required', Rule::in(['guru', 'ortu', 'all'])],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
            'attachments.*' => ['nullable', 'file', 'max:10240'],
        ], [], [
            'judul' => 'Judul',
            'isi_pengumuman' => 'Isi Pengumuman',
            'target_role' => 'Penerima',
            'scheduled_at' => 'Jadwal',
        ]);

        // Normalisasi target 'all' -> 'semua' agar sesuai enum DB
        if (($data['target_role'] ?? '') === 'all') {
            $data['target_role'] = 'semua';
        }

        return $data;
    }

    private function removeAttachments(Request $request, Pengumuman $announcement): void
    {
        $ids = (array) $request->input('remove_attachments', []);
        if (empty($ids)) return;

        $attachmentQuery = $announcement->attachments()->whereIn('id', $ids);
        $attachmentQuery->each(function ($att) {
            $path = $att->file_path ?? $att->path;
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            $att->delete();
        });
    }

    private function handleAttachments(Request $request, Pengumuman $announcement): void
    {
        $files = $request->file('attachments');
        if (!$files) {
            return;
        }
        if (!is_array($files)) {
            $files = [$files];
        }

        $legacy = Schema::hasColumn('pengumuman_attachments', 'nama_file');
        $announcementId = $announcement->getAttribute('id_pengumuman')
            ?? $announcement->getAttribute('id')
            ?? $announcement->getKey();

        foreach ($files as $file) {
            $path = $file->store('pengumuman', 'public');
            $payload = [
                'pengumuman_id' => $announcementId,
            ];

            if ($legacy) {
                $payload['nama_file'] = $file->getClientOriginalName();
                $payload['path'] = $path;
                $payload['size'] = $file->getSize();
                $payload['mime_type'] = $file->getMimeType();
            } else {
                $payload['file_name'] = $file->getClientOriginalName();
                $payload['file_path'] = $path;
                $payload['file_size'] = $file->getSize();
                $payload['file_mime'] = $file->getMimeType();
            }

            PengumumanAttachment::create($payload);
        }
    }

    private function finalizeDueAnnouncements(): void
    {
        $now = Carbon::now();
        $dueAnnouncements = Pengumuman::where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $now)
            ->get();

        foreach ($dueAnnouncements as $announcement) {
            $announcement->update([
                'status' => 'sent',
                'sent_at' => $now,
                'updated_at' => $now,
            ]);
            $this->syncRecipients($announcement);
        }
    }

    /**
     * Buat entri pengumuman_user untuk penerima sesuai target role.
     */
    private function syncRecipients(Pengumuman $announcement): void
    {
        if ($announcement->status !== 'sent') {
            return;
        }

        $target = $announcement->target_role === 'all' ? 'semua' : $announcement->target_role;
        $roleMap = match ($target) {
            'guru' => ['Guru'],
            'ortu' => ['Orang Tua'],
            'semua', 'all' => ['Guru', 'Orang Tua'],
            default => ['Guru', 'Orang Tua'],
        };

        $userIds = User::whereHas('roles', function ($q) use ($roleMap) {
            $q->whereIn('nama_role', $roleMap);
        })->pluck('users_id')->all();

        if (empty($userIds)) {
            return;
        }

        $announcementId = $announcement->getAttribute(Pengumuman::primaryKeyColumn() ?: $announcement->getKeyName()) ?? $announcement->getKey();

        $existing = PengumumanUser::where('pengumuman_id', $announcementId)->pluck('users_id')->all();
        $now = now();
        $newRows = [];

        foreach ($userIds as $uid) {
            if (in_array($uid, $existing, true)) {
                continue;
            }
            $newRows[] = [
                'pengumuman_id' => $announcementId,
                'users_id' => $uid,
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($newRows)) {
            PengumumanUser::insert($newRows);
        }
    }

    /**
     * Pastikan pengumuman berstatus sent sudah memiliki entri penerima.
     */
    private function backfillSentAnnouncements(): void
    {
        $primaryKey = Pengumuman::primaryKeyColumn() ?: 'id_pengumuman';
        $annIdsWithRecipients = PengumumanUser::pluck('pengumuman_id')->unique()->all();

        $missingAnnouncements = Pengumuman::where('status', 'sent')
            ->when(!empty($annIdsWithRecipients), function ($q) use ($primaryKey, $annIdsWithRecipients) {
                $q->whereNotIn($primaryKey, $annIdsWithRecipients);
            })
            ->get();

        foreach ($missingAnnouncements as $announcement) {
            $this->syncRecipients($announcement);
        }
    }
}
