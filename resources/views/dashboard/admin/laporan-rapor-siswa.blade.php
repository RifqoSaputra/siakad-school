@extends('layouts.template')

@section('title', 'Monitoring Laporan Rapor Siswa')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        .toast-box {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
        }
    </style>
    <div id="toastBox" class="toast-box"></div>

    @if (session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                showToast(@json(session('success')), "success");
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                showToast(@json(session('error')), "error");
            });
        </script>
    @endif

    <div class="p-6 md:p-10 bg-gray-50 min-h-screen font-sans">
        <div class="container mx-auto max-w-screen-2xl">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Laporan Rapor Siswa</h1>

            {{-- Filter Section --}}
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <form id="filterForm" method="GET" action="{{ route('admin.laporan.rapor.index') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    @csrf
                    <div>
                        <label for="tahun_ajaran" class="block text-sm font-medium text-gray-700">Tahun Ajaran</label>
                        <select name="tahun_ajaran" id="tahun_ajaran"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            @php $currentYear = (int) date('Y'); @endphp
                            @for ($i = $currentYear + 1; $i >= $currentYear - 5; $i--)
                                @php $ta = $i - 1 . '/' . $i; @endphp
                                <option value="{{ $ta }}" {{ $ta == $tahunAjaran ? 'selected' : '' }}>
                                    {{ $ta }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="semester" class="block text-sm font-medium text-gray-700">Semester</label>
                        <select name="semester" id="semester"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>
                    <div>
                        <label for="kelas_id" class="block text-sm font-medium text-gray-700">Kelas</label>
                        <select name="kelas_id" id="kelas_id"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->kelas_id }}" {{ $k->kelas_id == $kelasId ? 'selected' : '' }}>
                                    {{ $k->nama_kelas_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-1 flex space-x-2">
                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow-md transition duration-150">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        @if ($isFilterApplied)
                            <a href="{{ route('admin.laporan.rapor.index') }}"
                                class="w-full bg-gray-400 hover:bg-gray-500 text-white font-semibold py-2 px-4 rounded-md shadow-md text-center transition duration-150">
                                <i class="fas fa-redo mr-1"></i> Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            @if ($isFilterApplied && $kelasInfo)
                <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Data Rapor Kelas: {{ $kelasInfo->nama_kelas_lengkap }}
                        <span class="text-sm font-normal text-gray-500 ml-2">({{ $semester }} -
                            {{ $tahunAjaran }})</span>
                    </h2>

                    <div class="flex justify-between items-center mb-4">
                        <p class="text-sm text-gray-600">Status Publikasi Rapor Kelas:
                            @php
                                $labelClass = 'text-yellow-600';
                                if ($statusPublikasi === 'Diterbitkan') {
                                    $labelClass = 'text-green-600';
                                } elseif ($statusPublikasi === 'Terkunci') {
                                    $labelClass = 'text-indigo-600';
                                }
                            @endphp
                            <span class="font-bold {{ $labelClass }}">{{ $statusPublikasi }}</span>
                        </p>

                        @if ($statusPublikasi === 'Terkunci')
                            <button id="submitRaporBtn" type="button"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow-lg transition duration-150">
                                <i class="fas fa-check-double mr-2"></i> Submit Rapor Kelas
                            </button>
                        @elseif ($statusPublikasi === 'Diterbitkan')
                            <button type="button" disabled
                                class="bg-green-600 text-white font-semibold py-2 px-4 rounded-md shadow-lg opacity-70">
                                <i class="fas fa-lock mr-2"></i> Rapor Kelas Sudah Diterbitkan
                            </button>
                        @else
                            <button type="button" disabled
                                class="bg-gray-400 text-white font-semibold py-2 px-4 rounded-md shadow-lg opacity-70">
                                <i class="fas fa-clock mr-2"></i> Belum Siap Diterbitkan
                            </button>
                        @endif
                    </div>

                    <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        NIS</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Siswa</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rata-Rata Nilai</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Predikat Sikap</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status Rapor</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($dataRapor as $index => $rapor)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $rapor['nis'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                            {{ $rapor['nama'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span
                                                class="font-bold {{ $rapor['rata_rata_nilai'] !== 'Belum Lengkap' ? 'text-indigo-600' : 'text-red-500' }}">
                                                {{ $rapor['rata_rata_nilai'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span
                                                class="font-medium {{ in_array($rapor['predikat_sikap'], ['Belum Diisi', 'Belum Lengkap', '-- Belum Lengkap --']) ? 'text-red-500' : 'text-green-600' }}">
                                                {{ $rapor['predikat_sikap'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if ($rapor['status_final'] === 'Diterbitkan')
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Diterbitkan</span>
                                            @elseif ($rapor['status_final'] === 'Lengkap')
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Lengkap</span>
                                            @else
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ $rapor['status_final'] }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            {{-- Perbaikan: pakai atribut data-rapor secara langsung dengan @json --}}
                                            <button type="button" data-rapor='@json($rapor)'
                                                class="text-indigo-600 hover:text-indigo-900 detail-rapor-btn">
                                                <i class="fas fa-eye mr-1"></i> Detail
                                            </button>
                                            <a href="{{ route('admin.laporan.rapor.download', ['kelas_id' => $kelasId, 'id_siswa' => $rapor['rapor_id'], 'semester' => $semester, 'tahun_ajaran' => $tahunAjaran]) }}"
                                                class="text-green-600 hover:text-green-900 ml-4">
                                                <i class="fas fa-download mr-1"></i> Cetak
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            Tidak ada data siswa ditemukan untuk kriteria ini atau data belum diproses.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Alert & Confirm (komponen tetap dipakai) --}}
                @include('components.alert', [
                    'id' => 'alertNilaiKosong',
                    'title' => 'Validasi Gagal: Rapor Belum Lengkap!',
                    'message' => '',
                    'details_id' => 'alertNilaiKosong-list',
                    'confirmText' => 'OK',
                    'confirmClass' => 'bg-red-600 hover:bg-red-700',
                ])

                @include('components.confirm-delete', [
                    'id' => 'confirmSubmitRaporModal',
                    'title' => 'Konfirmasi Submit Rapor Kelas',
                    'message' =>
                        'Apakah Anda yakin ingin menSUBMIT (Menerbitkan) Rapor untuk seluruh kelas ini? Tindakan ini akan menyimpan nilai akhir permanen dan menandai rapor sebagai DITERBITKAN (tidak dapat diubah).',
                    'confirmText' => 'Ya, Submit Rapor Sekarang',
                    'cancelText' => 'Batal',
                    'confirmClass' => 'bg-indigo-600 hover:bg-indigo-700',
                ])

                {{-- Modal Detail Inline --}}
                <div id="detailRaporModal"
                    class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/5 shadow-lg rounded-md bg-white">
                        <h3 class="text-xl font-bold mb-4 text-gray-900" id="detailModalTitle"></h3>
                        <div id="detailModalBody" class="text-sm"></div>
                        <div class="mt-4 text-right">
                            <button type="button"
                                class="close-modal-btn px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-700">Tutup</button>
                        </div>
                    </div>
                </div>

                <script>
                    // Global show/hide modal helpers (tidak memakai window.showModal)
                    function showModal(id) {
                        $('#' + id).removeClass('hidden');
                    }

                    function hideModal(id) {
                        $('#' + id).addClass('hidden');
                    }

                    $(document).ready(function() {
                        // DETAIL BUTTON: gunakan data-rapor (JSON) -> parse langsung
                        $(document).on('click', '.detail-rapor-btn', function() {
                            var raw = $(this).attr('data-rapor');
                            var data = {};
                            try {
                                data = JSON.parse(raw);
                            } catch (e) {
                                // fallback ke jQuery data() jika JSON parsing gagal
                                data = $(this).data('rapor') || {};
                            }

                            $('#detailModalTitle').text('Detail Rapor: ' + (data.nama || '-') + ' (' + (data.nis ||
                                '-') + ')');

                            var nilaiMapelHtml = (data.detail_rapor && data.detail_rapor.nilai_mapel) ? data
                                .detail_rapor.nilai_mapel.map(function(score) {
                                    return `<tr>
                                            <td class="px-4 py-2 whitespace-nowrap">${score.mapel}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-center">${score.kkm}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-center font-bold">${score.nilai_rapor}</td>
                                            <td class="px-4 py-2 text-sm">${score.deskripsi}</td>
                                        </tr>`;
                                }).join('') : '<tr><td colspan="4" class="px-4 py-2">Tidak ada data nilai.</td></tr>';

                            var catatan = data.detail_rapor && data.detail_rapor.catatan_rapor ?
                                data.detail_rapor.catatan_rapor :
                                null;

                            var catatanHtml = '';

                            if (!catatan || !['Terkunci', 'Diterbitkan'].includes(catatan.status_publikasi)) {
                                catatanHtml = `
        <p class="text-red-600 font-semibold">
            Catatan Rapor Belum Diterbitkan (Masih Draft / Belum Lengkap)
        </p>`;
                            } else {
                                catatanHtml = `
        <p><span class="font-semibold">Predikat Sikap:</span>
            <strong>${catatan.Sikap || '-'}</strong>
        </p>
        <p class="mt-2"><span class="font-semibold">Catatan Wali Kelas:</span>
            ${catatan.Kepribadian || '-'}</p>
    `;
                            }


                            var absensi = (data.detail_rapor && data.detail_rapor.absensi) ? data.detail_rapor.absensi :
                            {
                                Sakit: 0,
                                Izin: 0,
                                Alpa: 0
                            };

                            var htmlContent = `
                                <div class="mb-4">
                                    <p class="font-semibold text-lg text-indigo-700">Informasi Semester</p>
                                    <p>Kelas: <strong>${(data.detail_rapor && data.detail_rapor.kelas_full) || '-'}</strong></p>
                                    <p>Tahun Ajaran: <strong>${(data.detail_rapor && data.detail_rapor.tahun_ajaran) || '-'}</strong></p>
                                    <p>Semester: <strong>${(data.detail_rapor && data.detail_rapor.semester) || '-'}</strong></p>
                                </div>

                                <p class="font-semibold text-lg text-indigo-700">Nilai Mata Pelajaran</p>
                                <div class="overflow-x-auto mb-4 border rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-indigo-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Mata Pelajaran</th>
                                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-700 uppercase">KKM</th>
                                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-700 uppercase">Nilai Rapor</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Deskripsi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            ${nilaiMapelHtml}
                                        </tbody>
                                    </table>
                                </div>

                                <p class="font-semibold text-lg text-indigo-700">Catatan Akhir Rapor</p>
                                <div class="mb-4 p-4 border rounded-lg bg-yellow-50">
                                    ${catatanHtml}
                                </div>

                                <p class="font-semibold text-lg text-indigo-700">Kehadiran (Absensi)</p>
                                <div class="mb-4 p-4 border rounded-lg bg-blue-50">
                                    <p>Sakit: <strong>${absensi.Sakit || 0} Hari</strong></p>
                                    <p>Izin: <strong>${absensi.Izin || 0} Hari</strong></p>
                                    <p>Alpa: <strong>${absensi.Alpa || 0} Hari</strong></p>
                                </div>
                            `;

                            $('#detailModalBody').html(htmlContent);
                            showModal('detailRaporModal');
                        });

                        $('.close-modal-btn').on('click', function() {
                            hideModal('detailRaporModal');
                            hideModal('alertNilaiKosong');
                            hideModal('confirmSubmitRaporModal');
                        });

                        // --- Logic Submit Rapor: VALIDATE then SHOW CONFIRM ---
                        $('#submitRaporBtn').on('click', function() {
                            let kelasId = '{{ $kelasId }}';
                            let semester = '{{ $semester }}';
                            let tahunAjaran = '{{ $tahunAjaran }}';
                            if (!kelasId) {
                                showToast('Harap pilih Kelas terlebih dahulu.', 'warning');
                                return;
                            }

                            $.ajax({
                                url: '{{ route('admin.laporan.rapor.validate-submit') }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    kelas_id: kelasId,
                                    semester: semester,
                                    tahun_ajaran: tahunAjaran
                                },
                                beforeSend: function() {
                                    $('#submitRaporBtn').attr('disabled', true).html(
                                        '<i class="fas fa-spinner fa-spin mr-2"></i> Memvalidasi...');
                                },
                                success: function(response) {
                                    $('#submitRaporBtn').attr('disabled', false).html(
                                        '<i class="fas fa-check-double mr-2"></i> Submit Rapor Kelas'
                                    );

                                    if (response.status === 'success') {

                                        // SET data-action KE TOMBOL YANG BENAR
                                        $('#confirmSubmitRaporModal-confirm-btn').attr('data-action',
                                            'submit');

                                        // UBAH PESAN DALAM MODAL
                                        $('#confirmSubmitRaporModal').find('.modal-message').html(
                                            `<p>${response.message}</p>
            <p class="mt-2 text-red-600 font-semibold">
                Anda akan menSUBMIT nilai akhir untuk seluruh siswa. Lanjutkan?
            </p>`
                                        );

                                        showModal('confirmSubmitRaporModal');

                                    } else if (response.status === 'warning') {

                                        let listHtml = response.list.map(s => `<li>${s}</li>`).join('');

                                        $('#alertNilaiKosong-message').html(
                                            `${response.message}
             <p class="mt-2 font-semibold">
                Silahkan lengkapi data rapor siswa tersebut terlebih dahulu.
             </p>`
                                        );

                                        $('#alertNilaiKosong-list').html(
                                            `<ul class="list-disc list-inside mt-2 text-sm text-gray-700">
                ${listHtml}
            </ul>`
                                        );

                                        showModal('alertNilaiKosong');

                                    } else {

                                        showToast(response.message || 'Validasi gagal.', 'error');

                                    }
                                },

                                error: function(xhr) {
                                    $('#submitRaporBtn').attr('disabled', false).html(
                                        '<i class="fas fa-check-double mr-2"></i> Submit Rapor Kelas');
                                    let errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr
                                        .responseJSON.message : "Terjadi kesalahan saat validasi submit.";
                                    showToast('Gagal Validasi: ' + errorMsg, 'error');
                                }
                            });
                        });

                        // --- FINAL SUBMIT: langsung listen ke ID real tombol confirm yang component buat ---
                        $(document).on('click', '#confirmSubmitRaporModal-confirm-btn', function() {

                            if ($(this).attr('data-action') !== 'submit') return;

                            let kelasId = '{{ $kelasId }}';
                            let semester = '{{ $semester }}';
                            let tahunAjaran = '{{ $tahunAjaran }}';

                            hideModal('confirmSubmitRaporModal');

                            $.ajax({
                                url: '{{ route('admin.laporan.rapor.final-submit') }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    kelas_id: kelasId,
                                    semester: semester,
                                    tahun_ajaran: tahunAjaran
                                },
                                beforeSend: function() {
                                    $('#submitRaporBtn')
                                        .attr('disabled', true)
                                        .html(
                                        '<i class="fas fa-cog fa-spin mr-2"></i> Memproses Submit...');
                                },
                                success: function(response) {

                                    if (response.status === 'success') {
                                        showToast(response.message, 'success');

                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 1200);

                                    } else {
                                        showToast(response.message || 'Gagal Submit', 'error');
                                        $('#submitRaporBtn')
                                            .attr('disabled', false)
                                            .html(
                                                '<i class="fas fa-check-double mr-2"></i> Submit Rapor Kelas'
                                                );
                                    }
                                },
                                error: function(xhr) {
                                    $('#submitRaporBtn')
                                        .attr('disabled', false)
                                        .html(
                                        '<i class="fas fa-check-double mr-2"></i> Submit Rapor Kelas');

                                    let errorMsg = xhr.responseJSON?.message ??
                                        'Terjadi kesalahan saat menyimpan nilai final.';

                                    showToast(errorMsg, 'error');
                                }
                            });
                        });

                        // fallback: jika component confirm punya tombol cancel dengan class .cancel-button -> close modal
                        $(document).on('click',
                            '#confirmSubmitRaporModal .cancel-button, #confirmSubmitRaporModal #cancelButton',
                            function() {
                                hideModal('confirmSubmitRaporModal');
                            });
                    });

                    function showToast(message, type = "success") {
                        const container = document.getElementById('toastBox');
                        if (!container) return;

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

                        const el = document.createElement("div");
                        el.className = `${colors[type]} text-white px-4 py-3 rounded-lg shadow-lg flex items-center mb-2
                    opacity-0 translate-y-3 transition-all duration-300`;
                        el.innerHTML = `<i class="${icons[type]} mr-2"></i>${message}`;

                        container.appendChild(el);

                        setTimeout(() => {
                            el.classList.remove("opacity-0", "translate-y-3");
                        }, 50);

                        setTimeout(() => {
                            el.classList.add("opacity-0", "translate-y-3");
                            setTimeout(() => el.remove(), 300);
                        }, 3000);
                    }
                </script>
            @else
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-sm"
                    role="alert">
                    <p class="font-bold"><i class="fas fa-info-circle mr-2"></i>Informasi</p>
                    <p>Silakan gunakan filter di atas untuk memilih <strong>Kelas</strong>, <strong>Semester</strong>, dan
                        <strong>Tahun Ajaran</strong> untuk melihat dan memproses laporan rapor.
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection
