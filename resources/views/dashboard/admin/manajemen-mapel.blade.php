@extends('layouts.template')

@section('title', 'Manajemen Mata Pelajaran')

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

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6 pb-3">
            <h1 class="flex items-center gap-3 text-2xl font-extrabold text-indigo-600">
                <span class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-book text-indigo-600"></i>
                </span>
                Manajemen Mata Pelajaran
            </h1>

            <button onclick="showModal('modalTambah')"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i> Tambah Mapel
            </button>
        </div>

        {{-- SUMMARY --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
                <p class="text-sm text-gray-500">Mapel Aktif</p>
                <p class="text-2xl font-bold text-green-600">{{ $totalMapelAktif }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-indigo-500">
                <p class="text-sm text-gray-500">Total Mapel</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $totalMapel }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
                <p class="text-sm text-gray-500">Mapel Nonaktif</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $totalMapelNonAktif }}</p>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="bg-white p-4 rounded-xl shadow mb-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-gray-600">Cari Kode / Nama Mapel</label>
                <input id="searchInput" value="{{ $request->search }}" class="w-full px-3 py-2 border rounded-lg" placeholder="Ketik untuk mencari...">
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-600">Status Mapel</label>
                <select id="statusSelect" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">Semua</option>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-xl shadow">
            <table class="w-full text-sm text-gray-700">
                <thead class="bg-indigo-50">
                    <tr>
                        <th class="p-3">Kode</th>
                        <th class="p-3">Nama Mapel</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Guru Pengampu</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mapel as $m)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-3">{{ $m->kode_mapel }}</td>
                            <td class="p-3 font-semibold">{{ $m->nama_mapel }}</td>
                            <td class="p-3">{{ $m->kategori_mapel }}</td>
                            <td class="p-3">
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($m->penugasanGuru as $gm)
                                        <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded text-xs">
                                            {{ $gm->guru->nama_guru }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-3 text-center">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold
        {{ $m->status ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $m->status ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <button class="text-indigo-600 font-semibold" onclick="openEdit(this)"
                                    data-id="{{ $m->mapel_id }}" data-kode="{{ $m->kode_mapel }}"
                                    data-nama="{{ $m->nama_mapel }}" data-kategori="{{ $m->kategori_mapel }}"
                                    data-status="{{ $m->status }}"
                                    data-guru="{{ $m->penugasanGuru->pluck('id_guru')->implode(',') }}">
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
        <form method="POST" action="{{ route('admin.mapel.store') }}"
            class="bg-white w-full max-w-2xl rounded-xl shadow-xl">
            @csrf

            {{-- HEADER --}}
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-book-medical text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-indigo-700">Tambah Mata Pelajaran</h3>
                </div>
                <button type="button" onclick="closeModal('modalTambah')">
                    <i class="fas fa-times text-gray-400 hover:text-red-500"></i>
                </button>
            </div>

            {{-- BODY --}}
            <div class="p-6 space-y-4">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Kode Mapel</label>
                        <input name="kode_mapel" required class="input">
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Nama Mapel</label>
                        <input name="nama_mapel" required class="input">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Kategori Mapel</label>
                        <select name="kategori_mapel" class="input">
                            <option value="MKDU">MKDU</option>
                            <option value="DKV">DKV</option>
                            <option value="AKUNTANSI">AKUNTANSI</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Status Mapel</label>
                        <select name="status" class="input">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-sm font-semibold">Guru Pengampu</label>
                    <div class="flex gap-2 mb-3">
                        <select id="guruSelectTambah" class="input">
                            <option value="">-- Pilih Guru --</option>
                            @foreach ($guru as $g)
                                <option value="{{ $g->id_guru }}">{{ $g->nama_guru }}</option>
                            @endforeach
                        </select>
                        <button type="button" onclick="tambahGuru('tambah')" class="btn-primary">
                            Tambah
                        </button>
                    </div>

                    {{-- LIST GURU TERPILIH --}}
                    <div id="listGuruTambah" class="flex flex-wrap gap-2"></div>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-xl">
                <button type="button" onclick="closeModal('modalTambah')" class="btn-secondary">Batal</button>
                <button class="btn-primary">Simpan Mapel</button>
            </div>
        </form>
    </div>

    {{-- ================= MODAL EDIT ================= --}}
    <div id="modalEdit" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
        <form method="POST" action="{{ route('admin.mapel.update') }}"
            class="bg-white w-full max-w-2xl rounded-xl shadow-xl">
            @csrf
            <input type="hidden" name="mapel_id" id="edit_id">

            {{-- HEADER --}}
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-edit text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-indigo-700">Edit Mata Pelajaran</h3>
                </div>
                <button type="button" onclick="closeModal('modalEdit')">
                    <i class="fas fa-times text-gray-400 hover:text-red-500"></i>
                </button>
            </div>

            {{-- BODY --}}
            <div class="p-6 space-y-4">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Kode Mapel</label>
                        <input id="edit_kode" name="kode_mapel" class="input">
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Nama Mapel</label>
                        <input id="edit_nama" name="nama_mapel" class="input">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Kategori Mapel</label>
                        <select id="edit_kategori" name="kategori_mapel" class="input">
                            <option value="MKDU">MKDU</option>
                            <option value="DKV">DKV</option>
                            <option value="AKUNTANSI">AKUNTANSI</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Status Mapel</label>
                        <select id="edit_status" name="status" class="input">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-sm font-semibold">Guru Pengampu</label>

                    <div class="flex gap-2 mb-3">
                        <select id="guruSelectEdit" class="input">
                            <option value="">-- Pilih Guru --</option>
                            @foreach ($guru as $g)
                                <option value="{{ $g->id_guru }}">{{ $g->nama_guru }}</option>
                            @endforeach
                        </select>
                        <button type="button" onclick="tambahGuru('edit')" class="btn-primary">
                            Tambah
                        </button>
                    </div>

                    <div id="listGuruEdit" class="flex flex-wrap gap-2"></div>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-xl">
                <button type="button" onclick="closeModal('modalEdit')" class="btn-secondary">Batal</button>
                <button class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const statusSelect = document.getElementById('statusSelect');
        let typingTimer;
        const debounceDelay = 400; 

        function applyFilter() {
            const params = new URLSearchParams();

            if (searchInput.value.trim() !== '') {
                params.set('search', searchInput.value.trim());
            }

            if (statusSelect.value !== '') {
                params.set('status', statusSelect.value);
            }

            window.location = `?${params.toString()}`;
        }

        searchInput.addEventListener('keyup', () => {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(applyFilter, debounceDelay);
        });

        statusSelect.addEventListener('change', applyFilter);

        function showModal(id) {
            document.getElementById(id).classList.remove('hidden')
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden')
        }

        function tambahGuru(mode) {
            const select = document.getElementById(mode === 'tambah' ? 'guruSelectTambah' : 'guruSelectEdit');
            const list = document.getElementById(mode === 'tambah' ? 'listGuruTambah' : 'listGuruEdit');

            if (!select.value) return;

            // cegah duplikat
            if (list.querySelector(`[data-id="${select.value}"]`)) return;

            const name = select.options[select.selectedIndex].text;

            const item = document.createElement('div');
            item.className = 'flex items-center gap-2 px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm';
            item.dataset.id = select.value;

            item.innerHTML = `
            ${name}
            <input type="hidden" name="guru_ids[]" value="${select.value}">
            <button type="button" onclick="this.parentElement.remove()">
                <i class="fas fa-times text-xs"></i>
            </button>
        `;

            list.appendChild(item);
            select.value = '';
        }

        function openEdit(el) {
            edit_id.value = el.dataset.id;
            edit_kode.value = el.dataset.kode;
            edit_nama.value = el.dataset.nama;
            edit_kategori.value = el.dataset.kategori;
            edit_status.value = el.dataset.status;

            const list = document.getElementById('listGuruEdit');
            list.innerHTML = '';

            if (el.dataset.guru) {
                el.dataset.guru.split(',').forEach(id => {
                    const opt = [...guruSelectEdit.options].find(o => o.value === id);
                    if (!opt) return;

                    const item = document.createElement('div');
                    item.className =
                        'flex items-center gap-2 px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm';
                    item.dataset.id = id;

                    item.innerHTML = `
                    ${opt.text}
                    <input type="hidden" name="guru_ids[]" value="${id}">
                    <button type="button" onclick="this.parentElement.remove()">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                `;
                    list.appendChild(item);
                });
            }

            showModal('modalEdit');
        }
    </script>
@endsection
