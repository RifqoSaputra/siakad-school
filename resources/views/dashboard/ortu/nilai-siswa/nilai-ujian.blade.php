@extends('layouts.template')

@section('title', 'Nilai Ujian Anak')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- 1. LOAD COMPONENT LOADING --}}
    <div id="global-loading" class="hidden fixed inset-0 z-[100]">
        @include('components.loading')
    </div>

    {{-- NOTE: Tidak ada Modal Detail untuk Nilai Ujian --}}

    <div class="p-6 md:p-10 bg-gray-50 min-h-screen">
        <div class="container mx-auto max-w-screen-2xl">

            <h1 class="text-3xl font-extrabold text-indigo-900 mb-8 border-l-8 border-indigo-500 pl-4">
                Rekap Nilai Ujian ({{ $jenisUjian }}) {{-- Menggunakan variabel jenisUjian --}}
            </h1>

            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r shadow-sm mb-6 flex items-start">
                    <i class="fas fa-exclamation-circle mt-1 mr-3"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- ================= FILTER KELAS + SEMESTER + JENIS UJIAN ================= --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
                <form id="filter-form" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">

                    {{-- Dropdown kelas (Lebar 4 col) --}}
                    <div class="md:col-span-4">
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

                    {{-- Semester (Lebar 3 col) --}}
                    <div class="md:col-span-3">
                        <label for="semester" class="text-sm font-semibold text-gray-700 mb-2 block">Pilih Semester</label>
                        <select name="semester" id="semester"
                            class="border border-gray-300 px-4 py-3 rounded-xl w-full focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 transition bg-gray-50">
                            <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>

                    {{-- Jenis Ujian (PTS/PAS) - BARU (Lebar 3 col) --}}
                    <div class="md:col-span-3">
                        <label for="jenis_ujian" class="text-sm font-semibold text-gray-700 mb-2 block">Jenis Ujian</label>
                        <select name="jenis_ujian" id="jenis_ujian"
                            class="border border-gray-300 px-4 py-3 rounded-xl w-full focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 transition bg-gray-50">
                            <option value="PTS" {{ $jenisUjian == 'PTS' ? 'selected' : '' }}>PTS (Tengah Semester)
                            </option>
                            <option value="PAS" {{ $jenisUjian == 'PAS' ? 'selected' : '' }}>PAS (Akhir Semester)
                            </option>
                        </select>
                    </div>

                    {{-- Button Terapkan (Lebar 2 col) --}}
                    <div class="md:col-span-2">
                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-indigo-200 transition duration-300 flex items-center justify-center gap-2">
                            <i class="fas fa-filter"></i> Terapkan
                        </button>
                    </div>
                </form>
            </div>

            {{-- ===================== TABEL NILAI ===================== --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">
                            Daftar Nilai {{ $jenisUjian }} {{-- Menggunakan variabel jenisUjian --}}
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">Kelas: {{ $kelasFull }}</p>
                    </div>
                    <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full">
                        {{ $semester }}
                    </span>
                </div>

                @if (empty($rekap) || count($rekap) == 0)
                    <div class="text-center py-16 px-6">
                        <div class="bg-indigo-50 inline-block p-4 rounded-full mb-4">
                            <i class="fas fa-file-alt text-3xl text-indigo-400"></i>
                        </div>
                        <h4 class="text-gray-800 font-bold text-lg mb-1">Data Belum Tersedia</h4>
                        <p class="text-gray-500">Belum ada nilai ujian yang diinput untuk kriteria ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 text-gray-500 font-medium">
                                <tr>
                                    <th class="px-6 py-4 text-center text-xs uppercase tracking-wider w-16">No</th>
                                    <th class="px-6 py-4 text-left text-xs uppercase tracking-wider">Mata Pelajaran</th>
                                    <th class="px-6 py-4 text-left text-xs uppercase tracking-wider hidden md:table-cell">
                                        Guru Pengampu</th>
                                    <th class="px-6 py-4 text-center text-xs uppercase tracking-wider w-32">Nilai Akhir</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($rekap as $i => $m)
                                    @php
                                        $nilai = $m['nilai'];
                                        // Logic warna badge nilai
                                        $badgeClass = 'bg-gray-100 text-gray-500';
                                        if (!is_null($nilai)) {
                                            if ($nilai < 70) {
                                                $badgeClass = 'bg-red-100 text-red-700';
                                            } elseif ($nilai < 80) {
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
                                            @if (is_null($nilai))
                                                <span class="text-sm text-gray-400 italic">Belum ada nilai</span>
                                            @else
                                                <span
                                                    class="px-4 py-2 inline-flex text-base leading-5 font-extrabold rounded-lg {{ $badgeClass }}">
                                                    {{ $nilai }}
                                                </span>
                                            @endif
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
        // Script Loading Component saat Form Submit
        // Tidak perlu script Modal karena tidak ada aksi detail
        document.getElementById('filter-form').addEventListener('submit', function() {
            // Tampilkan komponen loading
            document.getElementById('global-loading').classList.remove('hidden');
        });
    </script>
@endsection
