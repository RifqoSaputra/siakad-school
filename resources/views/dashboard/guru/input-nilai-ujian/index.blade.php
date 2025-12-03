@extends('layouts.template')

@section('title', 'Daftar Input Nilai Ujian')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    {{-- Gaya Toast Tetap Dipertahankan --}}
    <style>
        .toast-box {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
        }

        .toast {
            background: #22c55e;
            color: white;
            padding: 12px 18px;
            margin-top: 8px;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .15);
            opacity: 0;
            transform: translateX(20px);
            transition: all .25s;
        }

        .toast.error {
            background: #ef4444;
        }

        .toast.show {
            opacity: 1;
            transform: translateX(0);
        }
    </style>

    <div class="p-6 md:p-10 bg-gray-50 min-h-screen">
        <div class="container mx-auto">

            <h1 class="text-3xl font-extrabold text-indigo-800 mb-6 border-b-4 border-indigo-200 pb-2">
                <i class="fas fa-file-signature mr-3 text-indigo-500"></i> Daftar Input Nilai Ujian
            </h1>

            {{-- BOX FILTER --}}
            <div class="bg-white p-4 md:p-6 rounded-xl shadow-md mb-6">

                {{-- Form Filter --}}
                <form id="form-filter" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end" method="GET"
                    action="{{ route('nilai.ujian.index') }}">

                    {{-- Tahun Ajaran/Semester --}}
                    <div>
                        <label for="tahun_ajaran" class="block text-sm font-medium text-gray-700 mb-1">Tahun
                            Ajaran/Semester</label>
                        <select id="tahun_ajaran" name="tahun_ajaran"
                            onchange="document.getElementById('form-filter').submit()"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            {{-- Loop Tahun Ajaran, set selected berdasarkan $tahunAjaranFilter --}}
                            <option value="2024/2025" {{ $tahunAjaranFilter == '2024/2025' ? 'selected' : '' }}>2024/2025
                                (Ganjil)</option>
                            {{-- ... option lainnya ... --}}
                        </select>
                    </div>

                    {{-- Kelas Diampu --}}
                    <div>
                        <label for="kelas_id" class="block text-sm font-medium text-gray-700 mb-1">Kelas Diampu</label>
                        <select id="kelas_id" name="kelas_id" onchange="document.getElementById('form-filter').submit()"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">-- Semua Kelas --</option>
                            @foreach ($kelasYangTersedia as $kelas)
                                <option value="{{ $kelas->kelas_id }}"
                                    {{ $currentKelasId == $kelas->kelas_id ? 'selected' : '' }}>
                                    {{ $kelas->nama_kelas_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Mata Pelajaran --}}
                    <div>
                        <label for="mapel_id" class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                        <select id="mapel_id" name="mapel_id" onchange="document.getElementById('form-filter').submit()"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">-- Semua Mapel --</option>
                            @foreach ($listMapel as $mapel)
                                <option value="{{ $mapel->mapel_id }}"
                                    {{ $currentMapelId == $mapel->mapel_id ? 'selected' : '' }}>
                                    {{ $mapel->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tombol Filter --}}
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full h-auto px-4 py-2 text-white bg-indigo-600 font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition duration-150 ease-in-out">
                            <i class="fas fa-filter mr-2"></i> Filter
                        </button>
                    </div>
                </form>
            </div>

            {{-- MAIN CONTENT (Daftar Tugas Ujian) --}}
            <div class="bg-white p-6 rounded-xl shadow-xl">

                {{-- HEADER DAN TOMBOL REKAP --}}
                <div class="flex flex-col md:flex-row justify-between items-center mb-4 border-b pb-3">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 md:mb-0">
                        Daftar Tugas Ujian (Mata Pelajaran: {{ $currentMapel ?? 'Pilih Mapel' }})
                    </h2>

                    <div class="flex space-x-3 shrink-0">
                        {{-- TOMBOL: REKAP NILAI UJIAN --}}
                        <button type="button" id="open-summary-modal"
                            class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-150 ease-in-out">
                            <i class="fas fa-table mr-2"></i> Rekap Nilai Ujian
                        </button>
                    </div>
                </div>

                {{-- TABEL TUGAS UJIAN --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Tgl. Dibuat</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Jenis Penilaian</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Kelas Target</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Keterangan</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($listUjian as $ujian)
                                <tr id="task-row-{{ $ujian->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($ujian->tgl_entry)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                        {{ $ujian->tipe_ujian }} {{-- <--- PERUBAHAN DI SINI --}}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $ujian->kelas?->nama_kelas_lengkap ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $ujian->deskripsi }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span id="row-status-{{ $ujian->id }}"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $ujian->statusPengisian == 'Selesai' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $ujian->statusPengisian == 'Selesai' ? 'Selesai' : 'Belum Diisi' }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium flex justify-center space-x-3">

                                        @if ($ujian->status === 'Submitted')
                                            {{-- SUDAH SUBMIT — DIKUNCI --}}
                                            <span
                                                class="px-3 py-1 bg-gray-300 text-gray-700 rounded-lg font-semibold inline-flex items-center gap-2 cursor-not-allowed">
                                                <i class="fas fa-lock"></i> Sudah Submit
                                            </span>
                                        @else
                                            {{-- MASIH DRAFT — BOLEH INPUT/EDIT --}}
                                            <button type="button" onclick="loadNilaiUjianInputModal({{ $ujian->id }})"
                                                class="px-3 py-1 text-sm font-medium rounded-md
            {{ $ujian->statusPengisian == 'Selesai'
                ? 'bg-yellow-500 hover:bg-yellow-600'
                : 'bg-indigo-500 hover:bg-indigo-600' }}
            text-white transition duration-150">
                                                <i class="fas fa-keyboard mr-1"></i>
                                                {{ $ujian->statusPengisian == 'Selesai' ? 'Edit Nilai' : 'Input Nilai' }}
                                            </button>

                                            {{-- TOMBOL SUBMIT FINAL --}}
                                            <button type="button" onclick="submitUjianFinal({{ $ujian->id }})"
                                                class="flex items-center gap-2 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-sm transition">
                                                <i class="fas fa-check-circle"></i> Submit
                                            </button>
                                        @endif

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada tugas ujian untuk filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- PEMANGGILAN KOMPONEN POP-UP DARI FILE TERPISAH --}}
    <div class="toast-box" id="toastBox"></div>
    @include('dashboard.guru.input-nilai-ujian.detail') {{-- Diasumsikan path ini benar --}}
    @include('dashboard.guru.input-nilai-ujian.summary')
    @include('components.confirm-delete', [
        'id' => 'confirmSubmitUjianModal',
        'title' => 'Konfirmasi Submit Nilai Ujian',
        'message' =>
            'Apakah Anda yakin ingin mensubmit nilai ujian ini? Proses ini bersifat final dan tidak dapat diubah.',
        'confirmText' => 'Ya, Submit Sekarang',
        'cancelText' => 'Batal',
        'confirmClass' => 'bg-green-600 hover:bg-green-700', // warna tombol submit
    ])

    {{-- Script JavaScript untuk mengontrol Modal --}}
    <script>
        function showToast(msg, type = "success") {
            const box = document.getElementById("toastBox");
            const el = document.createElement("div");

            let cls = "toast";

            // warning dianggap error (merah)
            if (type === "error" || type === "warning") {
                cls += " error";
            }

            el.className = cls;
            el.innerText = msg;

            box.appendChild(el);

            setTimeout(() => el.classList.add("show"), 50);
            setTimeout(() => {
                el.classList.remove("show");
                setTimeout(() => el.remove(), 300);
            }, 2000);
        }

        document.addEventListener('DOMContentLoaded', function() {

            /* ===========================
             * = 1. Modal REKAP NILAI UJIAN =
             * ===========================*/
            const summaryModal = document.getElementById('summary-ujian-modal');
            const openSummaryModalBtn = document.getElementById('open-summary-modal');

            // Ambil data filter dari Blade
            const currentKelasId = '{{ $currentKelasId }}';
            const currentMapelId = '{{ $currentMapelId }}';
            const tahunAjaranFilter = '{{ $tahunAjaranFilter }}';

            // Dapatkan URL API rekap menggunakan route helper Laravel
            const rekapApiUrl = '{{ route('nilai.ujian.rekap') }}';

            if (openSummaryModalBtn && summaryModal) {
                openSummaryModalBtn.addEventListener('click', function() {

                    if (!currentKelasId || !currentMapelId || !tahunAjaranFilter) {
                        showToast('Silakan pilih Kelas, Mapel, dan Tahun Ajaran dulu.', 'error');
                        return;
                    }

                    // Diasumsikan window.loadSummaryData ada di x-summary-modal
                    if (window.loadSummaryUjianData) {
                        window.loadSummaryUjianData(currentKelasId, currentMapelId, tahunAjaranFilter);
                    } else {
                        console.error(
                            "Fungsi loadSummaryUjianData tidak ditemukan. Pastikan summary.blade.php sudah di-include."
                        );
                    }

                    summaryModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';

                    const modalContent = summaryModal.querySelector('.max-w-6xl');
                    modalContent.classList.remove('scale-95');
                    modalContent.classList.add('scale-100');
                });
            }

            /* ===========================
             * = 2. UPDATE ROW STATUS (Setelah input nilai sukses) =
             * ===========================*/
            window.updateRowStatus = function(id) {
                const status = document.querySelector(`#row-status-${id}`);
                const btn = document.querySelector(`#task-row-${id} button`);

                if (status) {
                    status.classList.remove("bg-red-100", "text-red-800");
                    status.classList.add("bg-green-100", "text-green-800");
                    status.innerText = "Selesai";
                }

                if (btn) {
                    btn.innerHTML = '<i class="fas fa-keyboard mr-1"></i> Edit Nilai';
                    btn.classList.remove("bg-indigo-500", "hover:bg-indigo-600");
                    btn.classList.add("bg-yellow-500", "hover:bg-yellow-600");
                }

                // Panggil ulang loadSummaryData (jika modal rekap terbuka) untuk menyegarkan rekap
                if (summaryModal && !summaryModal.classList.contains('hidden') && window.loadSummaryUjianData) {
                    window.loadSummaryUjianData(currentKelasId, currentMapelId, tahunAjaranFilter);
                }
            }
        });

        window.submitUjianFinal = function(ujianId) {
            window.selectedUjianId = ujianId; // simpan ID sementara

            // Tampilkan modal konfirmasi
            const modal = document.getElementById('confirmSubmitUjianModal');
            if (modal) modal.classList.remove('hidden');
        };

        document.addEventListener('DOMContentLoaded', function() {
            const confirmUjianSubmitBtn = document.getElementById('confirmSubmitUjianModal-confirm-btn');
            const confirmUjianModal = document.getElementById('confirmSubmitUjianModal');

            if (confirmUjianSubmitBtn) {
                confirmUjianSubmitBtn.addEventListener('click', async function() {
                    if (!window.selectedUjianId) return;

                    const submitBtn = this;
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...`;

                    try {
                        const response = await fetch(
                            `/guru/nilai/ujian/submit/${window.selectedUjianId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content,
                                }
                            });

                        const res = await response.json();

                        if (!res.success) {
                            showToast(res.message, "warning");
                        } else {
                            showToast(res.message, "success");
                            setTimeout(() => location.reload(), 1200);
                        }

                    } catch (err) {
                        console.error(err);
                        showToast("Server error, silakan coba lagi", "error");
                    } finally {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                        confirmUjianModal.classList.add('hidden');
                    }
                });

                // Tutup modal saat klik Batal atau backdrop
                confirmUjianModal.addEventListener('click', (e) => {
                    if (e.target === confirmUjianModal) {
                        confirmUjianModal.classList.add('hidden');
                    }
                });
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            const cancelBtn = document.getElementById("confirmSubmitUjianModal-cancel-btn");
            const modal = document.getElementById("confirmSubmitUjianModal");

            if (cancelBtn && modal) {
                cancelBtn.addEventListener("click", function() {
                    modal.classList.add("hidden");
                    document.body.style.overflow = "";
                });
            }
        });
    </script>
@endsection
