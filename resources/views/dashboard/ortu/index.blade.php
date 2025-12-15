@extends('layouts.template')

@section('title', 'Dashboard Orang Tua')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    <div class="p-6 md:p-10 bg-gray-50 min-h-screen">
        <div class="container mx-auto">

            {{-- HEADER --}}
            <h1 class="text-3xl font-extrabold text-indigo-800 mb-6 border-b-4 border-indigo-200 pb-2">
                <i class="fas fa-user-friends mr-3 text-indigo-500"></i>
                Dashboard Orang Tua
            </h1>

            {{-- INFORMASI SISWA --}}
            <div class="bg-white p-6 rounded-xl shadow-md mb-6">
                <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <i class="fas fa-user-graduate text-indigo-500"></i>
                    Informasi Siswa
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-indigo-50 p-4 rounded-lg border">
                        <p class="text-sm text-gray-600">Nama Siswa</p>
                        <p class="font-bold text-indigo-700">Ahmad Fauzan</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border">
                        <p class="text-sm text-gray-600">NIS</p>
                        <p class="font-bold text-green-700">20231234</p>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg border">
                        <p class="text-sm text-gray-600">Kelas</p>
                        <p class="font-bold text-yellow-700">10 DKV-1</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg border">
                        <p class="text-sm text-gray-600">Wali Kelas</p>
                        <p class="font-bold text-purple-700">Bu Rina Puspita</p>
                    </div>
                </div>
            </div>

            {{-- HISTOGRAM --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                {{-- HISTOGRAM KEHADIRAN --}}
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="font-bold flex items-center gap-2">
                            <i class="fas fa-calendar-check text-green-500"></i>
                            Kehadiran Semester Ini
                        </h2>
                        <button onclick="toggleKehadiran()"
                            class="text-sm px-3 py-1 rounded bg-indigo-100 text-indigo-700 hover:bg-indigo-200">
                            Toggle
                        </button>
                    </div>

                    @php
                        $kehadiran = [
                            'Hadir' => ['jumlah' => 34, 'persen' => 94, 'warna' => 'bg-green-500'],
                            'Sakit' => ['jumlah' => 1, 'persen' => 3, 'warna' => 'bg-yellow-500'],
                            'Izin' => ['jumlah' => 1, 'persen' => 3, 'warna' => 'bg-blue-500'],
                            'Tidak Hadir' => ['jumlah' => 0, 'persen' => 0, 'warna' => 'bg-red-500'],
                        ];
                    @endphp

                    <div class="space-y-3" id="kehadiran-container" data-mode="jumlah">
                        @foreach ($kehadiran as $label => $data)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span>{{ $label }}</span>
                                    <span class="nilai">{{ $data['jumlah'] }} hari</span>
                                </div>
                                <div class="w-full bg-gray-200 h-3 rounded-full">
                                    <div class="{{ $data['warna'] }} h-3 rounded-full"
                                        style="width: {{ $data['persen'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- HISTOGRAM NILAI --}}
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h2 class="font-bold mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-bar text-indigo-500"></i>
                        Performa Nilai per Semester
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <p class="text-sm mb-1">Semester Ganjil</p>
                            <div class="w-full bg-gray-200 h-4 rounded-full">
                                <div class="bg-indigo-600 h-4 rounded-full" style="width: 82.5%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Rata-rata: 82.5</p>
                        </div>

                        <div>
                            <p class="text-sm mb-1">Semester Genap</p>
                            <div class="w-full bg-gray-200 h-4 rounded-full">
                                <div class="bg-gray-400 h-4 rounded-full" style="width: 0%"></div>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Belum tersedia</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KEHADIRAN TERBARU & PENGUMUMAN --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- KEHADIRAN TERBARU --}}
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h2 class="font-bold mb-4 flex items-center gap-2">
                        <i class="fas fa-clock text-green-500"></i>
                        Kehadiran Terbaru
                    </h2>

                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left">Tanggal</th>
                                <th class="px-3 py-2 text-left">Status</th>
                                <th class="px-3 py-2 text-left">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t">
                                <td class="px-3 py-2">14 Des 2025</td>
                                <td class="px-3 py-2 text-green-600 font-semibold">Hadir</td>
                                <td class="px-3 py-2">-</td>
                            </tr>
                            <tr class="border-t">
                                <td class="px-3 py-2">13 Des 2025</td>
                                <td class="px-3 py-2 text-yellow-600 font-semibold">Sakit</td>
                                <td class="px-3 py-2">Demam</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- PENGUMUMAN --}}
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h2 class="font-bold mb-4 flex items-center gap-2">
                        <i class="fas fa-bullhorn text-orange-500"></i>
                        Pengumuman Sekolah
                    </h2>

                    <ul class="space-y-4">
                        <li class="border-l-4 border-indigo-500 pl-4">
                            <div class="flex justify-between">
                                <p class="font-semibold">Pembagian Rapor Semester Ganjil</p>
                                <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-700">
                                    Belum Dibaca
                                </span>
                            </div>
                            <p class="text-sm text-gray-500">6 hari lalu</p>
                        </li>

                        <li class="border-l-4 border-green-500 pl-4">
                            <div class="flex justify-between">
                                <p class="font-semibold">Libur Akhir Semester</p>
                                <span class="text-xs px-2 py-1 rounded bg-gray-200 text-gray-600">
                                    Sudah Dibaca
                                </span>
                            </div>
                            <p class="text-sm text-gray-500">2 minggu lalu</p>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    {{-- TOGGLE SCRIPT --}}
    <script>
        function toggleKehadiran() {
            const container = document.getElementById('kehadiran-container');
            const mode = container.dataset.mode;
            const items = @json($kehadiran);

            let i = 0;
            container.querySelectorAll('.nilai').forEach(el => {
                const key = Object.keys(items)[i];
                el.innerText = mode === 'jumlah' ?
                    items[key].persen + '%' :
                    items[key].jumlah + ' hari';
                i++;
            });

            container.dataset.mode = mode === 'jumlah' ? 'persen' : 'jumlah';
        }
    </script>
@endsection
