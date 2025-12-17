@extends('layouts.template')

@section('title', 'Daftar Penilaian Harian')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

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

    {{-- Ditingkatkan dari max-w-7xl mx-auto menjadi container mx-auto yang lebih lebar --}}
    <div class="p-6 md:p-10 bg-gray-50 min-h-screen">
        <div class="container mx-auto">

            <h1 class="text-3xl font-extrabold text-indigo-800 mb-6 border-b-4 border-indigo-200 pb-2">
                <i class="fas fa-list-alt mr-3 text-indigo-500"></i> Daftar Penilaian Harian
            </h1>

            <h2 class="text-xl font-semibold text-gray-700 mb-4 ml-1 hidden">Filter Tugas Harian</h2>
            {{-- H2 Filter disembunyikan karena sudah ada di dalam box --}}

            {{-- BOX FILTER --}}
            {{-- Menggunakan padding dan shadow yang lebih kecil agar sesuai dengan gambar --}}
            <div class="bg-white p-4 md:p-6 rounded-xl shadow-md mb-6">

                {{-- Form Filter disesuaikan agar elemen-elemen di dalamnya memiliki gaya input yang benar --}}
                <form id="form-filter" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end" method="GET"
                    action="{{ route('nilai.harian.index') }}">

                    {{-- Bungkus setiap select dengan div untuk mengatur margin dan label --}}
                    <div>
                        <label for="tahun_ajaran" class="block text-sm font-medium text-gray-700 mb-1">Tahun
                            Ajaran/Semester</label>
                        {{-- Mengganti input select agar terlihat seperti di screenshot --}}
                        <select id="tahun_ajaran" name="tahun_ajaran"
                            onchange="document.getElementById('form-filter').submit()"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            {{-- Loop Tahun Ajaran, set selected berdasarkan $tahunAjaranFilter --}}
                            <option value="2024/2025" {{ $tahunAjaranFilter == '2024/2025' ? 'selected' : '' }}>2024/2025
                                (Ganjil)</option>
                            {{-- ... option lainnya ... --}}
                        </select>
                    </div>

                    <div>
                        <label for="kelas_id" class="block text-sm font-medium text-gray-700 mb-1">Kelas Diampu</label>
                        {{-- Menambahkan kelas CSS untuk select --}}
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

                    <div>
                        <label for="mapel_id" class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                        {{-- Menambahkan kelas --}}
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

            {{-- MAIN CONTENT (Daftar Tugas) --}}
            <div class="bg-white p-6 rounded-xl shadow-xl">

                {{-- HEADER DAN TOMBOL TAMBAH TUGAS (DITAMBAH TOMBOL REKAP) --}}
                {{-- Mengubah gaya agar Tombol berada di kanan dan rata dengan baris Header --}}
                <div class="flex flex-col md:flex-row justify-between items-center mb-4 border-b pb-3">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 md:mb-0">
                        Daftar Tugas (Mata Pelajaran: {{ $currentMapel ?? 'Pilih Mapel' }})
                    </h2>

                    {{-- Mengubah w-full md:w-auto menjadi flex-shrink-0 untuk menjaga lebar asli tombol --}}
                    <div class="flex space-x-3 shrink-0">

                        {{-- TOMBOL REKAP --}}
                        <button type="button" id="open-summary-modal"
                            class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-150 ease-in-out">
                            <i class="fas fa-table mr-2"></i> Rekap Nilai Harian
                        </button>

                        {{-- TOMBOL TAMBAH --}}
                        @php
                            $isFinalSubmit =
                                $listTugas->count() > 0 && $listTugas->every(fn($t) => $t->status === 'Submitted');
                        @endphp

                        @if ($isFinalSubmit)
                            <span
                                class="px-4 py-2 bg-gray-300 text-gray-700 font-semibold rounded-lg shadow cursor-not-allowed inline-flex items-center gap-2">
                                <i class="fas fa-lock"></i> Nilai Sudah Dikunci
                            </span>
                        @else
                            <button type="button" id="open-add-task-modal" data-guru-mapel-id="{{ $guruMapelIdTarget }}"
                                data-kelas-id="{{ $currentKelasId }}" data-mapel-id="{{ $currentMapelId }}"
                                class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition">
                                <i class="fas fa-plus-circle mr-2"></i> Tambah Tugas Harian
                            </button>
                        @endif

                        {{-- TOMBOL SUBMIT SEMUA NILAI HARIAN --}}
                        @if ($currentKelasId && $currentMapelId && count($listTugas) > 0)
                            @php
                                $isFinalSubmit = $listTugas->every(fn($t) => $t->status === 'Submitted');
                            @endphp

                            @if ($isFinalSubmit)
                                {{-- SUDAH FINAL SUBMIT --}}
                                <span
                                    class="px-4 py-2 bg-gray-300 text-gray-700 font-semibold rounded-lg shadow cursor-not-allowed inline-flex items-center gap-2">
                                    <i class="fas fa-lock"></i> Sudah Submit Final
                                </span>
                            @else
                                {{-- BELUM FINAL SUBMIT --}}
                                <button type="button" id="btn-submit-index"
                                    class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow hover:bg-indigo-700 transition duration-150 ease-in-out">
                                    <i class="fas fa-paper-plane mr-1"></i> Submit Semua Nilai
                                </button>
                            @endif
                        @endif

                    </div>
                </div>

                {{-- TABEL TUGAS HARIAN --}}
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
                            @forelse($listTugas as $tugas)
                                <tr id="task-row-{{ $tugas->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($tugas->tgl_entry)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                        {{ $tugas->tipe_penunjang }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $tugas->kelas?->nama_kelas_lengkap ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $tugas->deskripsi }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span id="row-status-{{ $tugas->id }}"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $tugas->statusPengisian == 'Selesai' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $tugas->statusPengisian == 'Selesai' ? 'Selesai' : 'Belum Diisi' }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium flex justify-center space-x-3">

                                        @if ($tugas->status === 'Submitted')
                                            {{-- SUDAH SUBMIT — DIKUNCI --}}
                                            <span
                                                class="px-3 py-1 bg-gray-300 text-gray-700 rounded-lg font-semibold inline-flex items-center gap-2 cursor-not-allowed">
                                                <i class="fas fa-lock"></i> Sudah Submit
                                            </span>
                                        @else
                                            {{-- MASIH DRAFT — BOLEH INPUT/EDIT --}}
                                            <button type="button" onclick="loadNilaiInputModal({{ $tugas->id }})"
                                                class="px-3 py-1 text-sm font-medium rounded-md
            {{ $tugas->statusPengisian == 'Selesai'
                ? 'bg-yellow-500 hover:bg-yellow-600'
                : 'bg-indigo-500 hover:bg-indigo-600' }}
            text-white transition duration-150">
                                                <i class="fas fa-keyboard mr-1"></i>
                                                {{ $tugas->statusPengisian == 'Selesai' ? 'Edit Nilai' : 'Input Nilai' }}
                                            </button>

                                            {{-- TOMBOL HAPUS --}}
                                            <button type="button" onclick="deleteTask({{ $tugas->id }})"
                                                class="flex items-center gap-2 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-sm transition">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </button>
                                        @endif

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada tugas harian untuk filter ini. Silakan buat tugas baru.
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
    @include('dashboard.guru.input-nilai-harian.summary')
    <x-confirm-delete id="deleteTaskModal" title="Hapus Tugas Harian"
        message="Apakah Anda yakin ingin menghapus tugas ini? Semua nilai siswa juga akan terhapus."
        confirmText="Ya, Hapus" cancelText="Batal" />

    <x-add-task-modal id="add-task-modal" title="Tambah Tugas Penilaian Harian"
        formAction="{{ route('nilai.harian.store') }}" :kelasTarget="$currentKelas" :mapel="$currentMapel" :currentKelasId="$currentKelasId"
        :currentMapelId="$currentMapelId" :guruMapelIdTarget="$guruMapelIdTarget" />
    <div class="toast-box" id="toastBox"></div>
    @include('dashboard.guru.input-nilai-harian.detail')
    @include('components.confirm-delete', [
        'id' => 'confirmSubmitHarianModal',
        'title' => 'Konfirmasi Submit Nilai Harian',
        'message' =>
            'Apakah Anda yakin ingin mengunci semua nilai harian? Proses ini bersifat final dan tidak dapat diubah.',
        'confirmText' => 'Ya, Kunci Nilai Sekarang',
        'cancelText' => 'Batal',
        'confirmClass' => 'bg-indigo-600 hover:bg-indigo-700', // warna tombol submit
    ])

    {{-- Script JavaScript untuk mengontrol Modal --}}
    <script>
        window.showModal = function(modalId) {
            const modalEl = document.getElementById(modalId);
            if (modalEl) {
                modalEl.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                // Tambahkan logika animasi di sini jika Anda mau
            }
        }
        window.hideModal = function(modalId) {
            const modalEl = document.getElementById(modalId);
            if (modalEl) {
                // Beri sedikit jeda untuk transisi/animasi, lalu sembunyikan
                setTimeout(() => {
                    modalEl.classList.add('hidden');
                    document.body.style.overflow = 'auto'; // Kembalikan scrolling
                }, 150); // Jeda singkat (sesuaikan dengan durasi transisi modal)
            }
        }

        document.addEventListener('DOMContentLoaded', function() {

            /* ===========================
               = 1. Modal REKAP NILAI =
               ===========================*/
            const summaryModal = document.getElementById('summary-modal');
            const openSummaryModalBtn = document.getElementById('open-summary-modal');

            const currentKelasId = '{{ $currentKelasId }}';
            const currentMapelId = '{{ $currentMapelId }}';
            const tahunAjaranFilter = '{{ $tahunAjaranFilter }}';

            if (openSummaryModalBtn && summaryModal) {
                openSummaryModalBtn.addEventListener('click', function() {

                    if (!currentKelasId || !currentMapelId || !tahunAjaranFilter) {
                        alert('Silakan pilih Kelas, Mapel, dan Tahun Ajaran dulu.');
                        return;
                    }

                    if (window.loadSummaryData) {
                        window.loadSummaryData(currentKelasId, currentMapelId, tahunAjaranFilter);
                    }

                    summaryModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';

                    const modalContent = summaryModal.querySelector('.max-w-6xl');
                    modalContent.classList.remove('scale-95');
                    modalContent.classList.add('scale-100');
                });
            }

            /* ===========================
               = 2. Modal TAMBAH TUGAS =
               ===========================*/
            const openAddTaskModalBtn = document.getElementById('open-add-task-modal');
            const addTaskModal = document.getElementById('add-task-modal');
            const formAddTask = addTaskModal ? addTaskModal.querySelector('form') : null;

            if (openAddTaskModalBtn && addTaskModal && formAddTask) {

                openAddTaskModalBtn.addEventListener('click', function() {

                    const guruMapelId = this.dataset.guruMapelId;
                    const kelasId = this.dataset.kelasId;
                    const mapelId = this.dataset.mapelId;

                    if (!kelasId || !mapelId || !guruMapelId) {
                        alert('Silakan pilih Kelas dan Mapel dahulu.');
                        return;
                    }

                    formAddTask.elements.guru_mapel_id.value = guruMapelId;
                    formAddTask.elements.kelas_id.value = kelasId;
                    formAddTask.elements.mapel_id.value = mapelId;
                    window.showModal('add-task-modal');
                });
            }

            /* ===========================
               = 3. DELETE TASK (FINAL) =
               ===========================*/

            window.deleteTask = function(taskId) {
                const modalEl = document.getElementById('deleteTaskModal');
                modalEl.dataset.taskId = taskId;

                // --- PERBAIKAN DI SINI ---
                window.showModal('deleteTaskModal');
                // ---------------------------
            };

            document.getElementById('deleteTaskModal-confirm-btn').addEventListener('click', function() {

                const modalEl = document.getElementById('deleteTaskModal');
                const taskId = modalEl.dataset.taskId;
                const btn = this;

                btn.disabled = true;
                btn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i> Menghapus...`;

                fetch(`/guru/nilai/harian/${taskId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        }
                    })
                    .then(r => r.json())
                    .then(d => {

                        if (d.success) {

                            // Tutup modal
                            window.hideModal('deleteTaskModal');

                            // Hapus row
                            const row = document.getElementById(`task-row-${taskId}`);
                            if (row) {
                                row.style.opacity = "0";
                                setTimeout(() => {
                                    row.remove();

                                    // --- LOGIKA PERBAIKAN DITAMBAHKAN DI SINI ---
                                    const taskTableBody = document.querySelector(
                                        '.min-w-full tbody');
                                    // Hitung jumlah baris TUGAS yang tersisa (mengabaikan baris 'empty' jika ada)
                                    const remainingTasks = taskTableBody.querySelectorAll(
                                        'tr[id^="task-row-"]').length;
                                    const submitButtonContainer = document.querySelector(
                                        '.flex.space-x-3.shrink-0');

                                    // Cari tombol Submit Semua Nilai
                                    const submitButton = document.getElementById(
                                        'btn-submit-index');

                                    // Jika tidak ada lagi tugas, atau hanya tersisa baris 'empty',
                                    // hilangkan tombol submit agar tidak membingungkan.
                                    if (remainingTasks === 0) {
                                        if (submitButton) {
                                            submitButton.remove();
                                        }
                                        // Opsional: Tambahkan kembali baris "Tidak ada tugas" jika belum ada
                                        const emptyRow = taskTableBody.querySelector(
                                            'td[colspan="6"]');
                                        if (!emptyRow) {
                                            taskTableBody.innerHTML = `
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            Tidak ada tugas harian untuk filter ini. Silakan buat tugas baru.
                                        </td>
                                    </tr>
                                `;
                                        }
                                    }
                                    // ---------------------------------------------

                                }, 250);
                            }

                            showToast("Tugas berhasil dihapus.");
                        } else {
                            showToast("Gagal menghapus: " + d.message, "error");
                        }

                        // RESET BUTTON
                        btn.disabled = false;
                        btn.innerHTML = "Ya, Hapus";
                    })
                    .catch(err => {
                        console.error(err);
                        showToast("Terjadi error pada server", "error");

                        btn.disabled = false;
                        btn.innerHTML = "Ya, Hapus";
                    });
            });

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
            }
        });

        function showToast(message, type = "success") {
            const container = document.getElementById('toastBox');
            if (!container) return alert(message);

            const colors = {
                success: "bg-green-600",
                error: "bg-red-600",
                warning: "bg-yellow-500 text-black",
                info: "bg-blue-600",
            };

            const icons = {
                success: "fas fa-check-circle",
                error: "fas fa-times-circle",
                warning: "fas fa-exclamation-triangle",
                info: "fas fa-info-circle",
            };

            const colorClass = colors[type] || colors.success;
            const iconClass = icons[type] || icons.success;

            const el = document.createElement('div');
            el.className =
                `${colorClass} text-white px-4 py-3 rounded-lg shadow-lg flex items-center mb-2 opacity-0 translate-y-3 transition-all duration-300`;
            el.innerHTML = `<i class="${iconClass} mr-2"></i>${message}`;

            container.appendChild(el);

            setTimeout(() => {
                el.classList.remove("opacity-0", "translate-y-3");
            }, 50);

            setTimeout(() => {
                el.classList.add("opacity-0", "translate-y-3");
                setTimeout(() => el.remove(), 300);
            }, 3000);
        }

        // ===== SUBMIT SEMUA NILAI DARI INDEX =====
        document.getElementById("btn-submit-index")?.addEventListener("click", function() {
            const modal = document.getElementById('confirmSubmitHarianModal');
            if (modal) modal.classList.remove('hidden');
            document.body.style.overflow = "hidden";
        });

        // ===== FINAL SUBMIT NILAI HARIAN =====
        document.getElementById("confirmSubmitHarianModal-confirm-btn")?.addEventListener("click", async function() {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...`;

            try {
                const response = await fetch("/guru/nilai/harian/submit", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        kelas_id: "{{ $currentKelasId }}",
                        mapel_id: "{{ $currentMapelId }}",
                        tahun_ajaran: "{{ $tahunAjaranFilter }}"
                    }),
                });

                const result = await response.json();
                showToast(result.message, result.success ? "success" : "error");

                if (result.success) {
                    setTimeout(() => window.location.reload(), 1200);
                }

            } catch (error) {
                console.error(error);
                showToast("Terjadi error server", "error");
            }

            btn.disabled = false;
            btn.innerHTML = "Ya, Kunci Nilai Sekarang";
            document.getElementById('confirmSubmitHarianModal').classList.add('hidden');
            document.body.style.overflow = "";
        });
    </script>
@endsection
