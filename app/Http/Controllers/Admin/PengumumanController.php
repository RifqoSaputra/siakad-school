<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Models\SIAKAD\SCHOOL\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $this->seedDemoData();

        $filters = [
            'kapan' => $request->query('kapan', 'all'),
            'target' => $request->query('target', 'semua'),
            'status' => $request->query('status', 'semua'),
        ];

        $query = Pengumuman::query()->with('creator');
        $filteredQuery = $this->applyFilters($query, $filters);

        $orderExpr = 'created_at';
        if (Schema::hasColumn('pengumuman', 'sent_at') && Schema::hasColumn('pengumuman', 'scheduled_at')) {
            $orderExpr = DB::raw('COALESCE(sent_at, scheduled_at, created_at)');
        } elseif (Schema::hasColumn('pengumuman', 'sent_at')) {
            $orderExpr = DB::raw('COALESCE(sent_at, created_at)');
        } elseif (Schema::hasColumn('pengumuman', 'scheduled_at')) {
            $orderExpr = DB::raw('COALESCE(scheduled_at, created_at)');
        }

        $pengumuman = $filteredQuery
            ->orderByDesc($orderExpr)
            ->paginate(10)
            ->withQueryString();

        $statusCounts = Pengumuman::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('dashboard.admin.pengumuman.index', [
            'pengumuman' => $pengumuman,
            'filters' => $filters,
            'statusCounts' => $statusCounts,
            'timeOptions' => $this->timeFilterOptions(),
        ]);
    }

    public function show(Pengumuman $pengumuman)
    {
        return response()->json([
            'id' => $pengumuman->id_pengumuman,
            'judul' => $pengumuman->judul,
            'isi_pengumuman' => $pengumuman->isi_pengumuman,
            'target_role' => $pengumuman->target_role,
            'status' => $pengumuman->status,
            'scheduled_at' => optional($pengumuman->scheduled_at)->toIso8601String(),
            'sent_at' => optional($pengumuman->sent_at)->toIso8601String(),
            'created_at' => optional($pengumuman->created_at)->toIso8601String(),
            'creator' => $pengumuman->creator?->nama_lengkap,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $payload = [
            'judul' => $data['judul'],
            'isi_pengumuman' => $data['isi_pengumuman'],
            'target_role' => $data['target_role'],
            'status' => $data['status'],
            'scheduled_at' => null,
            'sent_at' => null,
            'created_by' => Auth::user()->users_id ?? null,
        ];

        if ($data['status'] === 'dikirim') {
            $payload['sent_at'] = now();
        } elseif ($data['status'] === 'dijadwalkan') {
            $payload['scheduled_at'] = Carbon::parse($data['scheduled_at']);
        }

        $announcement = new Pengumuman($payload);
        $announcement->save();

        return redirect()
            ->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil disimpan.');
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $data = $this->validatedData($request);

        $payload = [
            'judul' => $data['judul'],
            'isi_pengumuman' => $data['isi_pengumuman'],
            'target_role' => $data['target_role'],
            'status' => $data['status'],
            'scheduled_at' => null,
            'sent_at' => null,
        ];

        if ($data['status'] === 'dikirim') {
            $payload['sent_at'] = now();
        } elseif ($data['status'] === 'dijadwalkan') {
            $payload['scheduled_at'] = Carbon::parse($data['scheduled_at']);
        }

        if (!$pengumuman->created_by) {
            $payload['created_by'] = Auth::user()->users_id ?? null;
        }

        $pengumuman->update($payload);

        return redirect()
            ->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman diperbarui.');
    }

    private function validatedData(Request $request): array
    {
        $rules = [
            'judul' => ['required', 'string', 'max:200'],
            'isi_pengumuman' => ['required', 'string', 'max:2000'],
            'target_role' => ['required', Rule::in(['guru', 'ortu', 'semua'])],
            'status' => ['required', Rule::in(['draft', 'dijadwalkan', 'dikirim'])],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
        ];

        if ($request->input('status') === 'dijadwalkan') {
            $rules['scheduled_at'][] = 'required';
        }

        return $request->validate($rules, [], [
            'judul' => 'Judul',
            'isi_pengumuman' => 'Isi Pengumuman',
            'target_role' => 'Target',
            'status' => 'Status',
            'scheduled_at' => 'Jadwal Kirim',
        ]);
    }

    private function applyFilters($query, array $filters)
    {
        if (!empty($filters['target']) && $filters['target'] !== 'semua') {
            $query->where('target_role', $filters['target']);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'semua') {
            $query->where('status', $filters['status']);
        }

        $timeMap = [
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            '6months' => now()->subMonths(6),
            'year' => now()->subYear(),
        ];

        if (!empty($filters['kapan']) && $filters['kapan'] !== 'all' && isset($timeMap[$filters['kapan']])) {
            $start = $timeMap[$filters['kapan']]->startOfDay();
            $query->whereRaw('COALESCE(sent_at, scheduled_at, created_at) >= ?', [$start]);
        }

        return $query;
    }

    private function seedDemoData(): void
    {
        if (Pengumuman::count() > 0) {
            return;
        }

        $faker = fake('id_ID');
        $creatorId = Auth::user()->users_id ?? User::value('users_id');
        $titles = [
            'Pengumuman Kegiatan Besar Sekolah dan Libur Bersama',
            'Pengumuman Rapat Koordinasi Guru Bulanan',
            'Pemberitahuan Pertemuan Orang Tua/Wali Siswa',
            'Pemberitahuan Pelatihan Peningkatan Kompetensi Guru',
            'Pengumuman Pembagian Jadwal Mengajar Semester Baru',
            'Informasi Penilaian Kinerja Guru (PKG) Tahunan',
            'Informasi Pembayaran dan Jadwal Ujian Tengah Semester',
            'Pengumuman Tugas Tambahan bagi Bapak/Ibu Guru',
            'Pengumuman Kegiatan Parenting dan Konsultasi Pendidikan',
            'Pembagian Rapor dan Evaluasi Semester',
        ];

        for ($i = 0; $i < 24; $i++) {
            $status = $faker->randomElement(['dikirim', 'dijadwalkan', 'draft', 'dikirim']);
            $target = $faker->randomElement(['guru', 'ortu', 'semua']);
            $scheduledAt = null;
            $sentAt = null;

            if ($status === 'dikirim') {
                $sentAt = now()
                    ->subDays($faker->numberBetween(0, 150))
                    ->setTime($faker->numberBetween(7, 16), $faker->numberBetween(0, 59));
            } elseif ($status === 'dijadwalkan') {
                $scheduledAt = now()
                    ->addDays($faker->numberBetween(2, 45))
                    ->setTime($faker->numberBetween(8, 15), $faker->numberBetween(0, 59));
            }

            $createdAt = ($sentAt ?? $scheduledAt ?? now()->subDays($faker->numberBetween(1, 180)))->clone();

            $announcement = new Pengumuman([
                'judul' => $faker->randomElement($titles),
                'isi_pengumuman' => $faker->paragraphs(3, true),
                'target_role' => $target,
                'status' => $status,
                'scheduled_at' => $scheduledAt,
                'sent_at' => $sentAt,
                'created_by' => $creatorId,
            ]);

            $announcement->created_at = $createdAt;
            $announcement->updated_at = $createdAt;
            $announcement->save();
        }
    }

    private function timeFilterOptions(): array
    {
        return [
            'all' => 'Kapan saja',
            'week' => 'Seminggu terakhir',
            'month' => 'Sebulan terakhir',
            '6months' => '6 bulan terakhir',
            'year' => 'Setahun terakhir',
        ];
    }
}
