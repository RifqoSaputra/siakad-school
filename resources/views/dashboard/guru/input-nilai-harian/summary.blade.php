{{-- TOAST COMPONENT DITAMBAHKAN DI SINI (Termasuk CSS) --}}
<style>
    @keyframes fadeSlide {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-slide {
        animation: fadeSlide 0.3s ease-out;
    }
</style>
@if (session()->has('success') || session()->has('error') || session()->has('warning') || session()->has('info'))
    <div class="fixed top-4 right-4 z-[60]">
        @if (session('success'))
            <div class="flex items-center bg-green-600 text-white px-4 py-3 rounded-lg shadow-md animate-fade-slide">
                <i class="fas fa-check-circle mr-2 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="flex items-center bg-red-600 text-white px-4 py-3 rounded-lg shadow-md animate-fade-slide">
                <i class="fas fa-times-circle mr-2 text-lg"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if (session('warning'))
            <div class="flex items-center bg-yellow-500 text-white px-4 py-3 rounded-lg shadow-md animate-fade-slide">
                <i class="fas fa-exclamation-triangle mr-2 text-lg"></i>
                <span>{{ session('warning') }}</span>
            </div>
        @endif
        @if (session('info'))
            <div class="flex items-center bg-blue-600 text-white px-4 py-3 rounded-lg shadow-md animate-fade-slide">
                <i class="fas fa-info-circle mr-2 text-lg"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif
    </div>
    {{-- Auto-hide (Untuk Session Flash dari Server) --}}
    <script>
        setTimeout(() => {
            document.querySelectorAll('.fixed.top-4.right-4 > div').forEach(el => {
                el.style.transition = "all 0.5s ease";
                el.style.opacity = "0";
                el.style.transform = "translateY(-10px)";
                setTimeout(() => el.remove(), 500);
            });
        }, 2500);
    </script>
@else
    <!-- GLOBAL TOAST CONTAINER -->
    <div id="toastBox" class="fixed top-4 right-4 z-[9999] pointer-events-none"></div>
@endif
{{-- resources/views/dashboard/guru/input-nilai-harian/summary.blade.php --}}
<div id="summary-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-75">
    <div class="flex items-center justify-center min-h-screen p-4">
        {{-- Konten Modal --}}
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-6xl transform transition-all duration-300 scale-95"
            role="dialog" aria-modal="true" aria-labelledby="modal-title">

            {{-- HEADER MODAL BARU: Lebih sederhana, mirip modal input nilai --}}
            <div class="p-3 border-b flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-800 flex items-center" id="modal-title">
                    <i class="fas fa-chart-bar mr-2 text-indigo-600"></i> Rekapitulasi Nilai Harian
                </h3>
                <button type="button" id="close-summary-modal"
                    class="text-gray-400 hover:text-gray-600 focus:outline-none p-1 transition duration-150">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <div class="p-6">
                {{-- DETAIL REKAP DINAMIS BARU: Menggunakan list non-table agar lebih mirip modal input nilai --}}
                <div class="mb-6 p-4 bg-indigo-50 rounded-lg border-l-4 border-indigo-500">
                    <h4 class="text-sm font-bold text-indigo-700 mb-2 border-b border-indigo-200 pb-2">Informasi Nilai
                        Tugas
                        Mata Pelajaran</h4>
                    <table class="w-full text-sm text-gray-700">
                        <tbody>
                            <tr class="border-b border-indigo-200">
                                <td class="w-1/4 py-1 font-semibold text-indigo-700">Mata Pelajaran:</td>
                                <td class="w-3/4 py-1 font-bold" id="mapel-summary">Memuat...</td>
                            </tr>
                            <tr class="border-b border-indigo-200">
                                <td class="w-1/4 py-1 font-semibold text-indigo-700">Kelas Target:</td>
                                <td class="w-3/4 py-1 font-bold" id="kelas-summary">Memuat...</td>
                            </tr>
                            <tr class="border-b border-indigo-200">
                                <td class="w-1/4 py-1 font-semibold text-indigo-700">Tahun Ajaran/Semester:</td>
                                <td class="w-3/4 py-1 font-bold" id="semester-summary">Memuat...</td>
                            </tr>
                            <tr>
                                <td class="w-1/4 py-1 font-semibold text-indigo-700">Total Penilaian:</td>
                                <td class="w-3/4 py-1 font-bold" id="total-tugas-summary">Memuat...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                {{-- TABEL NILAI DINAMIS --}}
                <div class="overflow-x-auto max-h-[70vh] rounded-lg border border-gray-200 shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200" id="summary-table">
                        <thead class="bg-indigo-600 sticky top-0 z-10">
                            {{-- Baris Header 1 (akan diisi JS: colspan="N") --}}
                            <tr id="table-header-row-1">
                                <th rowspan="2"
                                    class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-indigo-700 w-1/5 min-w-[200px]">
                                    Nama Siswa</th>
                                <th colspan="1" id="task-header-colspan"
                                    class="px-4 py-2 text-center text-xs font-bold text-white uppercase tracking-wider border-b-2 border-indigo-200">
                                    Daftar Penilaian Harian</th>
                                <th rowspan="2"
                                    class="px-4 py-3 text-center text-xs font-bold text-white uppercase tracking-wider border-l border-indigo-700 bg-indigo-700 w-[100px]">
                                    Rata-rata</th>
                            </tr>
                            {{-- Baris Header 2 (Nama Tugas, akan diisi JS) --}}
                            <tr id="task-header-row-2">
                                {{-- Kolom Tugas Dinamis Placeholder --}}
                                <th
                                    class="px-2 py-3 text-center text-xs font-semibold text-white border-r border-indigo-700 min-w-[150px] max-w-[150px]">
                                    <div class="flex flex-col items-center">
                                        <span class="truncate w-full block text-xs font-medium">Placeholder
                                            Task</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100" id="summary-table-body">
                            {{-- Data Siswa Dinamis (akan diisi JS) --}}
                            <tr>
                                <td colspan="3" class="text-center py-4 text-gray-500">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL KONFIRMASI SUBMIT NILAI --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('summary-modal');
        const closeButton = document.getElementById('close-summary-modal');

        // Logika Tutup Modal (Tidak diubah)
        function closeModal() {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            const modalContent = modal.querySelector('.max-w-6xl');
            if (modalContent) {
                modalContent.classList.remove('scale-100');
                modalContent.classList.add('scale-95');
            }
        }

        closeButton.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
        // **FUNGSI UTAMA UNTUK MEMUAT DATA REKAP**
        window.loadSummaryData = async function(kelasId, mapelId, tahunAjaran) { // <--- 3 parameter
            // ... (Kode untuk menampilkan modal dan loading state lainnya) ...
            const tableBody = modal.querySelector('#summary-table-body');
            const taskHeaderColspan = modal.querySelector('#task-header-colspan');
            const taskHeaderRow2 = modal.querySelector('#task-header-row-2');
            const mapelSummary = document.getElementById('mapel-summary');
            const kelasSummary = document.getElementById('kelas-summary');
            const semesterSummary = document.getElementById('semester-summary');
            const totalTugasSummary = document.getElementById('total-tugas-summary');

            // Tampilkan modal dan set loading state
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            const modalContent = modal.querySelector('.max-w-6xl');
            if (modalContent) {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }

            // Kosongkan dan tampilkan loading
            tableBody.innerHTML =
                '<tr><td colspan="100" class="text-center py-4 text-indigo-500"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data rekapitulasi...</td></tr>';
            // Hapus isi header tugas sebelumnya
            taskHeaderRow2.innerHTML = '';
            taskHeaderColspan.setAttribute('colspan', '1');

            // Set detail loading state
            mapelSummary.textContent = 'Memuat...';
            kelasSummary.textContent = 'Memuat...';
            semesterSummary.textContent = 'Memuat...';
            totalTugasSummary.textContent = 'Memuat...';

            window.lastLoadedParams = {
                kelasId,
                mapelId,
                tahunAjaran,
                // Tambahkan default status flags (akan diupdate setelah fetch)
                isSubmitted: false,
                uncompletedTasks: 0
            };
            try {
                // Ganti URL endpoint dan kirim 3 parameter melalui query string
                const response = await
                fetch(`/guru/nilai/harian/rekap?kelas_id=${kelasId}&mapel_id=${mapelId}&tahun_ajaran=${tahunAjaran}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ?
                            document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content') : '',
                        'Content-Type': 'application/json'
                    },
                });
                if (!response.ok) {
                    let errorText = '';
                    try {
                        const errorData = await response.json();
                        console.error('Error body from server:', errorData);
                        errorText = errorData.detail || errorData.message ||
                            `Status: ${response.status}`;
                    } catch (jsonErr) {
                        errorText = `Gagal memuat data rekap. Status: ${response.status}`;
                    }
                    throw new Error(errorText);
                }
                const data = await response.json();
                // Ambil data status submit dan tugas yang belum selesai
                let summaryHtml = '';

                // Cek jika API merespons dengan success: false
                if (!data.success) {
                    throw new Error(data.message || 'Gagal mengambil data rekapitulasi.');
                }

                // 1. Update Detail Rekap
                document.getElementById('mapel-summary').textContent = data.mapel;
                document.getElementById('kelas-summary').textContent = data.kelas;
                document.getElementById('semester-summary').textContent = data.tahun_ajaran.replace('/',
                    ' / ') + ' - ' + data.semester;
                document.getElementById('total-tugas-summary').textContent = data.tasks.length;

                const totalColumns = data.tasks.length;
                // 3. Handle jika tidak ada tugas
                if (totalColumns === 0) {
                    // Set colspan untuk "Daftar Penilaian Harian" ke 1 (default)
                    taskHeaderColspan.setAttribute('colspan', '1');
                    // Set header row 2
                    taskHeaderRow2.innerHTML =
                        '<th class="px-4 py-2 text-center text-xs font-semibold text-white">Tidak ada tugas yang ditemukan.</th>';
                    // Set body table
                    tableBody.innerHTML =
                        `<tr><td colspan="2" class="text-center py-4 text-gray-500">Tidak ada siswa terdaftar atau tidak ada tugas yang ditemukan.</td></tr>`;
                    return;
                }

                // 4. Update Header Tugas (Logika tidak diubah)
                // Atur colspan header 1 sesuai jumlah tugas
                taskHeaderColspan.setAttribute('colspan', totalColumns);
                let headerTasksHtml = '';
                data.tasks.forEach(
                    task => { // task is now {id: '1', nama: 'Tugas', label: 'Tugas (d/m)'}
                        // Ambil bagian tanggal dari label (misal: 'd/m')
                        const dateLabel = task.label ? task.label.substring(task.label.lastIndexOf(
                            '(') + 1, task.label.lastIndexOf(')')) : '';

                        headerTasksHtml += `
                            <th class="px-2 py-3 text-center text-xs font-semibold text-white border-r border-indigo-700 min-w-[150px] max-w-[150px] transition duration-150 hover:bg-indigo-700 cursor-default" title="${task.label}">
                                <div class="flex flex-col items-center">
                                    <span class="truncate w-full block text-xs font-medium">${task.nama}</span>
                                    <span class="text-xs text-indigo-200">(${dateLabel})</span>
                                </div>
                            </th>
                        `;
                    });
                taskHeaderRow2.innerHTML = headerTasksHtml; // Ganti placeholder

                // 5. Update Body Tabel Siswa (Logika tidak diubah)
                let studentsHtml = '';
                data.students.forEach(student => {
                    let scoreColumnsHtml = '';

                    data.tasks.forEach(task => {
                        // Ambil nilai dari nilai_per_tugas menggunakan ID tugas
                        const score = student.nilai_per_tugas[task.id];
                        const displayScore = score !== null && score !== undefined ?
                            score : '-';

                        const isNumericScore = typeof score === 'number' && score !==
                            null;

                        // Perbaikan: Tambahkan font-extrabold untuk nilai di bawah KKM (asumsi KKM 75)
                        const scoreColorClass = isNumericScore && score < 75 ?
                            'text-red-600 font-extrabold' : 'text-gray-700';

                        scoreColumnsHtml += `
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center font-medium ${scoreColorClass} border-r border-gray-100">
                                ${displayScore}
                            </td>
                        `;
                    });

                    // Gunakan rata-rata yang sudah dihitung di Controller
                    const average = student.rata_rata;
                    const displayAverage = average !== null && average !== undefined ?
                        average :
                        '-';
                    const isNumericAverage = typeof average === 'number' && average !== null;
                    // Asumsi KKM 75, beri warna berbeda jika di bawah KKM dan bukan 0
                    const averageBgClass = isNumericAverage && average < 75 && average > 0 ?
                        'bg-red-500' :
                        'bg-indigo-500';
                    const averageDisplay = isNumericAverage && average > 0 ? average.toFixed(
                        2) : '-';
                    // Menampilkan 2 desimal jika numerik dan > 0


                    studentsHtml += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-100">
                                ${student.nama}
                            </td>
                            ${scoreColumnsHtml}
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center font-extrabold text-white ${averageBgClass} border-l border-gray-100">
                                ${averageDisplay}
                            </td>
                        </tr>
                    `;
                });

                // Handle jika tidak ada siswa
                if (data.students.length === 0) {
                    // Colspan = Nama Siswa (1) + Rata-rata (1) + Kolom Tugas (jumlah tugas)
                    const finalColspan = totalColumns + 2;
                    tableBody.innerHTML =
                        `<tr><td colspan="${finalColspan}" class="text-center py-4 text-gray-500">Tidak ada siswa terdaftar di kelas ini pada tahun ajaran ${data.tahun_ajaran}.</td></tr>`;
                } else {
                    tableBody.innerHTML = studentsHtml;
                }

            } catch (error) {
                console.error('Error fetching summary data:', error);
                // Colspan = Nama Siswa (1) + Rata-rata (1) + Kolom Tugas (jumlah tugas saat error)
                const currentColspan = taskHeaderColspan.getAttribute('colspan') ?
                    parseInt(
                        taskHeaderColspan.getAttribute('colspan')) + 2 : 100;
                tableBody.innerHTML =
                    `<tr><td colspan="${currentColspan}" class="text-center py-4 text-red-500"><i class="fas fa-exclamation-triangle mr-2"></i> Gagal memuat data: ${error.message}</td></tr>`;
                showToast(`Gagal memuat data: ${error.message}`, 'error');
            }
        };
    });
</script>
