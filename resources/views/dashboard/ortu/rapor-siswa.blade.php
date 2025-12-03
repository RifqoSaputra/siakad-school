@extends('layouts.template')

@section('title', 'Cetak Rapor Siswa')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Data Dummy untuk Contoh Tampilan --}}
    @php
        $kelasId = request('kelas_id', 1);
        $semester = request('semester', 'Ganjil');
        $tahunAjaran = '2024/2025';
        $kelasFull = 'IX-A';
        $kelasListDummy = collect([
            (object) ['kelas_id' => 1, 'kelas' => (object) ['tingkat_kelas' => 'IX', 'nama_kelas' => 'A']],
            (object) ['kelas_id' => 2, 'kelas' => (object) ['tingkat_kelas' => 'IX', 'nama_kelas' => 'B']],
        ]);

        $dataRapor = [
            [
                'mapel' => 'Pendidikan Agama',
                'kkm' => 75,
                'nilai_rapor' => 92.5,
                'deskripsi' => 'Sangat menguasai konsep-konsep ibadah dan moralitas.',
            ],
            [
                'mapel' => 'Matematika',
                'kkm' => 75,
                'nilai_rapor' => 88.0,
                'deskripsi' => 'Menguasai konsep aljabar dan geometri dengan baik.',
            ],
            [
                'mapel' => 'Bahasa Indonesia',
                'kkm' => 75,
                'nilai_rapor' => 90.0,
                'deskripsi' => 'Sangat mahir dalam menulis dan berdiskusi formal.',
            ],
            [
                'mapel' => 'Bahasa Inggris',
                'kkm' => 75,
                'nilai_rapor' => 85.5,
                'deskripsi' => 'Memiliki kemampuan komunikasi lisan dan tulisan yang baik.',
            ],
            [
                'mapel' => 'Pendidikan Jasmani',
                'kkm' => 75,
                'nilai_rapor' => 94.0,
                'deskripsi' => 'Sangat aktif dan unggul dalam semua cabang olahraga.',
            ],
        ];

        // Dummy Data untuk Catatan Sikap/Kepribadian
        $catatanOrtu = [
            'Sikap' => 'Sangat Baik',
            'Kepribadian' => 'Memiliki integritas yang tinggi dan selalu bersikap sopan.',
            'Kehadiran' => [
                'Sakit' => 0,
                'Izin' => 1,
                'Alpa' => 0,
            ],
        ];
    @endphp

    <div class="p-6 md:p-10 bg-gray-50 min-h-screen">
        <div class="container mx-auto max-w-screen-2xl">

            <h1 class="text-3xl font-extrabold text-teal-900 mb-8 border-l-8 border-teal-500 pl-4">
                Laporan Hasil Belajar (Rapor)
            </h1>

            {{-- ================= FILTER KELAS + SEMESTER ================= --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
                <form id="filter-form" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">

                    {{-- Dropdown kelas (Lebar 5 col) --}}
                    <div class="md:col-span-5">
                        <label for="kelas_id" class="text-sm font-semibold text-gray-700 mb-2 block">Pilih Kelas</label>
                        <select name="kelas_id" id="kelas_id"
                            class="border border-gray-300 px-4 py-3 rounded-xl w-full focus:ring-2 focus:ring-teal-200 focus:border-teal-500 transition bg-gray-50">
                            @foreach ($kelasListDummy as $k)
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

                    {{-- Semester (Lebar 4 col) --}}
                    <div class="md:col-span-4">
                        <label for="semester" class="text-sm font-semibold text-gray-700 mb-2 block">Pilih Semester</label>
                        <select name="semester" id="semester"
                            class="border border-gray-300 px-4 py-3 rounded-xl w-full focus:ring-2 focus:ring-teal-200 focus:border-teal-500 transition bg-gray-50">
                            <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>

                    {{-- Button Terapkan (Lebar 3 col) --}}
                    <div class="md:col-span-3">
                        <button type="submit"
                            class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-teal-200 transition duration-300 flex items-center justify-center gap-2">
                            <i class="fas fa-search"></i> Tampilkan Rapor
                        </button>
                    </div>
                </form>
            </div>

            {{-- ===================== TABEL NILAI AKADEMIK ===================== --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 mb-8">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-teal-50">
                    <div>
                        <h3 class="text-xl font-extrabold text-teal-800">
                            Nilai Mata Pelajaran
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Kelas: **{{ $kelasFull }}** | Semester:
                            **{{ $semester }}** | TA: **{{ $tahunAjaran }}**</p>
                    </div>
                    <i class="fas fa-award text-3xl text-teal-400"></i>
                </div>

                @if (empty($dataRapor))
                    <div class="text-center py-16 px-6">
                        <div class="bg-teal-50 inline-block p-4 rounded-full mb-4">
                            <i class="fas fa-file-alt text-3xl text-teal-400"></i>
                        </div>
                        <h4 class="text-gray-800 font-bold text-lg mb-1">Data Rapor Belum Tersedia</h4>
                        <p class="text-gray-500">Nilai akhir semester belum diinput oleh wali kelas.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 text-gray-500 font-medium">
                                <tr>
                                    <th class="px-6 py-4 text-center text-xs uppercase tracking-wider w-16">No</th>
                                    <th class="px-6 py-4 text-left text-xs uppercase tracking-wider">Mata Pelajaran</th>
                                    <th class="px-6 py-4 text-center text-xs uppercase tracking-wider w-20">KKM</th>
                                    <th class="px-6 py-4 text-center text-xs uppercase tracking-wider w-32">Nilai Rapor</th>
                                    <th class="px-6 py-4 text-left text-xs uppercase tracking-wider hidden sm:table-cell">
                                        Deskripsi Capaian</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($dataRapor as $i => $item)
                                    @php
                                        $nilai = $item['nilai_rapor'];
                                        $kkm = $item['kkm'];
                                        $isTuntas = $nilai >= $kkm;
                                        $badgeClass = $isTuntas
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-red-100 text-red-700';
                                    @endphp
                                    <tr class="hover:bg-teal-50/50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            {{ $i + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">{{ $item['mapel'] }}</div>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-600">
                                            {{ $kkm }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span
                                                class="px-4 py-2 inline-flex text-base leading-5 font-extrabold rounded-lg {{ $badgeClass }}">
                                                {{ number_format($nilai, 1) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 hidden sm:table-cell">
                                            {{ $item['deskripsi'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- ===================== CATATAN ORANG TUA / KEHADIRAN ===================== --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                {{-- KOTAK KEPRIBADIAN / SIKAP --}}
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
                    <div class="flex items-center mb-4 border-b pb-3">
                        <i class="fas fa-user-check text-xl text-yellow-600 mr-3"></i>
                        <h4 class="text-lg font-bold text-gray-800">Sikap dan Kepribadian</h4>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm font-semibold text-gray-700">Predikat Sikap:</p>
                        <span
                            class="inline-block px-3 py-1 mt-1 text-sm font-extrabold rounded-full bg-yellow-100 text-yellow-800">
                            {{ $catatanOrtu['Sikap'] }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-700">Catatan Wali Kelas:</p>
                        <p class="text-gray-600 mt-1 italic p-3 bg-gray-50 rounded-lg border border-gray-200">
                            {{ $catatanOrtu['Kepribadian'] }}
                        </p>
                    </div>
                </div>

                {{-- KOTAK KEHADIRAN --}}
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
                    <div class="flex items-center mb-4 border-b pb-3">
                        <i class="fas fa-calendar-check text-xl text-indigo-600 mr-3"></i>
                        <h4 class="text-lg font-bold text-gray-800">Rekapitulasi Kehadiran</h4>
                    </div>

                    <ul class="space-y-4">
                        <li class="flex justify-between items-center text-gray-700 border-b pb-2">
                            <span class="font-semibold">Sakit (S):</span>
                            <span class="text-lg font-extrabold text-blue-600">{{ $catatanOrtu['Kehadiran']['Sakit'] }}
                                hari</span>
                        </li>
                        <li class="flex justify-between items-center text-gray-700 border-b pb-2">
                            <span class="font-semibold">Izin (I):</span>
                            <span class="text-lg font-extrabold text-blue-600">{{ $catatanOrtu['Kehadiran']['Izin'] }}
                                hari</span>
                        </li>
                        <li class="flex justify-between items-center text-gray-700">
                            <span class="font-semibold">Tanpa Keterangan (A):</span>
                            <span class="text-lg font-extrabold text-red-600">{{ $catatanOrtu['Kehadiran']['Alpa'] }}
                                hari</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    {{-- Tambahkan script jika ada komponen interaktif (misalnya loading, seperti di nilai-ujian) --}}
@endsection
