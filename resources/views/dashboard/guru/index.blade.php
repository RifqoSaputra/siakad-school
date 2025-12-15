@extends('layouts.template')

@section('title', 'Dashboard Guru')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    <div class="p-6 md:p-10 bg-gray-50 min-h-screen">
        <div class="container mx-auto">

            {{-- HEADER --}}
            <h1 class="text-3xl font-extrabold text-indigo-800 mb-6 border-b-4 border-indigo-200 pb-2">
                <i class="fas fa-chalkboard-teacher mr-3 text-indigo-500"></i>
                Dashboard Guru
            </h1>

            {{-- BOX INFO GURU --}}
            <div class="bg-white p-6 rounded-xl shadow-md mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-indigo-500"></i> Informasi Guru
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                        <p class="text-sm text-gray-600">Nama Guru</p>
                        <p class="text-lg font-bold text-indigo-700">
                            {{ $guru->nama_guru }}
                        </p>
                    </div>

                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <p class="text-sm text-gray-600">Wali Kelas</p>
                        <p class="text-lg font-bold text-green-700">
                            {{ $waliKelas->nama_kelas_lengkap ?? '-' }}
                        </p>
                    </div>

                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <p class="text-sm text-gray-600">Total Mapel Diampu</p>
                        <p class="text-lg font-bold text-yellow-700">
                            {{ $totalMapel }} Mapel
                        </p>
                    </div>

                </div>
            </div>

            {{-- JADWAL MENGAJAR HARI INI --}}
            <div class="bg-white p-6 rounded-xl shadow-md mb-6">

                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-blue-500"></i>
                        Jadwal Mengajar Hari Ini
                    </h2>
                    <span class="text-sm text-gray-500">
                        {{ $tanggalHariIni }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">
                                    Jam
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">
                                    Kelas
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">
                                    Mata Pelajaran
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($jadwalHariIni as $jadwal)
                                <tr>
                                    <td class="px-6 py-4">
                                        {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $jadwal->kelas->nama_kelas_lengkap }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $jadwal->penugasan->mapel->nama_mapel }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada jadwal hari ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- TABEL NILAI HARIAN --}}
                <div class="bg-white p-6 rounded-xl shadow-md">

                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-tasks text-purple-500"></i>
                            Penilaian Harian Terbaru
                        </h2>

                        <a href="{{ route('nilai.harian.index') }}"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 text-sm">
                            Lihat Semua
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-indigo-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Mapel</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Kelas</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($nilaiHarian as $item)
                                    <tr>
                                        <td class="px-6 py-4">
                                            {{ \Carbon\Carbon::parse($item->tgl_entry)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4">{{ $item->mapel->nama_mapel }}</td>
                                        <td class="px-6 py-4">{{ $item->kelas->nama_kelas_lengkap }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="px-2 py-1 
                    {{ $item->status == 'Selesai' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}
                    rounded-full text-xs font-semibold">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                            Belum ada penilaian harian.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>

                {{-- TABEL NILAI UJIAN --}}
                <div class="bg-white p-6 rounded-xl shadow-md">

                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-file-alt text-green-500"></i>
                            Nilai Ujian (PTS / PAS)
                        </h2>

                        <a href="{{ route('nilai.ujian.index') }}"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 text-sm">
                            Lihat Semua
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-green-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Mapel</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Kelas</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($nilaiUjian as $ujian)
                                    <tr>
                                        <td class="px-6 py-4">
                                            {{ \Carbon\Carbon::parse($ujian->tanggal_ujian)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4">{{ $ujian->mapel->nama_mapel }}</td>
                                        <td class="px-6 py-4">{{ $ujian->kelas->nama_kelas_lengkap }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="px-2 py-1
                    {{ $ujian->status == 'Selesai' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}
                    rounded-full text-xs font-semibold">
                                                {{ $ujian->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                            Belum ada nilai ujian.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection
