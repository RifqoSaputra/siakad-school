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
                        <p class="font-bold text-indigo-700">{{ $siswa->nama }}</p>
                    </div>

                    <div class="bg-green-50 p-4 rounded-lg border">
                        <p class="text-sm text-gray-600">NIS</p>
                        <p class="font-bold text-green-700">{{ $siswa->nis }}</p>
                    </div>

                    <div class="bg-yellow-50 p-4 rounded-lg border">
                        <p class="text-sm text-gray-600">Kelas</p>
                        <p class="font-bold text-yellow-700">
                            {{ optional($kelasAktif)->kelas?->nama_kelas_lengkap ?? '-' }}
                        </p>
                    </div>

                    <div class="bg-purple-50 p-4 rounded-lg border">
                        <p class="text-sm text-gray-600">Wali Kelas</p>
                        <p class="font-bold text-purple-700">
                            {{ optional($kelasAktif)->kelas?->waliKelas?->nama_guru ?? '-' }}
                        </p>
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
                            @if ($rataNilai !== null)
                                <div class="w-full bg-gray-200 h-4 rounded-full">
                                    <div class="bg-indigo-600 h-4 rounded-full" style="width: {{ min(100, $rataNilai) }}%">
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    Rata-rata: {{ number_format($rataNilai, 2) }}
                                </p>
                            @else
                                <div class="w-full bg-gray-100 h-4 rounded-full"></div>
                                <p class="text-xs text-gray-400 mt-1 italic">
                                    Rapor belum diterbitkan
                                </p>
                            @endif
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
                            @forelse ($kehadiranTerbaru as $absen)
                                <tr class="border-t">
                                    <td class="px-3 py-2">
                                        {{ \Carbon\Carbon::parse($absen->waktu_absen)->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-3 py-2 font-semibold">
                                        {{ $absen->status }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $absen->keterangan ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-3 py-4 text-center text-gray-500">
                                        Tidak ada data kehadiran
                                    </td>
                                </tr>
                            @endforelse
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
                        @foreach ($pengumuman as $item)
                            <li class="border-l-4 {{ $item->is_read ? 'border-gray-400' : 'border-indigo-500' }} pl-4">
                                <div class="flex justify-between">
                                    <p class="font-semibold">{{ $item->judul }}</p>
                                    <span
                                        class="text-xs px-2 py-1 rounded
                                    {{ $item->is_read ? 'bg-gray-200 text-gray-600' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $item->is_read ? 'Sudah Dibaca' : 'Belum Dibaca' }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                </p>
                            </li>
                        @endforeach
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
