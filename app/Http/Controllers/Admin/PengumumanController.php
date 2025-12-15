<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SIAKAD\SCHOOL\Pengumuman;
use App\Models\SIAKAD\SCHOOL\PengumumanAttachment;
use App\Models\SIAKAD\SCHOOL\PengumumanUser;
use App\Models\SIAKAD\SCHOOL\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumuman = Pengumuman::with(['admin', 'attachments'])
            ->withCount('notifications')
            ->latest()
            ->paginate(15);

        return view('dashboard.admin.pengumuman.index', compact('pengumuman'));
    }

    public function create()
    {
        return view('dashboard.admin.pengumuman.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'isi_pengumuman' => ['nullable', 'string'],
            'target_role' => ['required', Rule::in(['admin', 'guru', 'ortu', 'all'])],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'lampiran.*' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif'],
        ]);

        $adminId = Auth::user()->users_id;

        DB::transaction(function () use ($data, $adminId, $request) {
            $pengumuman = Pengumuman::create([
                'judul' => $data['judul'],
                'isi_pengumuman' => $data['isi_pengumuman'] ?? null,
                'target_role' => $data['target_role'],
                'status' => $data['status'],
                'id_admin' => $adminId,
            ]);

            if ($pengumuman->status === 'published') {
                $targetUserIds = $this->getTargetUserIds($pengumuman->target_role);

                $now = now();
                $notificationPayload = collect($targetUserIds)->map(function ($userId) use ($pengumuman, $now) {
                    return [
                        'pengumuman_id' => $pengumuman->id_pengumuman,
                        'users_id' => $userId,
                        'is_read' => false,
                        'read_at' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                })->values()->all();

                if (!empty($notificationPayload)) {
                    PengumumanUser::insert($notificationPayload);
                }
            }

            // Simpan lampiran (bisa multi file)
            if ($request->hasFile('lampiran')) {
                foreach ($request->file('lampiran') as $file) {
                    $path = $file->store('pengumuman', 'public');
                    PengumumanAttachment::create([
                        'pengumuman_id' => $pengumuman->id_pengumuman,
                        'nama_file' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getClientMimeType(),
                    ]);
                }
            }
        });

        $message = $data['status'] === 'draft'
            ? 'Pengumuman disimpan sebagai draft.'
            : 'Pengumuman berhasil dibuat dan notifikasi dikirim.';

        return redirect()->route('admin.pengumuman.index')->with('success', $message);
    }

    public function edit($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        return view('dashboard.admin.pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'isi_pengumuman' => ['nullable', 'string'],
            'target_role' => ['required', Rule::in(['admin', 'guru', 'ortu', 'all'])],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'lampiran.*' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif'],
            'remove_attachments' => ['array'],
            'remove_attachments.*' => ['integer'],
        ]);

        DB::transaction(function () use ($data, $id, $request) {
            $pengumuman = Pengumuman::findOrFail($id);
            $previousStatus = $pengumuman->status;

            $pengumuman->update([
                'judul' => $data['judul'],
                'isi_pengumuman' => $data['isi_pengumuman'] ?? null,
                'target_role' => $data['target_role'],
                'status' => $data['status'],
            ]);

            if ($pengumuman->status === 'published') {
                $targetUserIds = $this->getTargetUserIds($pengumuman->target_role);

                $now = now();
                $payload = collect($targetUserIds)->map(function ($userId) use ($pengumuman, $now) {
                    return [
                        'pengumuman_id' => $pengumuman->id_pengumuman,
                        'users_id' => $userId,
                        'is_read' => false,
                        'read_at' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                })->all();

                if (!empty($payload)) {
                    PengumumanUser::upsert(
                        $payload,
                        ['pengumuman_id', 'users_id'],
                        ['is_read', 'read_at', 'updated_at']
                    );
                }

                // Hapus notifikasi user yang tidak lagi menjadi target
                if (!empty($targetUserIds)) {
                    PengumumanUser::where('pengumuman_id', $pengumuman->id_pengumuman)
                        ->whereNotIn('users_id', $targetUserIds)
                        ->delete();
                } else {
                    PengumumanUser::where('pengumuman_id', $pengumuman->id_pengumuman)->delete();
                }
            } else {
                // Status draft: bersihkan notifikasi agar tidak tampil
                PengumumanUser::where('pengumuman_id', $pengumuman->id_pengumuman)->delete();
            }

            // Hapus lampiran yang ditandai
            if (!empty($data['remove_attachments'])) {
                $attachments = $pengumuman->attachments()->whereIn('id', $data['remove_attachments'])->get();
                foreach ($attachments as $att) {
                    if ($att->path) {
                        Storage::disk('public')->delete($att->path);
                    }
                    $att->delete();
                }
            }

            if ($request->hasFile('lampiran')) {
                foreach ($request->file('lampiran') as $file) {
                    $path = $file->store('pengumuman', 'public');
                    PengumumanAttachment::create([
                        'pengumuman_id' => $pengumuman->id_pengumuman,
                        'nama_file' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getClientMimeType(),
                    ]);
                }
            }
        });

        $message = $data['status'] === 'draft'
            ? 'Pengumuman disimpan sebagai draft (notifikasi tidak dikirim).'
            : 'Pengumuman diperbarui dan notifikasi di-reset.';

        return redirect()->route('admin.pengumuman.index')->with('success', $message);
    }

    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index')->with('deleted', 'Pengumuman berhasil dihapus.');
    }

    /**
     * Ambil daftar users_id berdasarkan target role pengumuman.
     */
    private function getTargetUserIds(string $targetRole): array
    {
        $roleMapping = [
            'admin' => ['Admin'],
            'guru' => ['Guru'],
            'ortu' => ['Orang Tua'],
            'all' => ['Admin', 'Guru', 'Orang Tua'],
        ];

        $roles = $roleMapping[$targetRole] ?? [];

        return User::whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('nama_role', $roles);
        })->pluck('users_id')->toArray();
    }
}