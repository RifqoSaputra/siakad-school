@extends('layouts.template')

@section('title', 'Monitoring Nilai Ujian (PTS & PAS) Siswa')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>

    <div class="p-6 md:p-10 bg-gray-50 min-h-screen">
        <div class="container mx-auto">
            <h1 class="text-3xl font-extrabold text-indigo-800 mb-6 border-b-4 border-indigo-200 pb-2">
                <i class="fas fa-chart-line mr-3 text-indigo-500"></i> Monitoring Nilai Ujian (PTS/PAS) Siswa
            </h1>

            {{-- FILTER BOX --}}
            <div class="bg-white p-4 md:p-6 rounded-xl shadow-md mb-6">
                <form id="filter-form" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end" method="GET">
                    {{-- 1. Tahun Ajaran --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
                        <select name="tahun_ajaran" id="tahun_ajaran" class="mt-1 block w-full py-2 px-3 border rounded-md">
                            <option value="2024/2025" {{ $tahunAjaran == '2024/2025' ? 'selected' : '' }}>2024/2025</option>
                            {{-- Tambahkan tahun ajaran lain jika ada --}}
                        </select>
                    </div>

                    {{-- 2. Semester --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                        <select name="semester" id="semester" class="mt-1 block w-full py-2 px-3 border rounded-md">
                            <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>

                    {{-- 3. Tipe Ujian (BARU) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Ujian</label>
                        <select name="tipe_ujian" id="tipe_ujian" class="mt-1 block w-full py-2 px-3 border rounded-md">
                            <option value="">-- Pilih --</option>
                            <option value="PTS" {{ $tipeUjian == 'PTS' ? 'selected' : '' }}>PTS (Penilaian Tengah
                                Semester)</option>
                            <option value="PAS" {{ $tipeUjian == 'PAS' ? 'selected' : '' }}>PAS (Penilaian Akhir
                                Semester)</option>
                        </select>
                    </div>

                    {{-- 4. Kelas --}}
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

                    {{-- 5. Mapel --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mapel</label>
                        <select name="mapel_id" id="mapel_id" class="mt-1 block w-full py-2 px-3 border rounded-md">
                            <option value="">-- Pilih --</option>
                            {{-- Mapel akan diisi via AJAX atau saat load pertama jika kelas sudah dipilih --}}
                            @foreach ($mapel as $m)
                                <option value="{{ $m->mapel_id }}"
                                    {{ (string) $mapelId === (string) $m->mapel_id ? 'selected' : '' }}>
                                    {{ $m->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 6. Tombol Filter --}}
                    <div>
                        <button type="submit" id="apply-filter"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow w-full">
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>

            {{-- HEADER RINGKAS --}}
            @if ($ujian)
                {{-- Tampilkan jika data ujian ditemukan --}}
                <div class="bg-white p-4 rounded-xl shadow mb-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div>
                            <div class="text-sm text-gray-600">Kelas</div>
                            <div class="font-semibold text-gray-800">
                                {{ optional($ujian->kelas)->nama_kelas_lengkap ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-600">Mapel</div>
                            <div class="font-semibold text-gray-800">
                                {{ optional($ujian->mapel)->nama_mapel ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-600">Guru</div>
                            <div class="font-semibold text-gray-800">
                                {{ optional($ujian->guruMapel->guru)->nama_guru ?? '-' }}</div>
                        </div>

                        {{-- PERUBAHAN: KOLOM TIPE UJIAN DIPISAH --}}
                        <div>
                            <div class="text-sm text-gray-600">Tipe Ujian</div>
                            <div class="font-semibold text-gray-800">{{ $ujian->tipe_ujian }}</div>
                        </div>

                        {{-- PERUBAHAN: KOLOM STATUS DIPISAH --}}
                        <div>
                            <div class="text-sm text-gray-600">Status</div>
                            <div class="font-semibold text-gray-800">{{ $ujian->status }}</div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Pesan Peringatan --}}
            @if (!empty($kelasId) && !empty($mapelId) && !empty($tipeUjian) && $noUjianForFilter)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded mb-6">
                    <div class="text-sm text-yellow-900">
                        <strong>Tidak ada data ujian {{ $tipeUjian }} untuk filter yang dipilih.</strong>
                        Guru mungkin belum membuat entri ujian ini.
                    </div>
                </div>
            @elseif (empty($kelasId) || empty($mapelId) || empty($tipeUjian))
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded mb-6">
                    <div class="text-sm text-blue-900">
                        Silakan pilih **Kelas, Mata Pelajaran, dan Tipe Ujian** di atas, lalu klik "Terapkan Filter"
                        untuk menampilkan data.
                    </div>
                </div>
            @endif

            {{-- TABEL NILAI UJIAN --}}
            @if ($ujian && !$noUjianForFilter)
                <div class="overflow-x-auto">
                    <div class="relative">
                        <table class="min-w-full border border-gray-200 text-sm">
                            <thead class="bg-indigo-600 text-white text-sm">
                                <tr>
                                    <th class="px-4 py-3 border text-center w-1/5">NIS</th>
                                    <th class="px-4 py-3 border text-left w-3/5">Nama Siswa</th>
                                    {{-- PERBAIKAN: KOLOM TIPE UJIAN DIPISAH --}}
                                    <th class="px-4 py-3 border text-center w-1/5">Nilai {{ $ujian->tipe_ujian }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($rekap as $r)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 border text-center">{{ $r['nis'] }}</td>
                                        <td class="px-4 py-2 border">{{ $r['nama'] }}</td>
                                        <td class="px-4 py-2 border text-center font-semibold text-lg">
                                            @if (is_null($r['nilai']))
                                                <span class="text-red-500">-</span>
                                            @else
                                                {{ $r['nilai'] }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- JS: mapel-by-kelas (Hanya perlu untuk filter, tidak ada tooltip header seperti harian) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kelasSelect = document.getElementById('kelas_id');
            const mapelSelect = document.getElementById('mapel_id');
            const tahunSelect = document.getElementById('tahun_ajaran');
            const semesterSelect = document.getElementById('semester');
            const tipeUjianSelect = document.getElementById('tipe_ujian');

            // Event listener untuk memuat mapel saat Kelas, Tahun, atau Semester berubah
            const updateMapelOptions = async () => {
                const kelasId = kelasSelect?.value || '';
                const tahun = tahunSelect?.value || '';
                const semester = semesterSelect?.value || '';
                const currentMapelId = mapelSelect?.value;

                // clear mapel first
                mapelSelect.innerHTML = '<option value="">-- Pilih --</option>';

                if (!kelasId) return;

                // Tampilkan indikator loading (asumsi fungsi showLoading/hideLoading ada)
                if (typeof showLoading === 'function') showLoading();

                try {
                    // Perluas route admin.nilai.ujian dengan parameter query
                    const url = "{{ route('admin.nilai.ujian') }}" +
                        "?kelas_id=" + encodeURIComponent(kelasId) +
                        "&tahun_ajaran=" + encodeURIComponent(tahun) +
                        "&semester=" + encodeURIComponent(semester);

                    const resp = await fetch(url, {
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        }
                    });
                    const data = await resp.json();

                    if (data.success && Array.isArray(data.mapel)) {
                        data.mapel.forEach(m => {
                            const opt = document.createElement('option');
                            opt.value = m.mapel_id;
                            opt.textContent = m.nama_mapel;

                            // Pertahankan selection
                            if (String(opt.value) === String(currentMapelId)) {
                                opt.selected = true;
                            }
                            mapelSelect.appendChild(opt);
                        });
                    }
                } catch (err) {
                    console.error('Failed to fetch mapel:', err);
                } finally {
                    if (typeof hideLoading === 'function') hideLoading();
                }
            };

            kelasSelect?.addEventListener('change', updateMapelOptions);
            tahunSelect?.addEventListener('change', updateMapelOptions);
            semesterSelect?.addEventListener('change', updateMapelOptions);
            tipeUjianSelect?.addEventListener('change', updateMapelOptions);
            
            // Panggil saat DOMContentLoaded jika kelasId sudah ada di URL (untuk mempertahankan filter setelah submit)
            if (kelasSelect?.value) {
                // Panggil sekali saat load untuk memastikan mapel terisi jika ada kelasId di URL
                updateMapelOptions();
            }
        });

        // Asumsi fungsi showLoading() dan hideLoading() tersedia di layout utama atau di sini
        function showLoading() {
            /* Implementasi loading spinner */
        }

        function hideLoading() {
            /* Implementasi hide loading spinner */
        }
    </script>
@endsection
