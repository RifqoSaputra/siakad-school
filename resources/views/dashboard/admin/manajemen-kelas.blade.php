@extends('layouts.template')

@section('title', 'Manajemen Kelas')

@section('content')
    <style>
        .input {
            width: 100%;
            padding: .6rem .75rem;
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            font-size: .875rem
        }

        .input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 1px #6366f1
        }

        .btn-primary {
            background: #4f46e5;
            color: #fff;
            padding: .5rem 1rem;
            border-radius: .5rem;
            font-weight: 600
        }

        .btn-secondary {
            border: 1px solid #e5e7eb;
            padding: .5rem 1rem;
            border-radius: .5rem
        }
    </style>

    <div class="p-6 bg-gray-50 min-h-screen">

        {{-- ================= HEADER ================= --}}
        <div class="flex justify-between items-center mb-6 pb-3">
            <h1 class="flex items-center gap-3 text-2xl font-extrabold text-indigo-600">
                <span class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-school text-indigo-600"></i>
                </span>
                Manajemen Kelas
            </h1>

            <button onclick="showModal('modalTambah')"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i> Tambah Kelas
            </button>
        </div>

        {{-- ================= SUMMARY ================= --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
                <p class="text-sm text-gray-500">Kelas Aktif</p>
                <p class="text-2xl font-bold text-green-600">{{ $kelasAktif }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-indigo-500">
                <p class="text-sm text-gray-500">Total Kelas</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $totalKelas }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
                <p class="text-sm text-gray-500">Kelas Nonaktif</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $kelasNonaktif }}</p>
            </div>
        </div>

        {{-- ================= FILTER ================= --}}
        <div class="bg-white p-4 rounded-xl shadow mb-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-gray-600">Cari Kelas / Wali Kelas</label>
                <input id="searchInput" value="{{ $request->search }}" class="w-full px-3 py-2 border rounded-lg"
                    placeholder="Ketik nama kelas atau wali kelas...">
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-600">Status Kelas</label>
                <select id="statusSelect" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">Semua</option>
                    <option value="1" {{ $request->status === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ $request->status === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </div>

        {{-- ================= TABLE ================= --}}
        <div class="bg-white rounded-xl shadow">
            <table class="w-full text-sm text-gray-700">
                <thead class="bg-indigo-50">
                    <tr>
                        <th class="p-3">Kelas</th>
                        <th class="p-3">Tahun Ajaran</th>
                        <th class="p-3">Wali Kelas</th>
                        <th class="p-3 text-center">Jumlah Siswa</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kelas as $k)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-3 font-semibold">{{ $k->nama_kelas_lengkap }}</td>
                            <td class="p-3">{{ $k->tahun_ajaran }}</td>
                            <td class="p-3">{{ $k->waliKelas->nama_guru ?? '-' }}</td>
                            <td class="p-3 text-center">
                                {{ $k->siswaTerdaftar->count() }}
                            </td>
                            <td class="p-3 text-center">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $k->status ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $k->status ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <button class="text-indigo-600 font-semibold" onclick="openEdit(this)"
                                    data-id="{{ $k->kelas_id }}" data-tingkat="{{ $k->tingkat_kelas }}"
                                    data-nama="{{ $k->nama_kelas }}" data-tahun="{{ $k->tahun_ajaran }}"
                                    data-semester="{{ $k->semester }}" data-status="{{ $k->status }}"
                                    data-walikelas="{{ $k->walikelas }}">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= MODAL TAMBAH ================= --}}
    <div id="modalTambah" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
        <form method="POST" action="{{ route('admin.kelas.store') }}"
            class="bg-white w-full max-w-3xl rounded-xl shadow-xl">
            @csrf

            <div class="flex items-center justify-between px-6 py-4 border-b">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-school text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-indigo-700">Tambah Kelas</h3>
                </div>
                <button type="button" onclick="closeModal('modalTambah')">
                    <i class="fas fa-times text-gray-400 hover:text-red-500"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Tingkat Kelas</label>
                        <input name="tingkat_kelas" required class="input" placeholder="Contoh: 10, 11, 12">
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Nama Kelas</label>
                        <input name="nama_kelas" required class="input" placeholder="Contoh: DKV 1">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Wali Kelas</label>
                        <select name="walikelas" class="input">
                            <option value="">-- Pilih Guru --</option>
                            @foreach ($guru as $g)
                                <option value="{{ $g->id_guru }}">{{ $g->nama_guru }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Status</label>
                        <select name="status" class="input">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Tahun Ajaran</label>
                        <input name="tahun_ajaran" class="input" placeholder="2024/2025">
                    </div>
                </div>

                <div>
                    <label class="text-sm font-semibold">Assign Siswa (Belum Punya Kelas)</label>

                    <input type="text" id="searchSiswaTambah" class="input mb-2" placeholder="Ketik nama siswa...">

                    <div id="resultSiswaTambah" class="border rounded-lg max-h-40 overflow-y-auto text-sm"></div>

                    <div id="listSiswaTambah" class="border rounded-lg max-h-40 overflow-y-auto divide-y text-sm"></div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalTambah')" class="btn-secondary">Batal</button>
                <button class="btn-primary">Simpan Kelas</button>
            </div>
        </form>
    </div>

    {{-- ================= MODAL EDIT ================= --}}
    <div id="modalEdit" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
        <form method="POST" action="{{ route('admin.kelas.update') }}"
            class="bg-white w-full max-w-3xl rounded-xl shadow-xl">
            @csrf
            <input type="hidden" name="kelas_id" id="edit_id">

            <div class="flex items-center justify-between px-6 py-4 border-b">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-school text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-indigo-700">Edit Kelas</h3>
                </div>
                <button type="button" onclick="closeModal('modalEdit')">
                    <i class="fas fa-times text-gray-400 hover:text-red-500"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Tingkat Kelas</label>
                        <input id="edit_tingkat" name="tingkat_kelas" class="input">
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Nama Kelas</label>
                        <input id="edit_nama" name="nama_kelas" class="input">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Wali Kelas</label>
                        <select id="edit_walikelas" name="walikelas" class="input">
                            <option value="">-- Pilih Guru --</option>
                            @foreach ($guru as $g)
                                <option value="{{ $g->id_guru }}">{{ $g->nama_guru }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Status</label>
                        <select id="edit_status" name="status" class="input">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Tahun Ajaran</label>
                        <input id="edit_tahun" name="tahun_ajaran" class="input">
                    </div>
                </div>

                <div>
                    <label class="text-sm font-semibold mb-1 block">Siswa dalam kelas ini</label>
                    <div id="listSiswaEdit"
                        class="border rounded-lg max-h-40 overflow-y-auto divide-y text-sm mb-4 bg-gray-50">
                    </div>

                    <label class="text-sm font-semibold mb-1 block">Tambah Siswa (Belum Punya Kelas)</label>
                    <input type="text" id="searchSiswaEdit" class="input mb-2" placeholder="Ketik nama siswa...">
                    <div id="resultSiswaEdit" class="border rounded-lg max-h-40 overflow-y-auto text-sm"></div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalEdit')" class="btn-secondary">Batal</button>
                <button class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <script>
        const edit_id = document.getElementById('edit_id');
        const edit_tingkat = document.getElementById('edit_tingkat');
        const edit_nama = document.getElementById('edit_nama');
        const edit_tahun = document.getElementById('edit_tahun');
        const edit_status = document.getElementById('edit_status');
        const edit_walikelas = document.getElementById('edit_walikelas');

        function showModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function openEdit(el) {
            edit_id.value = el.dataset.id;
            edit_tingkat.value = el.dataset.tingkat;
            edit_nama.value = el.dataset.nama;
            edit_tahun.value = el.dataset.tahun;
            edit_status.value = el.dataset.status;
            edit_walikelas.value = el.dataset.walikelas || '';

            document.getElementById('listSiswaEdit').innerHTML = '';

            showModal('modalEdit');
            loadSiswaKelas(edit_id.value);
        }

        function loadSiswaKelas(kelasId) {
            fetch(`/admin/kelas/${kelasId}/siswa`)
                .then(res => res.json())
                .then(data => {
                    const list = document.getElementById('listSiswaEdit');
                    list.innerHTML = '';

                    data.forEach(s => {
                        const item = document.createElement('div');
                        item.dataset.id = s.id_siswa;
                        item.className = 'flex justify-between items-center px-3 py-2 border-b';

                        item.innerHTML = `
                    <span>${s.nama}</span>
                    <div>
                        <input type="hidden" name="siswa_ids[]" value="${s.id_siswa}">
                        <button type="button"
                            onclick="this.closest('[data-id]').remove()"
                            class="text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                        list.appendChild(item);
                    });
                });
        }

        function loadDefaultSiswa(mode) {
            const result = document.getElementById(`resultSiswa${mode}`);
            const kelasId = mode === 'Edit' ? edit_id.value : '';

            fetch(`/admin/kelas/search-siswa?kelas_id=${kelasId}`)
                .then(res => res.json())
                .then(data => {
                    result.innerHTML = '';

                    if (data.empty) {
                        result.innerHTML = `
                        <div class="px-3 py-2 text-gray-500 italic">
                            ${data.message}
                        </div>`;
                        return;
                    }

                    data.forEach(s => {
                        const div = document.createElement('div');
                        div.className = 'px-3 py-2 hover:bg-indigo-50 cursor-pointer';
                        div.textContent = s.nama;

                        div.onclick = () => addSiswa(mode, s);
                        result.appendChild(div);
                    });
                });
        }

        function addSiswa(mode, s) {
            const list = document.getElementById(`listSiswa${mode}`);
            if (list.querySelector(`[data-id="${s.id_siswa}"]`)) return;

            const item = document.createElement('div');
            item.dataset.id = s.id_siswa;
            item.className = 'flex justify-between items-center px-3 py-2 border-b';

            item.innerHTML = `
            <span>${s.nama}</span>
            <div>
                <input type="hidden" name="siswa_ids[]" value="${s.id_siswa}">
                <button type="button"
                    onclick="this.closest('[data-id]').remove()"
                    class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

            list.appendChild(item);
        }

        function initSearch(mode) {
            const input = document.getElementById(`searchSiswa${mode}`);
            const result = document.getElementById(`resultSiswa${mode}`);
            let timer;

            input.addEventListener('keyup', () => {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    if (input.value.length < 2) {
                        result.innerHTML = '';
                        return;
                    }

                    const kelasId = mode === 'Edit' ? edit_id.value : '';

                    fetch(`/admin/kelas/search-siswa?q=${input.value}&kelas_id=${kelasId}`)
                        .then(res => res.json())
                        .then(data => {
                            result.innerHTML = '';

                            if (data.empty) {
                                result.innerHTML = `
                                <div class="px-3 py-2 text-gray-500 italic">
                                    ${data.message}
                                </div>`;
                                return;
                            }

                            data.forEach(s => {
                                const div = document.createElement('div');
                                div.className = 'px-3 py-2 hover:bg-indigo-50 cursor-pointer';
                                div.textContent = s.nama;
                                div.onclick = () => addSiswa(mode, s);
                                result.appendChild(div);
                            });
                        });
                }, 300);
            });
        }

        initSearch('Tambah');
        initSearch('Edit');
    </script>
@endsection
