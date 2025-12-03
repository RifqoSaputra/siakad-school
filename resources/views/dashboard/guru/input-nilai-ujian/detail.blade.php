{{-- HANYA BERISI MODAL DAN SCRIPT, untuk di-INCLUDE di index.blade.php --}}

<div id="nilai-ujian-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-75">
    <div class="flex items-center justify-center min-h-screen p-4">
        {{-- Konten Modal --}}
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl transform transition-all duration-300 scale-95"
            role="dialog" aria-modal="true" aria-labelledby="modal-title">

            {{-- Header Modal --}}
            <div class="p-6 border-b flex justify-between items-center bg-indigo-50 rounded-t-xl">
                <h3 class="text-2xl font-bold text-indigo-800" id="modal-title">
                    <i class="fas fa-edit mr-2"></i> Input Nilai Ujian
                    {{-- Judul diubah menjadi "Input Nilai Ujian" --}}
                </h3>
                <button type="button" id="close-nilai-ujian-modal"
                    class="text-gray-400 hover:text-gray-600 focus:outline-none p-1 transition duration-150">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            {{-- Form Input Nilai --}}
            <form id="form-simpan-nilai-ujian">
                @csrf
                {{-- Input tersembunyi untuk ID Ujian yang sedang diisi --}}
                <input type="hidden" name="nilai_ujian_id" id="ujian_id"> {{-- ID Input diubah menjadi 'ujian_id' --}}

                <div class="p-6">
                    <div id="modal-loading" class="text-center py-8 text-gray-500 hidden">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Memuat data...</p>
                    </div>

                    <div id="modal-content-wrapper">
                        {{-- Informasi Ujian --}}
                        <div class="mb-6 p-4 bg-indigo-50 rounded-lg border-l-4 border-indigo-500">
                            <table class="w-full text-sm text-gray-700">
                                <tbody>
                                    <tr class="border-b border-indigo-200">
                                        <td class="w-1/3 py-1 font-semibold text-indigo-700">Tgl. Ujian:</td>
                                        <td class="w-2/3 py-1 font-bold" id="info-tgl-ujian"></td>
                                    </tr>
                                    <tr class="border-b border-indigo-200">
                                        <td class="w-1/3 py-1 font-semibold text-indigo-700">Kelas:</td>
                                        <td class="w-2/3 py-1 font-bold" id="info-kelas"></td>
                                    </tr>
                                    <tr class="border-b border-indigo-200">
                                        <td class="w-1/3 py-1 font-semibold text-indigo-700">Mata Pelajaran:</td>
                                        <td class="w-2/3 py-1 font-bold" id="info-mapel"></td>
                                    </tr>
                                    <tr class="border-b border-indigo-200">
                                        <td class="w-1/3 py-1 font-semibold text-indigo-700">Jenis Ujian:</td>
                                        <td class="w-2/3 py-1 font-bold" id="info-tipe-ujian"></td>
                                        {{-- ID diubah menjadi 'info-tipe-ujian' --}}
                                    </tr>
                                    <tr>
                                        <td class="w-1/3 py-1 font-semibold text-indigo-700">Keterangan:</td>
                                        <td class="w-2/3 py-1 font-bold" id="info-keterangan"></td>
                                        {{-- ID diubah menjadi 'info-tipe-ujian' --}}
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Tabel Input Nilai --}}
                        <div class="overflow-x-auto border rounded-lg max-h-[400px]">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100 sticky top-0">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase w-10">
                                            No</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase w-24">
                                            NIS</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Nama
                                            Siswa</th>
                                        <th
                                            class="w-full text-center border-indigo-200 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-1">
                                            Nilai (0-100)</th>
                                    </tr>
                                </thead>
                                <tbody id="table-nilai-ujian-body" class="bg-white divide-y divide-gray-200">
                                    {{-- Data siswa akan diisi oleh JavaScript --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Footer Modal --}}
                <div class="p-3 border-t flex justify-end items-center bg-gray-50 rounded-b-xl">
                    <button type="submit" id="btn-simpan-nilai-ujian"
                        class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:bg-indigo-400"
                        disabled>
                        <i class="fas fa-save mr-2"></i> Simpan Nilai
                    </button>
                    {{-- ID tombol diubah --}}
                </div>
            </form>
        </div>
    </div>
</div>
@include('components.alert', [
    'id' => 'alertNilaiUjianKosong',
    'title' => 'Nilai Belum Lengkap!',
    'message' => 'Beberapa siswa belum memiliki nilai.',
    'type' => 'warning',
    'buttonText' => 'Tutup',
])
{{-- ID Alert disesuaikan --}}

{{-- Skrip JavaScript untuk logika modal --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('nilai-ujian-modal');
        const closeModalButton = document.getElementById('close-nilai-ujian-modal');
        const tableBody = document.getElementById('table-nilai-ujian-body');
        const loading = document.getElementById('modal-loading');
        const contentWrapper = document.getElementById('modal-content-wrapper');
        const formSimpan = document.getElementById('form-simpan-nilai-ujian');
        const btnSimpan = document.getElementById('btn-simpan-nilai-ujian');
        const ujianIdInput = document.getElementById('ujian_id');
        // ID variabel disesuaikan

        // Fungsi untuk menutup modal
        function closeModal() {
            modal.classList.add('hidden');
            document.body.style.overflow = ''; // Aktifkan scroll
            const modalContent = modal.querySelector('.max-w-4xl');
            if (modalContent) {
                modalContent.classList.remove('scale-100');
                modalContent.classList.add('scale-95');
            }
        }

        // Event listener untuk tombol tutup
        closeModalButton.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target.id === 'nilai-ujian-modal') {
                closeModal();
            }
        });

        // 1. Logic Memuat Data Modal (Dipanggil dari tombol di index.blade.php)
        window.loadNilaiUjianInputModal = async function(ujianId) {
            // Nama fungsi diubah

            // Tampilkan modal dan loading state
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            loading.classList.remove('hidden');
            contentWrapper.classList.add('hidden');
            btnSimpan.disabled = true;

            ujianIdInput.value = ujianId;
            tableBody.innerHTML = '';

            try {
                // Ganti URL API untuk Ujian
                const url = `{{ url('guru/nilai/ujian') }}/${ujianId}/data-input`;
                const response = await fetch(url);
                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Gagal mengambil data siswa.');
                }

                const info = data.info_ujian; // Nama variabel info disesuaikan
                const siswaData = data.data_siswa;

                let tglUjianFormatted = 'N/A';
                if (info.tanggal_ujian) { // Asumsi nama properti dari backend adalah 'tanggal_ujian'
                    try {
                        const dateObj = new Date(info.tanggal_ujian);
                        tglUjianFormatted = new Intl.DateTimeFormat('id-ID', {
                            day: '2-digit', // HH
                            month: '2-digit', // BB
                            year: 'numeric' // TTTT
                        }).format(dateObj).replace(/\//g,
                            '-'); // Mengganti default separator '/' menjadi '-'
                    } catch (e) {
                        console.warn("Gagal memformat tanggal:", e);
                        tglUjianFormatted = info.tanggal_ujian;
                    }
                }

                // Isi detail informasi ujian
                document.getElementById('info-tgl-ujian').textContent = tglUjianFormatted;
                document.getElementById('info-kelas').textContent = info.kelas;
                document.getElementById('info-mapel').textContent = info.mapel;
                document.getElementById('info-tipe-ujian').textContent = info.tipe_ujian; // ID diubah
                document.getElementById('info-keterangan').textContent = info.deskripsi ??
                    ''; // ID diubah

                // Isi tabel siswa
                let html = '';
                if (siswaData.length === 0) {
                    html =
                        `<tr><td colspan="4" class="text-center py-4 text-gray-500">Tidak ada siswa terdaftar di kelas ini.</td></tr>`;
                    btnSimpan.disabled = true;
                } else {
                    siswaData.forEach((siswa, index) => {
                        const nilaiValue = siswa.nilai !== null ? siswa.nilai : '';

                        html += `
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">${index + 1}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">${siswa.nis}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-semibold">${siswa.nama}</td>
                                <td class="px-4 py-3 text-center">
                                    <input type="number" 
                                            name="nilai_siswa[${siswa.id_siswa}]" 
                                            value="${nilaiValue}"
                                            placeholder="0-100" 
                                            min="0" max="100" 
                                            class="w-full text-center border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                            >
                                </td>
                            </tr>
                        `;
                    });
                    btnSimpan.disabled = false;
                }

                tableBody.innerHTML = html;

                // Logic validasi input
                document.querySelectorAll('#table-nilai-ujian-body input[type="number"]').forEach(
                    input => {
                        input.addEventListener('input', function() {
                            this.value = this.value.replace(/[^0-9]/g, '');

                            if (this.value.length > 3) {
                                this.value = this.value.slice(0, 3);
                            }

                            if (parseInt(this.value) > 100) {
                                this.value = 100;
                            }

                            if (this.value === '' || isNaN(this.value)) {
                                this.value = '';
                            }
                        });
                    });

                // Tampilkan transisi modal
                loading.classList.add('hidden');
                contentWrapper.classList.remove('hidden');

                const modalContent = modal.querySelector('.max-w-4xl');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');

            } catch (error) {
                console.error(error);
                tableBody.innerHTML =
                    `<tr><td colspan="4" class="text-center py-4 text-red-500">Gagal memuat data: ${error.message}</td></tr>`;
                loading.classList.add('hidden');
                contentWrapper.classList.remove('hidden');
                btnSimpan.disabled = true;
                alert('Gagal memuat data: ' + error.message);
            }
        };

        // Fungsi menampilkan alert nilai kosong (disesuaikan ID-nya)
        function showAlertNilaiKosong(listSiswa) {
            const modalId = 'alertNilaiUjianKosong';
            const modalElement = document.getElementById(modalId);
            // Ambil elemen untuk list di dalam modal alert
            const listEl = document.getElementById(`${modalId}-list`);
            const messageEl = document.getElementById(`${modalId}-message`);

            // Perbarui pesan utama
            messageEl.textContent = 'Beberapa siswa belum memiliki nilai.';

            // Isi daftar siswa yang kosong
            let listHtml = '';
            if (listSiswa.length > 0) {
                listSiswa.forEach(nama => {
                    listHtml +=
                        `<li class="text-danger"><i class="fas fa-arrow-right me-1"></i> ${nama}</li>`;
                });
            } else {
                listHtml = '<li class="text-muted">Tidak ada data.</li>';
            }
            listEl.innerHTML = listHtml;

            // **PERUBAHAN KRITIS: Menggunakan Bootstrap Modal Show**
            // Asumsi library Bootstrap dan jQuery/Vanilla JS wrapper-nya tersedia.
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const alertModal = new bootstrap.Modal(modalElement);
                alertModal.show();
            } else if (typeof $ !== 'undefined' && $.fn.modal) {
                // Untuk versi jQuery (jika Bootstrap 4/5 di-load dengan jQuery)
                $(`#${modalId}`).modal('show');
            } else {
                // Fallback jika Bootstrap tidak terdeteksi (Hanya untuk jaga-jaga)
                console.error("Bootstrap Modal library tidak ditemukan. Menampilkan alert biasa.");
                alert("Nilai siswa berikut belum diisi:\n" + listSiswa.join('\n'));
            }

            // Hapus baris showToast lama
            // if (typeof showToast === 'function') {
            //     showToast("Beberapa siswa belum memiliki nilai.", "warning");
            // } else {
            //     alert("Beberapa siswa belum memiliki nilai.");
            // }
        }

        // 2. Logic Menyimpan Nilai (Saat Tombol "Simpan Nilai" Ditekan)
        formSimpan.addEventListener('submit', async function(e) {
            e.preventDefault();

            btnSimpan.disabled = true;
            btnSimpan.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...`;

            const nilaiInputs = document.querySelectorAll(
                '#table-nilai-ujian-body input[type="number"]');
            let listKosong = [];

            nilaiInputs.forEach(input => {
                if (input.value.trim() === '') {
                    const row = input.closest('tr');
                    const namaSiswa = row.children[2].textContent.trim();
                    listKosong.push(namaSiswa);
                }
            });

            // Jika ada yang kosong → tampilkan modal alert dan hentikan submit
            if (listKosong.length > 0) {
                // Panggil fungsi yang kini menampilkan modal popup
                showAlertNilaiKosong(listKosong);

                // TIDAK MELANJUTKAN SUBMIT (hanya menampilkan peringatan)
                btnSimpan.disabled = false;
                btnSimpan.innerHTML = `<i class="fas fa-save mr-2"></i> Simpan Nilai`;
                return; // Hentikan proses submit
            }

            const formData = new FormData(formSimpan);

            try {
                // Ganti URL Action Form untuk Ujian
                const response = await fetch(`/guru/nilai/ujian/save-nilai`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                            .content,
                        "Accept": "application/json",
                    },
                    body: formData
                });

                const data = await response.json();

                if (!data.success) {
                    showToast(data.message || "Gagal menyimpan nilai", "error");
                    btnSimpan.disabled = false;
                    btnSimpan.innerHTML = `<i class="fas fa-save mr-2"></i> Simpan Nilai`;
                    return;
                }

                closeModal();

                showToast("Nilai berhasil disimpan!");

                const id = ujianIdInput.value;

                // Panggil fungsi untuk mengupdate status baris di index.blade.php
                if (window.updateRowStatus) {
                    window.updateRowStatus(id);
                }

            } catch (err) {
                console.error(err);
                showToast("Terjadi error pada server", "error");
            }

            btnSimpan.disabled = false;
            btnSimpan.innerHTML = `<i class="fas fa-save mr-2"></i> Simpan Nilai`;
        });
    });
</script>
