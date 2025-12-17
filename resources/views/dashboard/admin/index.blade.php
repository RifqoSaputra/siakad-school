@extends('layouts.template')

@section('title', 'Dashboard Admin')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        .scrollbar-gutter-stable {
            scrollbar-gutter: stable both-edges;
        }
    </style>

    <div class="p-6 md:p-10 bg-gray-50 min-h-screen">
        <div class="container mx-auto">

            {{-- HEADER --}}
            <h1 class="text-4xl font-extrabold text-indigo-800 mb-8 border-b-4 border-indigo-200 pb-3">
                <i class="fas fa-user-shield mr-3 text-indigo-500"></i>
                Dashboard Admin
            </h1>

            {{-- SUMMARY --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-indigo-500">
                    <p class="text-base text-gray-500 mb-1">Total Siswa</p>
                    <p class="text-3xl font-bold text-indigo-700">{{ $totalSiswa }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-green-500">
                    <p class="text-base text-gray-500 mb-1">Total Guru</p>
                    <p class="text-3xl font-bold text-green-600">{{ $totalGuru }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-yellow-500">
                    <p class="text-base text-gray-500 mb-1">Total Kelas Aktif</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $totalKelas }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-purple-500">
                    <p class="text-base text-gray-500 mb-1">Tahun Ajaran Aktif</p>
                    <p class="text-xl font-semibold text-purple-700">{{ $tahunAjaran }}</p>
                </div>
            </div>

            {{-- AKTIVITAS TERBARU --}}
            <div class="bg-white p-7 rounded-xl shadow-md mb-10">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold flex items-center gap-2">
                        <i class="fas fa-history text-indigo-500"></i>
                        Aktivitas Terbaru Sistem
                    </h2>

                    <button onclick="openAktivitasModal()"
                        class="text-base px-4 py-2 border border-indigo-300 text-indigo-600 rounded-lg hover:bg-indigo-50 transition">
                        Lihat semua
                    </button>
                </div>

                <ul class="space-y-4 text-base">
                    @forelse ($aktivitasTerbaru as $log)
                        <li class="pl-4 border-l-4 {{ $log['warna'] }}">
                            <p class="font-semibold">{{ $log['pesan'] }}</p>
                            <p class="text-sm text-gray-500">{{ $log['waktu'] }}</p>
                        </li>
                    @empty
                        <li class="text-gray-500 italic">Belum ada aktivitas sistem</li>
                    @endforelse
                </ul>
            </div>

            {{-- DATA KRITIS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                {{-- GURU BELUM ABSEN --}}
                <div class="bg-white p-7 rounded-xl shadow-md">
                    <h2 class="text-xl font-bold mb-5 flex items-center gap-2">
                        <i class="fas fa-user-clock text-red-500"></i>
                        Guru Belum Absen Hari Ini
                    </h2>

                    <table class="min-w-full text-base">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Nama Guru</th>
                                <th class="px-4 py-3 text-left font-semibold">Mapel</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($guruBelumAbsen as $item)
                                <tr class="border-t">
                                    <td class="px-4 py-3">{{ $item->guru->nama_guru }}</td>
                                    <td class="px-4 py-3">{{ $item->mapel->nama_mapel }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-5 text-center text-gray-500">
                                        Semua guru sudah absen
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PENGUMUMAN --}}
                <div class="bg-white p-7 rounded-xl shadow-md">
                    <div class="flex justify-between items-center mb-5">
                        <h2 class="text-xl font-bold flex items-center gap-2">
                            <i class="fas fa-bullhorn text-orange-500"></i>
                            Pengumuman Terbaru
                        </h2>

                        <a href="{{ route('admin.pengumuman.index') }}"
                            class="text-base px-4 py-2 border border-orange-300 text-orange-600 rounded-lg hover:bg-orange-50 transition">
                            Lihat semua
                        </a>
                    </div>

                    <table class="w-full text-base">
                        <thead class="text-gray-500 border-b">
                            <tr>
                                <th class="pb-3 text-left">Judul</th>
                                <th class="pb-3 text-left">Target</th>
                                <th class="pb-3 text-left">Status</th>
                                <th class="pb-3 text-left">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pengumuman as $p)
                                <tr class="border-b last:border-0">
                                    <td class="py-3 font-semibold">{{ $p->judul }}</td>
                                    <td class="py-3">
                                        <span class="px-3 py-1 text-sm rounded bg-gray-100 font-medium">
                                            {{ strtoupper($p->target_role) }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        @if ($p->status === 'published')
                                            <span
                                                class="px-3 py-1 text-sm rounded bg-green-100 text-green-700 font-semibold">
                                                Published
                                            </span>
                                        @else
                                            <span class="px-3 py-1 text-sm rounded bg-gray-200 text-gray-600 font-semibold">
                                                Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-gray-500">
                                        {{ $p->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-5 text-center text-gray-500 italic">
                                        Belum ada pengumuman
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL AKTIVITAS --}}
    <div id="modalAktivitas" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white w-full max-w-3xl rounded-xl shadow-lg p-7">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-2xl font-bold text-indigo-700">
                    Semua Aktivitas Sistem
                </h3>
                <button onclick="closeAktivitasModal()" class="text-gray-500 hover:text-red-500 text-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="mb-4">
                <select id="filterAktivitas" class="px-4 py-2 border rounded text-base w-full md:w-auto"
                    onchange="filterAktivitas()">
                    <option value="all">Semua Aktivitas</option>
                    <option value="7">7 Hari Terakhir</option>
                    <option value="30">30 Hari Terakhir</option>
                </select>
            </div>

            <div class="max-h-[400px] overflow-y-auto pr-4 scrollbar-gutter-stable">
                <ul class="space-y-4 text-base">
                    @foreach ($aktivitasGrouped as $i => $log)
                        <li class="pl-4 border-l-4 {{ $log['warna'] }}" data-time="{{ $log['waktu_raw'] }}">
                            <div class="flex justify-between items-start
                                {{ $log['count'] > 1 ? 'cursor-pointer' : '' }}"
                                @if ($log['allow_expand'] && $log['count'] > 1) onclick="toggleDetail({{ $i }})" @endif>
                                <div>
                                    <p class="font-semibold">{{ $log['pesan'] }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $log['count'] }} aktivitas • {{ $log['waktu'] }}
                                    </p>
                                </div>

                                @if ($log['allow_expand'] && $log['count'] > 1)
                                    <i class="fas fa-chevron-down text-gray-400 mt-1"></i>
                                @endif
                            </div>

                            @if ($log['allow_expand'] && $log['count'] > 1)
                                <ul id="detail-{{ $i }}"
                                    class="hidden mt-3 ml-6 space-y-1 text-sm text-gray-600">
                                    @foreach ($log['details'] as $d)
                                        <li>• {{ $d }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <script>
        function openAktivitasModal() {
            document.getElementById('modalAktivitas').classList.remove('hidden');
            document.getElementById('modalAktivitas').classList.add('flex');
        }

        function closeAktivitasModal() {
            document.getElementById('modalAktivitas').classList.add('hidden');
            document.getElementById('modalAktivitas').classList.remove('flex');
        }

        function toggleDetail(id) {
            document.getElementById('detail-' + id).classList.toggle('hidden');
        }

        function filterAktivitas() {
            const value = document.getElementById('filterAktivitas').value;
            const now = Math.floor(Date.now() / 1000);

            document.querySelectorAll('#modalAktivitas li[data-time]').forEach(item => {
                const time = parseInt(item.dataset.time);
                if (value === 'all') {
                    item.style.display = '';
                } else {
                    const days = parseInt(value) * 86400;
                    item.style.display = (now - time <= days) ? '' : 'none';
                }
            });
        }
    </script>
@endsection
