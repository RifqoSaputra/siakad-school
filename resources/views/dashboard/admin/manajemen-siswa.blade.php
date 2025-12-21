@extends('layouts.template')

@section('title', 'Manajemen Siswa')

@section('content')
    <style>
        .input {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 0.875rem;
        }

        .input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 1px #6366f1;
        }

        .btn-primary {
            background: #4f46e5;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
        }

        .btn-secondary {
            border: 1px solid #e5e7eb;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
        }
    </style>

    <div class="p-6 bg-gray-50 min-h-screen">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6 pb-3">
            <h1 class="flex items-center gap-3 text-2xl font-extrabold text-indigo-600">
                <span class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-users text-indigo-600"></i>
                </span>
                Manajemen Siswa
            </h1>

            <button onclick="showModal('modalTambah')"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i> Tambah Siswa
            </button>
        </div>

        {{-- SUMMARY --}}
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
                <p class="text-sm text-gray-500">Siswa Aktif</p>
                <p class="text-2xl font-bold text-green-600">{{ $totalSiswaAktif }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
                <p class="text-sm text-gray-500">Siswa Non Aktif</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $totalSiswaNonAktif }}</p>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="bg-white p-4 rounded-xl shadow mb-5 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="text-sm font-semibold text-gray-600">Cari NIS / Nama</label>
                <input id="searchInput" type="text" value="{{ $request->search }}"
                    class="w-full px-3 py-2 border rounded-lg" placeholder="Ketik untuk mencari...">
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-600">Tahun Ajaran</label>
                <select id="tahunSelect" class="w-full px-3 py-2 border rounded-lg">
                    @foreach ($allTahunAjaran as $ta)
                        <option value="{{ $ta }}" {{ $tahunAjaranAktif == $ta ? 'selected' : '' }}>
                            {{ $ta }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-600">Kelas</label>
                <select id="kelasSelect" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">Semua Kelas</option>
                    @foreach ($allKelas as $k)
                        <option value="{{ $k->kelas_id }}" {{ $request->kelas == $k->kelas_id ? 'selected' : '' }}>
                            {{ $k->nama_kelas_lengkap }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- TABEL --}}
        <div class="bg-white rounded-xl shadow">
            <table class="w-full text-sm text-gray-700">
                <thead class="bg-indigo-50 text-gray-700">
                    <tr>
                        <th class="p-3 text-left">NIS</th>
                        <th class="p-3 text-left">Nama</th>
                        <th class="p-3 text-center">Jenis Kelamin</th>
                        <th class="p-3 text-left">Tgl Lahir</th>
                        <th class="p-3 text-left">Agama</th>
                        <th class="p-3 text-left">Kota</th>
                        <th class="p-3 text-left">Kelas</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($siswaData as $s)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-3">{{ $s->nis }}</td>
                            <td class="p-3 font-semibold">{{ $s->nama }}</td>
                            <td class="p-3 text-center">{{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            <td class="p-3">{{ \Carbon\Carbon::parse($s->tgl_lahir)->format('d-m-Y') }}</td>
                            <td class="p-3">{{ $s->agama }}</td>
                            <td class="p-3">{{ $s->kota_rmh }}</td>
                            <td class="p-3">{{ $s->kelas_sekarang ?? '-' }}</td>
                            <td class="p-3 text-center">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold
                        {{ $s->status_siswa == 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $s->status_siswa }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <button class="text-indigo-600 hover:text-indigo-800 font-semibold" onclick="openEdit(this)"
                                    data-id="{{ $s->id_siswa }}" data-nis="{{ $s->nis }}"
                                    data-nama="{{ $s->nama }}" data-jk="{{ $s->jenis_kelamin }}"
                                    data-tgl="{{ $s->tgl_lahir }}" data-agama="{{ $s->agama }}"
                                    data-kota="{{ $s->kota_rmh }}" data-alamat="{{ $s->alamat_rmh }}"
                                    data-status="{{ $s->status_siswa }}">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- PAGINATION --}}
            <div class="p-4 ">
                {{ $siswaData->links() }}
            </div>
        </div>
    </div>

    <div id="modalTambah" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
        <form method="POST" action="{{ route('admin.siswa.store') }}"
            class="bg-white w-full max-w-2xl rounded-xl shadow-xl">
            @csrf
            <!-- HEADER -->
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-user-plus text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-indigo-700">
                        Tambah Siswa
                    </h3>
                </div>
                <button type="button" onclick="closeModal('modalTambah')">
                    <i class="fas fa-times text-gray-400 hover:text-red-500"></i>
                </button>
            </div>

            <!-- BODY -->
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">NIS</label>
                        <input name="nis" required class="input">
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Nama Lengkap</label>
                        <input name="nama" required class="input">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="input">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir" class="input">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Agama</label>
                        <select name="agama" class="input">
                            <option>Islam</option>
                            <option>Kristen</option>
                            <option>Katolik</option>
                            <option>Hindu</option>
                            <option>Buddha</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Kota</label>
                        <input name="kota_rmh" class="input">
                    </div>
                </div>

                <div>
                    <label class="text-sm font-semibold">Alamat Rumah</label>
                    <textarea name="alamat_rmh" rows="2" class="input"></textarea>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-xl">
                <button type="button" onclick="closeModal('modalTambah')" class="btn-secondary">
                    Batal
                </button>
                <button class="btn-primary">
                    Simpan Siswa
                </button>
            </div>
        </form>
    </div>

    <div id="modalEdit" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
        <form method="POST" action="{{ route('admin.siswa.update') }}"
            class="bg-white w-full max-w-2xl rounded-xl shadow-xl">
            @csrf
            <input type="hidden" name="id_siswa" id="edit_id">

            <!-- HEADER -->
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-user-edit text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-indigo-700">
                        Edit Siswa
                    </h3>
                </div>
                <button type="button" onclick="closeModal('modalEdit')">
                    <i class="fas fa-times text-gray-400 hover:text-red-500"></i>
                </button>
            </div>

            <!-- BODY -->
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">NIS</label>
                        <input id="edit_nis" name="nis" class="input">
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Nama Lengkap</label>
                        <input id="edit_nama" name="nama" class="input">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Jenis Kelamin</label>
                        <select id="edit_jk" name="jenis_kelamin" class="input">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Tanggal Lahir</label>
                        <input type="date" id="edit_tgl" name="tgl_lahir" class="input">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Agama</label>
                        <input id="edit_agama" name="agama" class="input">
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Kota</label>
                        <input id="edit_kota" name="kota_rmh" class="input">
                    </div>
                </div>

                <div>
                    <label class="text-sm font-semibold">Alamat Rumah</label>
                    <textarea id="edit_alamat" name="alamat_rmh" rows="2" class="input"></textarea>
                </div>

                <div>
                    <label class="text-sm font-semibold">Status Siswa</label>
                    <select id="edit_status" name="status_siswa" class="input">
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                        <option value="Lulus">Lulus</option>
                        <option value="Tidak Lulus">Tidak Lulus</option>
                    </select>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-xl">
                <button type="button" onclick="closeModal('modalEdit')" class="btn-secondary">
                    Batal
                </button>
                <button class="btn-primary">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script>
        function showModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        function openEdit(el) {
            document.getElementById('edit_id').value = el.dataset.id;
            document.getElementById('edit_nis').value = el.dataset.nis ?? '';
            document.getElementById('edit_nama').value = el.dataset.nama ?? '';
            document.getElementById('edit_jk').value = el.dataset.jk ?? '';
            document.getElementById('edit_tgl').value = el.dataset.tgl ?? '';
            document.getElementById('edit_agama').value = el.dataset.agama ?? '';
            document.getElementById('edit_kota').value = el.dataset.kota ?? '';
            document.getElementById('edit_alamat').value = el.dataset.alamat ?? '';
            document.getElementById('edit_status').value = el.dataset.status ?? 'Aktif';

            showModal('modalEdit');
        }

        /* FILTER */
        const search = document.getElementById('searchInput');
        const tahun = document.getElementById('tahunSelect');
        const kelas = document.getElementById('kelasSelect');

        function applyFilter() {
            const params = new URLSearchParams();
            if (search.value) params.set('search', search.value);
            if (tahun.value) params.set('tahun_ajaran', tahun.value);
            if (kelas.value) params.set('kelas', kelas.value);
            window.location = '?' + params.toString();
        }

        function debounce(fn, delay) {
            let t;
            return (...args) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...args), delay);
            }
        }

        search.addEventListener('keyup', debounce(applyFilter, 400));
        tahun.addEventListener('change', applyFilter);
        kelas.addEventListener('change', applyFilter);
    </script>
@endsection
