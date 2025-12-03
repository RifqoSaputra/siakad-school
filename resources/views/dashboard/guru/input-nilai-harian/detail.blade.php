{{-- HANYA BERISI MODAL DAN SCRIPT, untuk di-INCLUDE di index.blade.php --}}

<div id="nilai-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-75">
    <div class="flex items-center justify-center min-h-screen p-4">
        {{-- Konten Modal --}}
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl transform transition-all duration-300 scale-95"
            role="dialog" aria-modal="true" aria-labelledby="modal-title">

            {{-- Header Modal --}}
            <div class="p-6 border-b flex justify-between items-center bg-indigo-50 rounded-t-xl">
                <h3 class="text-2xl font-bold text-indigo-800" id="modal-title">
                    <i class="fas fa-edit mr-2"></i> Input Nilai Penilaian Harian
                </h3>
                <button type="button" id="close-nilai-modal"
                    class="text-gray-400 hover:text-gray-600 focus:outline-none p-1 transition duration-150">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            {{-- Form Input Nilai --}}
            <form id="form-simpan-nilai">
                @csrf
                {{-- Input tersembunyi untuk ID Tugas yang sedang diisi --}}
                <input type="hidden" name="nilai_tambahan_id" id="nilai_tambahan_id">

                <div class="p-6">
                    <div id="modal-loading" class="text-center py-8 text-gray-500 hidden">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Memuat data...</p>
                    </div>

                    <div id="modal-content-wrapper">
                        {{-- Informasi Tugas --}}
                        <div class="mb-6 p-4 bg-indigo-50 rounded-lg border-l-4 border-indigo-500">
                            <table class="w-full text-sm text-gray-700">
                                <tbody>
                                    <tr class="border-b border-indigo-200">
                                        <td class="w-1/3 py-1 font-semibold text-indigo-700">Tgl. Dibuat:</td>
                                        <td class="w-2/3 py-1 font-bold" id="info-tgl-dibuat"></td>
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
                                        <td class="w-1/3 py-1 font-semibold text-indigo-700">Jenis Penilaian:</td>
                                        <td class="w-2/3 py-1 font-bold" id="info-tipe-penunjang"></td>
                                    </tr>
                                    <tr>
                                        <td class="w-1/3 py-1 font-semibold text-indigo-700">Deskripsi:</td>
                                        <td class="w-2/3 py-1 font-bold" id="info-deskripsi"></td>
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
                                <tbody id="table-nilai-body" class="bg-white divide-y divide-gray-200">
                                    {{-- Data siswa akan diisi oleh JavaScript --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Footer Modal --}}
                <div class="p-3 border-t flex justify-end items-center bg-gray-50 rounded-b-xl">
                    <button type="submit" id="btn-simpan-nilai"
                        class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:bg-indigo-400"
                        disabled>
                        <i class="fas fa-save mr-2"></i> Simpan Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@include('components.alert', [
    'id' => 'alertNilaiKosong',
    'title' => 'Nilai Belum Lengkap!',
    'message' => 'Beberapa siswa belum memiliki nilai.',
    'type' => 'warning',
    'buttonText' => 'Tutup',
])

{{-- Skrip JavaScript untuk logika modal --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('nilai-modal');
        const closeModalButton = document.getElementById('close-nilai-modal');
        const tableBody = document.getElementById('table-nilai-body');
        const loading = document.getElementById('modal-loading');
        const contentWrapper = document.getElementById('modal-content-wrapper');
        const formSimpan = document.getElementById('form-simpan-nilai');
        const btnSimpan = document.getElementById('btn-simpan-nilai');
        const nilaiTambahanIdInput = document.getElementById('nilai_tambahan_id');

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
            if (e.target.id === 'nilai-modal') {
                closeModal();
            }
        });

        // 1. Logic Memuat Data Modal (Dipanggil dari tombol di index.blade.php)
        window.loadNilaiInputModal = async function(nilaiTambahanId) {

            // Tampilkan modal dan loading state
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            loading.classList.remove('hidden');
            contentWrapper.classList.add('hidden');
            btnSimpan.disabled = true;

            nilaiTambahanIdInput.value = nilaiTambahanId;
            tableBody.innerHTML = '';

            try {
                // Gunakan route API yang baru
                const url = `{{ url('guru/nilai/harian') }}/${nilaiTambahanId}/data-input`;
                const response = await fetch(url);
                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Gagal mengambil data siswa.');
                }

                const info = data.info_tugas;
                const siswaData = data.data_siswa;

                let tglDibuatFormatted = 'N/A';
                if (info
                    .tgl_dibuat) { // Asumsi properti dari backend adalah 'tgl_dibuat' (termasuk jam)
                    try {
                        const dateObj = new Date(info.tgl_dibuat);
                        tglDibuatFormatted = new Intl.DateTimeFormat('id-ID', {
                            day: '2-digit', // HH (Hari)
                            month: '2-digit', // BB (Bulan)
                            year: 'numeric', // TTTT (Tahun)
                            hour: '2-digit', // JJ (Jam)
                            minute: '2-digit', // MM (Menit)
                            hour12: false, // Memastikan format 24 jam
                        }).format(dateObj);

                        // Format default id-ID biasanya DD/MM/YYYY, HH.MM. Kita ubah separator dan formatnya.
                        // Contoh: 22/11/2025 20.59 => 22-11-2025 20:59

                        // 1. Mengganti separator tanggal '/' menjadi '-'
                        // 2. Mengganti separator waktu '.' menjadi ':' (jika default-nya menggunakan titik)
                        tglDibuatFormatted = tglDibuatFormatted.replace(/\//g, '-').replace('.', ':');

                    } catch (e) {
                        console.warn("Gagal memformat tanggal dan waktu:", e);
                        tglDibuatFormatted = info.tgl_dibuat;
                    }
                }

                // Isi detail informasi tugas
                document.getElementById('info-tgl-dibuat').textContent = tglDibuatFormatted;
                document.getElementById('info-kelas').textContent = info.kelas;
                document.getElementById('info-mapel').textContent = info.mapel;
                document.getElementById('info-tipe-penunjang').textContent = info.tipe_penunjang;
                document.getElementById('info-deskripsi').textContent = info.deskripsi ?? '';

                // Isi tabel siswa
                let html = '';
                if (siswaData.length === 0) {
                    html =
                        `<tr><td colspan="4" class="text-center py-4 text-gray-500">Tidak ada siswa terdaftar di kelas ini.</td></tr>`;
                    btnSimpan.disabled = true;
                } else {
                    siswaData.forEach((siswa, index) => {
                        // Nilai saat ini (jika null, biarkan kosong agar placeholder muncul)
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

                document.querySelectorAll('#table-nilai-body input[type="number"]').forEach(input => {
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

                // Tampilkan konten dan aktifkan tombol simpan
                loading.classList.add('hidden');
                contentWrapper.classList.remove('hidden');

                // Tampilkan transisi modal
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

        function showAlertNilaiKosong(listSiswa) {
            const modalId = 'alertNilaiKosong';
            const modalElement = document.getElementById(modalId);
            const listContainer = document.getElementById(modalId + '-list');
            const messageEl = document.getElementById(modalId + '-message');

            // Reset list
            listContainer.innerHTML = '';

            if (listSiswa.length > 0) {
                messageEl.textContent = "Nilai siswa berikut belum diisi:";
                listSiswa.forEach(nama => {
                    const li = document.createElement('li');
                    li.textContent = "• " + nama;
                    listContainer.appendChild(li);
                });
            } else {
                messageEl.textContent = "Semua nilai sudah terisi.";
            }

            // Tampilkan alert (Bootstrap Modal)
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }

        // 2. Logic Menyimpan Nilai (Saat Tombol "Simpan Nilai" Ditekan)
        formSimpan.addEventListener('submit', async function(e) {
            e.preventDefault();

            btnSimpan.disabled = true;
            btnSimpan.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...`;

            const nilaiInputs = document.querySelectorAll('#table-nilai-body input[type="number"]');
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
                showAlertNilaiKosong(listKosong);
                btnSimpan.disabled = false;
                btnSimpan.innerHTML = `<i class="fas fa-save mr-2"></i> Simpan Nilai`;
                return;
            }

            const formData = new FormData(formSimpan);

            try {
                const response = await fetch(`/guru/nilai/harian/save-nilai`, {
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

                const id = nilaiTambahanIdInput.value;

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
