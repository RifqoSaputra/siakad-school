@extends('layouts.template')

@section('title', 'Monitoring Nilai Harian Siswa')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>

    <div class="p-6 md:p-10 bg-gray-50 min-h-screen">
        <div class="container mx-auto">
            <h1 class="text-3xl font-extrabold text-indigo-800 mb-6 border-b-4 border-indigo-200 pb-2">
                <i class="fas fa-eye mr-3 text-indigo-500"></i> Monitoring Nilai Harian Siswa
            </h1>

            {{-- FILTER BOX --}}
            <div class="bg-white p-4 md:p-6 rounded-xl shadow-md mb-6">
                <form id="filter-form" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end" method="GET">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
                        <select name="tahun_ajaran" id="tahun_ajaran" class="mt-1 block w-full py-2 px-3 border rounded-md">
                            <option value="2024/2025" {{ $tahunAjaran == '2024/2025' ? 'selected' : '' }}>2024/2025</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                        <select name="semester" id="semester" class="mt-1 block w-full py-2 px-3 border rounded-md">
                            <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ $semester == 'Genap' : 'selected' : '' }}>Genap</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                        <select name="kelas_id" id="kelas_id" class="mt-1 block w-full py-2 px-3 border rounded-md">
                            <option value="">-- Pilih --</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->kelas_id }}"
                                    {{ (string) $kelasId === (string) $k->kelas_id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mapel</label>
                        <select name="mapel_id" id="mapel_id" class="mt-1 block w-full py-2 px-3 border rounded-md">
                            <option value="">-- Pilih --</option>
                            @foreach ($mapel as $m)
                                <option value="{{ $m->mapel_id }}"
                                    {{ (string) $mapelId === (string) $m->mapel_id ? 'selected' : '' }}>
                                    {{ $m->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <button type="submit" id="apply-filter"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow">
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>

            {{-- HEADER RINGKAS --}}
            @if (!empty($kelasId) && !empty($mapelId))
                <div class="bg-white p-4 rounded-xl shadow mb-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                        <div>
                            <div class="text-sm text-gray-600">Kelas</div>
                            <div class="font-semibold text-gray-800">
                                {{ optional($listTugas->first())->kelas->nama_kelas_lengkap ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-600">Mapel</div>
                            <div class="font-semibold text-gray-800">
                                {{ optional($listTugas->first())->mapel->nama_mapel ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-600">Guru</div>
                            <div class="font-semibold text-gray-800">
                                {{ optional($listTugas->first())->guruMapel->guru->nama_guru ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-600">Jumlah Tugas</div>
                            <div class="font-semibold text-gray-800">{{ $tasks->count() }}</div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-600">Status Keseluruhan</div>
                            <div class="font-semibold text-gray-800">{{ $overallStatus ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Jika tidak ada tugas untuk filter, tampilkan pesan --}}
            @if (!empty($kelasId) && !empty($mapelId) && !empty($noTasksForFilter))
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded mb-6">
                    <div class="text-sm text-yellow-900">
                        <strong>Tidak ada data nilai harian untuk kelas ini.</strong>
                        Guru belum membuat tugas/penilaian harian untuk filter yang dipilih.
                    </div>
                </div>
            @endif

            {{-- ONLY RENDER TABLE WHEN ADA TASKS --}}
            @if (empty($noTasksForFilter))
                <div class="overflow-x-auto" style="overflow: visible !important;">
                    <div class="relative overflow-visible">
                        <table class="min-w-full border border-gray-200 text-sm">
                            <thead class="bg-indigo-600 text-white text-sm">
                                <tr>
                                    <th class="px-4 py-2 border text-center" rowspan="2">NIS</th>
                                    <th class="px-4 py-2 border text-center" rowspan="2">Nama</th>

                                    @if ($tasks->count() > 0)
                                        <th class="px-2 py-1.5 border text-center" colspan="{{ $tasks->count() }}">
                                            Semua Penilaian Harian
                                        </th>
                                    @endif
                                    <th class="px-4 py-2 border text-center" rowspan="2">Rata-rata</th>
                                </tr>

                                <tr class="bg-indigo-500 text-white">
                                    @foreach ($tasks as $t)
                                        @php
                                            $fullName = $t->tipe_penunjang;
                                            $fullDate = $t->tgl_entry
                                                ? \Carbon\Carbon::parse($t->tgl_entry)->format('d/m/Y')
                                                : '-';
                                        @endphp

                                        <th class="px-3 py-2 border text-center tooltip-header cursor-pointer"
                                            data-title="{{ $fullName }}" data-date="{{ $fullDate }}">
                                            <div class="font-semibold text-sm">{{ $t->kode_ringkas }}</div>
                                            <div class="text-[11px] text-gray-200">
                                                {{ $t->tgl_entry ? \Carbon\Carbon::parse($t->tgl_entry)->format('d/m') : '-' }}
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody>
                                @if (count($rekap) === 0)
                                    <tr>
                                        <td colspan="{{ 3 + $tasks->count() }}"
                                            class="px-6 py-6 text-center text-gray-500">
                                            Silakan pilih filter dan tekan "Terapkan Filter".
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($rekap as $r)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-2 py-1.5 border">{{ $r['nis'] }}</td>
                                            <td class="px-2 py-1.5 border">{{ $r['nama'] }}</td>

                                            @foreach ($tasks as $t)
                                                @php $val = $r['nilai_per_tugas'][$t->id] ?? null; @endphp
                                                <td class="px-2 py-1.5 border text-center">
                                                    {{ is_null($val) ? '-' : $val }}
                                                </td>
                                            @endforeach

                                            <td class="px-2 py-1.5 border text-center font-semibold">
                                                {{ $r['rata_rata'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>

                        {{-- Global tooltip element --}}
                        <div id="tooltip-js"
                            class="fixed z-[9999] bg-white border border-gray-300 rounded-lg shadow-lg text-[12px] px-3 py-2 hidden pointer-events-none">
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- JS: mapel-by-kelas + tooltip --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kelasSelect = document.getElementById('kelas_id');
            const mapelSelect = document.getElementById('mapel_id');
            const tahunSelect = document.getElementById('tahun_ajaran');
            const semesterSelect = document.getElementById('semester');

            // When kelas changes, fetch mapel options via AJAX
            kelasSelect?.addEventListener('change', async function() {
                const kelasId = this.value;
                const tahun = tahunSelect?.value || '';
                const semester = semesterSelect?.value || '';

                // clear mapel first
                mapelSelect.innerHTML = '<option value="">-- Pilih --</option>';

                if (!kelasId) return;

                showLoading();
                try {
                    const resp = await fetch(
                        "{{ route('admin.nilai.harian') }}" +
                        "?kelas_id=" + encodeURIComponent(kelasId) +
                        "&tahun_ajaran=" + encodeURIComponent(tahun) +
                        "&semester=" + encodeURIComponent(semester), {
                            headers: {
                                "X-Requested-With": "XMLHttpRequest"
                            }
                        }
                    );
                    const data = await resp.json();
                    if (data.success && Array.isArray(data.mapel)) {
                        data.mapel.forEach(m => {
                            const opt = document.createElement('option');
                            opt.value = m.mapel_id;
                            opt.textContent = m.nama_mapel;
                            // keep selected if matches current query param
                            if (String(opt.value) === "{{ $mapelId ?? '' }}") {
                                opt.selected = true;
                            }
                            mapelSelect.appendChild(opt);
                        });
                    }
                } catch (err) {
                    console.error('Failed to fetch mapel:', err);
                } finally {
                    hideLoading();
                }
            });

            // Tooltip logic (follows mouse, works with overflow)
            const tooltip = document.getElementById('tooltip-js');
            document.querySelectorAll('.tooltip-header').forEach(el => {
                el.addEventListener('mouseenter', (e) => {
                    const title = el.getAttribute('data-title') || '';
                    const date = el.getAttribute('data-date') || '';
                    tooltip.innerHTML =
                        `<strong>${title}</strong><br><span class="text-gray-600">${date}</span>`;
                    tooltip.classList.remove('hidden');
                });
                el.addEventListener('mousemove', (e) => {
                    // offset to avoid cursor overlap
                    tooltip.style.left = (e.pageX + 12) + 'px';
                    tooltip.style.top = (e.pageY + 12) + 'px';
                });
                el.addEventListener('mouseleave', () => {
                    tooltip.classList.add('hidden');
                });
            });
        });
    </script>
@endsection
