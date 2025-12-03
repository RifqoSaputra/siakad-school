{{-- resources/views/dashboard/guru/input-nilai-ujian/summary.blade.php --}}

<div id="summary-ujian-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-75">
    <div class="flex items-center justify-center min-h-screen p-4">

        <div class="bg-white rounded-xl shadow-2xl w-full max-w-6xl transform transition-all duration-300 scale-95"
            role="dialog" aria-modal="true" aria-labelledby="modal-ujian-title">

            <div class="p-3 border-b flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-800 flex items-center" id="modal-ujian-title">
                    <i class="fas fa-chart-line mr-2 text-indigo-600"></i> Rekapitulasi Nilai Ujian
                </h3>
                <button type="button" id="close-summary-ujian-modal"
                    class="text-gray-400 hover:text-gray-600 focus:outline-none p-1 transition duration-150">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <div class="p-6">

                <div class="mb-6 p-4 bg-indigo-50 rounded-lg border-l-4 border-indigo-500">
                    <h4 class="text-sm font-bold text-indigo-700 mb-2 border-b border-indigo-200 pb-2">
                        Informasi Kelas & Mata Pelajaran
                    </h4>
                    <table class="w-full text-sm text-gray-700">
                        <tbody>
                            <tr class="border-b border-indigo-200">
                                <td class="w-1/4 py-1 font-semibold text-indigo-700">Mata Pelajaran:</td>
                                <td class="w-3/4 py-1 font-bold" id="mapel-summary-ujian">Memuat...</td>
                            </tr>
                            <tr class="border-b border-indigo-200">
                                <td class="w-1/4 py-1 font-semibold text-indigo-700">Kelas Target:</td>
                                <td class="w-3/4 py-1 font-bold" id="kelas-summary-ujian">Memuat...</td>
                            </tr>
                            <tr>
                                <td class="w-1/4 py-1 font-semibold text-indigo-700">Tahun Ajaran/Semester:</td>
                                <td class="w-3/4 py-1 font-bold" id="semester-summary-ujian">Memuat...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="overflow-x-auto max-h-[70vh] rounded-lg border border-gray-200 shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200" id="summary-ujian-table">
                        <thead class="bg-indigo-600 sticky top-0 z-10">
                            <tr id="table-header-ujian-row-1">
                                <th rowspan="2"
                                    class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-indigo-700 w-1/5 min-w-[200px]">
                                    Nama Siswa
                                </th>
                                <th colspan="1" id="task-header-ujian-colspan"
                                    class="px-4 py-2 text-center text-xs font-bold text-white uppercase tracking-wider border-b-2 border-indigo-200">
                                    Daftar Penilaian Ujian
                                </th>
                            </tr>

                            <tr id="task-header-ujian-row-2">
                                <th
                                    class="px-2 py-3 text-center text-xs font-semibold text-white border-r border-indigo-700 min-w-[150px] max-w-[150px]">
                                    <div class="flex flex-col items-center">
                                        <span class="truncate w-full block text-xs font-medium">Placeholder Task</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-100" id="summary-ujian-table-body">
                            <tr>
                                <td colspan="3" class="text-center py-4 text-gray-500">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            {{-- FOOTER DIHAPUS KARENA ADA EXPORT EXCEL --}}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const modal = document.getElementById('summary-ujian-modal');
        const closeButton = document.getElementById('close-summary-ujian-modal');

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

        // FUNGSI LOAD REKAP UJIAN
        window.loadSummaryUjianData = async function(kelasId, mapelId, tahunAjaran) {
            const tableBody = modal.querySelector('#summary-ujian-table-body');
            const taskHeaderColspan = modal.querySelector('#task-header-ujian-colspan');
            const taskHeaderRow2 = modal.querySelector('#task-header-ujian-row-2');

            const mapelSummary = document.getElementById('mapel-summary-ujian');
            const kelasSummary = document.getElementById('kelas-summary-ujian');
            const semesterSummary = document.getElementById('semester-summary-ujian');

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            const modalContent = modal.querySelector('.max-w-6xl');
            if (modalContent) {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }

            tableBody.innerHTML =
                '<tr><td colspan="100" class="text-center py-4 text-indigo-500"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data rekapitulasi...</td></tr>';

            taskHeaderRow2.innerHTML = '';
            taskHeaderColspan.setAttribute('colspan', '1');

            mapelSummary.textContent = 'Memuat...';
            kelasSummary.textContent = 'Memuat...';
            semesterSummary.textContent = 'Memuat...';

            try {
                const response = await fetch(
                    `/guru/nilai/ujian/rekap?kelas_id=${kelasId}&mapel_id=${mapelId}&tahun_ajaran=${tahunAjaran}`, {
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
                        errorText = errorData.detail || errorData.message ||
                            `Status: ${response.status}`;
                    } catch {
                        errorText = `Gagal memuat data rekap. Status: ${response.status}`;
                    }
                    throw new Error(errorText);
                }

                const data = await response.json();

                if (!data.success) throw new Error(data.message ||
                'Gagal mengambil data rekapitulasi.');

                mapelSummary.textContent = data.mapel;
                kelasSummary.textContent = data.kelas;
                semesterSummary.textContent = data.tahun_ajaran.replace('/', ' / ') + ' - ' + data
                    .semester;

                const listUjian = data.tasks || [];
                const totalColumns = listUjian.length;

                if (totalColumns === 0) {
                    taskHeaderColspan.setAttribute('colspan', '1');
                    taskHeaderRow2.innerHTML =
                        '<th class="px-4 py-2 text-center text-xs font-semibold text-white">Tidak ada Ujian yang ditemukan.</th>';
                    tableBody.innerHTML =
                        `<tr><td colspan="3" class="text-center py-4 text-gray-500">Tidak ada data siswa / ujian.</td></tr>`;
                    return;
                }

                taskHeaderColspan.setAttribute('colspan', totalColumns);

                let headerTasksHtml = '';
                listUjian.forEach(task => {
                    const dateLabel = task.label ?
                        task.label.substring(task.label.lastIndexOf('(') + 1, task.label
                            .lastIndexOf(')')) : '';

                    headerTasksHtml += `
                    <th class="px-2 py-3 text-center text-xs font-semibold text-white border-r border-indigo-700 min-w-[150px] max-w-[150px]" title="${task.label}">
                        <div class="flex flex-col items-center">
                            <span class="truncate w-full block text-xs font-medium">${task.nama}</span>
                            <span class="text-xs text-indigo-200">(${dateLabel})</span>
                        </div>
                    </th>
                `;
                });
                taskHeaderRow2.innerHTML = headerTasksHtml;

                let studentsHtml = '';
                data.students.forEach(student => {
                    let scoreColumnsHtml = '';

                    listUjian.forEach(task => {
                        const score = student.nilai_per_ujian[task.id];
                        const displayScore = score ?? '-';
                        const scoreColorClass =
                            typeof score === 'number' && score < 75 ?
                            'text-red-600 font-extrabold' :
                            'text-gray-700';

                        scoreColumnsHtml += `
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-center font-medium ${scoreColorClass} border-r border-gray-100">
                            ${displayScore}
                        </td>
                    `;
                    });

                    studentsHtml += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-100">
                            ${student.nama}
                        </td>
                        ${scoreColumnsHtml}
                    </tr>
                `;
                });

                tableBody.innerHTML =
                    data.students.length === 0 ?
                    `<tr><td colspan="${totalColumns + 1}" class="text-center py-4 text-gray-500">Tidak ada siswa terdaftar.</td></tr>` :
                    studentsHtml;

            } catch (error) {
                tableBody.innerHTML =
                    `<tr><td colspan="30" class="text-center py-4 text-red-500"><i class="fas fa-exclamation-triangle mr-2"></i> Gagal memuat data: ${error.message}</td></tr>`;
            }
        };
    });
</script>
