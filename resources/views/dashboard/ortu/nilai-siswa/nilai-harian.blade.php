@extends('layouts.template')

@section('title', 'Nilai Harian Anak')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- 1. LOAD COMPONENT LOADING --}}
    <div id="global-loading" class="hidden fixed inset-0 z-[100]">
        @include('components.loading')
    </div>

    {{-- ===================== MODAL DETAIL ===================== --}}
    <div id="modal-detail"
        class="fixed inset-0 bg-gray-900 bg-opacity-60 hidden items-center justify-center z-50 p-4 transition-opacity duration-300 opacity-0"
        aria-modal="true" role="dialog">

        {{-- Container Modal --}}
        {{-- Opsional: Saya juga perlebar modal sedikit ke max-w-5xl agar lebih proporsional --}}
        <div id="modal-panel"
            class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 p-6 md:p-8">

            {{-- HEADER MODAL --}}
            <div class="flex justify-between items-start border-b border-gray-200 pb-4 mb-4">
                <div>
                    <h3 class="text-2xl font-bold text-indigo-800" id="detail-mapel-title">Detail Nilai</h3>
                    <p class="text-sm text-gray-500 mt-1">Rekapitulasi perolehan nilai harian siswa.</p>
                </div>
                <button onclick="closeModal()"
                    class="text-gray-400 hover:text-red-500 transition duration-150 rounded-lg p-1 hover:bg-gray-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            {{-- INFO METADATA --}}
            <div class="bg-indigo-50 rounded-xl p-4 mb-6 border border-indigo-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex">
                        <span class="font-semibold text-indigo-800 w-32">Mata Pelajaran</span>
                        <span class="mr-2">:</span>
                        <span id="info-mapel-name" class="font-medium text-gray-700">-</span>
                    </div>
                    <div class="flex">
                        <span class="font-semibold text-indigo-800 w-32">Guru Pengampu</span>
                        <span class="mr-2">:</span>
                        <span id="info-guru" class="font-medium text-gray-700">-</span>
                    </div>
                    <div class="flex">
                        <span class="font-semibold text-indigo-800 w-32">Kelas</span>
                        <span class="mr-2">:</span>
                        <span id="info-kelas" class="font-medium text-gray-700">-</span>
                    </div>
                    <div class="flex">
                        <span class="font-semibold text-indigo-800 w-32">Semester</span>
                        <span class="mr-2">:</span>
                        <span id="info-semester" class="font-medium text-gray-700">-</span>
                    </div>
                </div>
            </div>

            {{-- DATA NILAI DETAIL --}}
            <div id="modal-content-data" class="overflow-x-auto min-h-[150px]">
                {{-- Loading State Default --}}
                <div class="flex flex-col items-center justify-center py-10 space-y-3">
                    <svg class="animate-spin h-10 w-10 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <p class="text-gray-500 font-medium">Mengambil data nilai...</p>
                </div>
            </div>

            {{-- Footer Button Close --}}
            <div class="mt-6 flex justify-end">
                <button onclick="closeModal()"
                    class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg font-medium hover:bg-gray-300 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    {{-- ===================== END MODAL ===================== --}}

    <div class="p-6 md:p-10 bg-gray-50 min-h-screen">
        {{-- 
            PERUBAHAN DISINI: 
            Saya ganti 'max-w-6xl' menjadi 'max-w-screen-2xl' (Extra Wide). 
            Ini akan membuat layout melebar hampir memenuhi layar standar laptop/PC,
            tapi tetap rapi di tengah. 
        --}}
        <div class="container mx-auto max-w-screen-2xl">

            <h1 class="text-3xl font-extrabold text-indigo-900 mb-8 border-l-8 border-indigo-500 pl-4">
                Rekap Nilai Harian
            </h1>

            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r shadow-sm mb-6 flex items-start">
                    <i class="fas fa-exclamation-circle mt-1 mr-3"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- ================= FILTER KELAS + SEMESTER ================= --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-3">
                <form id="filter-form" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">

                    {{-- Dropdown kelas --}}
                    <div class="md:col-span-5">
                        <label for="kelas_id" class="text-sm font-semibold text-gray-700 mb-2 block">Pilih Kelas</label>
                        <select name="kelas_id" id="kelas_id"
                            class="border border-gray-300 px-4 py-3 rounded-xl w-full focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 transition bg-gray-50">
                            @foreach ($kelasList as $k)
                                @php
                                    $k_full_name =
                                        ($k->kelas->tingkat_kelas ?? '') . ' ' . ($k->kelas->nama_kelas ?? '');
                                @endphp
                                <option value="{{ $k->kelas_id }}" {{ $kelasId == $k->kelas_id ? 'selected' : '' }}>
                                    {{ $k_full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Semester --}}
                    <div class="md:col-span-4">
                        <label for="semester" class="text-sm font-semibold text-gray-700 mb-2 block">Pilih Semester</label>
                        <select name="semester" id="semester"
                            class="border border-gray-300 px-4 py-3 rounded-xl w-full focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 transition bg-gray-50">
                            <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>

                    {{-- Button Terapkan --}}
                    <div class="md:col-span-3">
                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-indigo-200 transition duration-300 flex items-center justify-center gap-2">
                            <i class="fas fa-filter"></i> Terapkan
                        </button>
                    </div>
                </form>
            </div>

            {{-- ===================== TABEL REKAP ===================== --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-800">
                        Rekap Nilai Harian
                    </h3>
                    <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full">
                        {{ $semester }}
                    </span>
                </div>

                @if (empty($rekap) || count($rekap) == 0)
                    <div class="text-center py-16 px-6">
                        <div class="bg-indigo-50 inline-block p-4 rounded-full mb-4">
                            <i class="fas fa-folder-open text-3xl text-indigo-400"></i>
                        </div>
                        <h4 class="text-gray-800 font-bold text-lg mb-1">Data Belum Tersedia</h4>
                        <p class="text-gray-500">Tidak ada rekap nilai ditemukan untuk filter ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 text-gray-500 font-medium">
                                <tr>
                                    <th class="px-6 py-4 text-center text-xs uppercase tracking-wider w-16">No</th>
                                    <th class="px-6 py-4 text-left text-xs uppercase tracking-wider">Mata Pelajaran</th>
                                    <th class="px-6 py-4 text-left text-xs uppercase tracking-wider hidden md:table-cell">
                                        Guru</th>
                                    <th class="px-6 py-4 text-center text-xs uppercase tracking-wider">Rata-rata</th>
                                    <th class="px-6 py-4 text-center text-xs uppercase tracking-wider w-32">Detail</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($rekap as $i => $m)
                                    @php
                                        $rata = $m['rata'];
                                        // Logic warna badge nilai
                                        $badgeClass = 'bg-gray-100 text-gray-500';
                                        if (!is_null($rata)) {
                                            if ($rata < 70) {
                                                $badgeClass = 'bg-red-100 text-red-700';
                                            } elseif ($rata < 80) {
                                                $badgeClass = 'bg-yellow-100 text-yellow-800';
                                            } else {
                                                $badgeClass = 'bg-green-100 text-green-700';
                                            }
                                        }
                                    @endphp
                                    <tr class="hover:bg-indigo-50/50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            {{ $i + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">{{ $m['mapel'] }}</div>
                                            <div class="text-xs text-gray-500 md:hidden">{{ $m['guru'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 hidden md:table-cell">
                                            {{ $m['guru'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span
                                                class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-lg {{ $badgeClass }}">
                                                {{ is_null($rata) ? '-' : $rata }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <button
                                                onclick="showDetail('{{ $m['mapel_id'] }}', '{{ addslashes($m['mapel']) }}')"
                                                class="text-indigo-600 hover:text-indigo-900 font-medium text-sm border border-indigo-200 hover:bg-indigo-50 px-3 py-1.5 rounded-lg transition">
                                                Lihat
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ===================== SCRIPT JAVASCRIPT ===================== --}}
    <script>
        // 1. Script Loading Component saat Form Submit
        document.getElementById('filter-form').addEventListener('submit', function() {
            // Tampilkan komponen loading
            document.getElementById('global-loading').classList.remove('hidden');
        });

        // 2. Fungsi Helper Warna Nilai
        function getNilaiClass(nilai) {
            if (nilai === null || nilai === undefined || nilai === '-') return "text-gray-400";
            nilai = Number(nilai);
            if (nilai < 70) return "text-red-600 font-bold";
            if (nilai < 80) return "text-yellow-600 font-bold";
            return "text-green-600 font-bold";
        }

        // 3. Fungsi Buka Modal (Dengan Animasi & Reset Loading)
        function showDetail(mapelId, mapelName) {
            const modal = document.getElementById('modal-detail');
            const panel = document.getElementById('modal-panel');
            const contentDataEl = document.getElementById('modal-content-data');

            // Set Judul dulu
            document.getElementById('detail-mapel-title').innerText = mapelName;
            document.getElementById('info-mapel-name').innerText = mapelName;
            document.getElementById('info-guru').innerText = "Memuat...";
            document.getElementById('info-semester').innerText = "{{ $semester }}";

            // Reset konten modal ke state loading
            contentDataEl.innerHTML = `
                <div class="flex flex-col items-center justify-center py-12 space-y-3">
                    <svg class="animate-spin h-10 w-10 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-gray-500 font-medium">Sedang mengambil data...</p>
                </div>
            `;

            // Buka Modal (Hapus hidden dulu, lalu mainkan opacity)
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Sedikit delay agar CSS transition opacity terbaca browser
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                panel.classList.remove('scale-95');
                panel.classList.add('scale-100');
            }, 50);

            // Fetch Data
            const url =
                `{{ url('ortu/nilai-siswa/harian/detail') }}/${mapelId}?semester={{ $semester }}&kelas_id={{ $kelasId }}`;

            fetch(url)
                .then(res => res.json())
                .then(res => {
                    if (!res.success) throw new Error(res.msg);

                    // Update Metadata
                    const meta = res.meta || {};
                    document.getElementById('info-guru').innerText = meta.guru || "-";
                    document.getElementById('info-kelas').innerText = meta.kelas_full || "-";

                    // Cek jika data kosong
                    if (res.data.length === 0) {
                        contentDataEl.innerHTML = `
                            <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-600 font-medium">Belum ada nilai harian yang diinput untuk mata pelajaran ini.</p>
                            </div>
                        `;
                        return;
                    }

                    // Render Tabel Data
                    let rows = "";
                    let nilaiValues = [];

                    res.data.forEach(d => {
                        const nilaiTampil = d.nilai !== null ? d.nilai : '-';
                        if (d.nilai !== null) nilaiValues.push(Number(d.nilai));

                        rows += `
                            <tr class="hover:bg-gray-50 transition border-b border-gray-100 last:border-0">
                                <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-500 font-mono">${d.tgl}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-800">${d.jenis}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 italic max-w-xs truncate">${d.ket || '-'}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-center text-base ${getNilaiClass(d.nilai)}">
                                    ${nilaiTampil}
                                </td>
                            </tr>
                        `;
                    });

                    let rataRata = nilaiValues.length ? (nilaiValues.reduce((a, b) => a + b) / nilaiValues.length)
                        .toFixed(2) : '-';

                    contentDataEl.innerHTML = `
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-indigo-50 text-indigo-800">
                                <tr>
                                    <th class="px-4 py-3 text-center text-xs font-bold uppercase w-32">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase w-40">Jenis</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase">Keterangan</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold uppercase w-24">Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">${rows}</tbody>
                            <tfoot class="bg-gray-50 border-t border-gray-200">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right text-sm font-bold text-gray-700">Rata-rata:</td>
                                    <td class="px-4 py-3 text-center text-base font-extrabold ${getNilaiClass(rataRata)}">${rataRata}</td>
                                </tr>
                            </tfoot>
                        </table>
                    `;
                })
                .catch(err => {
                    console.error(err);
                    contentDataEl.innerHTML = `
                        <div class="bg-red-50 text-red-600 p-4 rounded-lg text-center">
                            <p class="font-bold">Gagal memuat data.</p>
                            <p class="text-sm mt-1">Silakan coba lagi nanti.</p>
                        </div>
                    `;
                });
        }

        // 4. Fungsi Tutup Modal (Dengan Animasi)
        function closeModal() {
            const modal = document.getElementById('modal-detail');
            const panel = document.getElementById('modal-panel');

            // Mulai animasi keluar (fade out)
            modal.classList.add('opacity-0');
            panel.classList.remove('scale-100');
            panel.classList.add('scale-95');

            // Tunggu animasi CSS selesai (300ms sesuai class duration-300), baru hidden
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }
    </script>
@endsection
