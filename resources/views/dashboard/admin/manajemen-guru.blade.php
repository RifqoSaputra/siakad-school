@extends('layouts.template')

@section('title', 'Manajemen Guru')

@section('content')
    <style>
        .input {
            width: 100%;
            padding: .6rem .75rem;
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            font-size: .875rem;
        }

        .input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 1px #6366f1;
        }

        .btn-primary {
            background: #4f46e5;
            color: #fff;
            padding: .5rem 1rem;
            border-radius: .5rem;
            font-weight: 600;
        }

        .btn-secondary {
            border: 1px solid #e5e7eb;
            padding: .5rem 1rem;
            border-radius: .5rem;
        }
    </style>

    <div class="p-6 bg-gray-50 min-h-screen">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6 pb-3">
            <h1 class="flex items-center gap-3 text-2xl font-extrabold text-indigo-600">
                <span class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-indigo-600"></i>
                </span>
                Manajemen Guru
            </h1>

            <button onclick="showModal('modalTambah')"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i> Tambah Guru
            </button>
        </div>

        {{-- SUMMARY --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
                <p class="text-sm text-gray-500">Guru Aktif</p>
                <p class="text-2xl font-bold text-green-600">{{ $totalGuruAktif }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-indigo-500">
                <p class="text-sm text-gray-500">Total Guru</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $totalGuru }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
                <p class="text-sm text-gray-500">Guru Nonaktif</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $totalGuruNonAktif }}</p>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="bg-white p-4 rounded-xl shadow mb-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-gray-600">Cari NIP / Nama</label>
                <input id="searchInput" value="{{ $request->search }}" class="w-full px-3 py-2 border rounded-lg"
                    placeholder="Ketik untuk mencari...">
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-600">Status Guru</label>
                <select id="statusSelect" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">Semua</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Cuti">Cuti</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-xl shadow">
            <table class="w-full text-sm text-gray-700">
                <thead class="bg-indigo-50">
                    <tr>
                        <th class="p-3 text-left">NIP</th>
                        <th class="p-3 text-left">Nama</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">No HP</th>
                        <th class="p-3 text-left">Kota</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($guruData as $g)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-3">{{ $g->nip }}</td>
                            <td class="p-3 font-semibold">{{ $g->nama_guru }}</td>
                            <td class="p-3">{{ $g->email ?? '-' }}</td>
                            <td class="p-3">{{ $g->no_hp ?? '-' }}</td>
                            <td class="p-3">{{ $g->kota_rmh ?? '-' }}</td>
                            <td class="p-3 text-center">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $g->status_guru == 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $g->status_guru }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <button class="text-indigo-600 font-semibold hover:text-indigo-800" onclick="openEdit(this)"
                                    data-id="{{ $g->id_guru }}" data-nip="{{ $g->nip }}"
                                    data-nama="{{ $g->nama_guru }}" data-jk="{{ $g->jenis_kelamin }}"
                                    data-email="{{ $g->email }}" data-nohp="{{ $g->no_hp }}"
                                    data-kota="{{ $g->kota_rmh }}" data-alamat="{{ $g->alamat_rmh }}"
                                    data-status="{{ $g->status_guru }}">
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
        <form method="POST" action="{{ route('admin.guru.store') }}"
            class="bg-white w-full max-w-2xl rounded-xl shadow-xl">
            @csrf

            {{-- HEADER --}}
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-user-plus text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-indigo-700">Tambah Guru</h3>
                </div>
                <button type="button" onclick="closeModal('modalTambah')">
                    <i class="fas fa-times text-gray-400 hover:text-red-500"></i>
                </button>
            </div>

            {{-- BODY --}}
            <div class="p-6 space-y-4">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">NIP</label>
                        <input name="nip" required class="input">
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Nama Lengkap</label>
                        <input name="nama_guru" required class="input">
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
                        <label class="text-sm font-semibold">Status Guru</label>
                        <select name="status_guru" class="input">
                            <option value="Aktif">Aktif</option>
                            <option value="Cuti">Cuti</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">No HP</label>
                        <input name="no_hp" class="input">
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Kota</label>
                        <input name="kota_rmh" class="input">
                    </div>
                </div>

                <div>
                    <label class="text-sm font-semibold">Email</label>
                    <div class="flex">
                        <input name="email_prefix" class="input rounded-r-none">
                        <span class="px-3 flex items-center bg-gray-100 border border-l-0 rounded-r-lg text-sm">
                            @mutiarabangsa.ac.id
                        </span>
                    </div>
                </div>

                <div>
                    <label class="text-sm font-semibold">Alamat</label>
                    <textarea name="alamat_rmh" rows="2" class="input"></textarea>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-xl">
                <button type="button" onclick="closeModal('modalTambah')" class="btn-secondary">Batal</button>
                <button class="btn-primary">Simpan Guru</button>
            </div>
        </form>
    </div>

    {{-- ================= MODAL EDIT ================= --}}
    <div id="modalEdit" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
        <form method="POST" action="{{ route('admin.guru.update') }}"
            class="bg-white w-full max-w-2xl rounded-xl shadow-xl">
            @csrf
            <input type="hidden" name="id_guru" id="edit_id">

            {{-- HEADER --}}
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-user-edit text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-indigo-700">Edit Guru</h3>
                </div>
                <button type="button" onclick="closeModal('modalEdit')">
                    <i class="fas fa-times text-gray-400 hover:text-red-500"></i>
                </button>
            </div>

            {{-- BODY --}}
            <div class="p-6 space-y-4">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">NIP</label>
                        <input id="edit_nip" name="nip" class="input">
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Nama Lengkap</label>
                        <input id="edit_nama" name="nama_guru" class="input">
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
                        <label class="text-sm font-semibold">Status Guru</label>
                        <select id="edit_status" name="status_guru" class="input">
                            <option value="Aktif">Aktif</option>
                            <option value="Cuti">Cuti</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">No HP</label>
                        <input id="edit_nohp" name="no_hp" class="input">
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Kota</label>
                        <input id="edit_kota" name="kota_rmh" class="input">
                    </div>
                </div>

                <div>
                    <label class="text-sm font-semibold">Email</label>
                    <div class="flex">
                        <input id="edit_email_prefix" name="email_prefix" class="input rounded-r-none">
                        <span class="px-3 flex items-center bg-gray-100 border border-l-0 rounded-r-lg text-sm">
                            @mutiarabangsa.ac.id
                        </span>
                    </div>
                </div>

                <div>
                    <label class="text-sm font-semibold">Alamat</label>
                    <textarea id="edit_alamat" name="alamat_rmh" rows="2" class="input"></textarea>
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

        function showModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.body.style.overflow = '';
        }

        function openEdit(el) {
            edit_id.value = el.dataset.id;
            edit_nip.value = el.dataset.nip;
            edit_nama.value = el.dataset.nama;
            edit_jk.value = el.dataset.jk;

            // ambil prefix email
            edit_email_prefix.value = el.dataset.email.split('@')[0];

            edit_nohp.value = el.dataset.nohp;
            edit_alamat.value = el.dataset.alamat;
            edit_kota.value = el.dataset.kota;
            edit_status.value = el.dataset.status;

            showModal('modalEdit');
        }

        function applyFilter() {
            const p = new URLSearchParams();
            if (searchInput.value) p.set('search', searchInput.value);
            if (statusSelect.value) p.set('status', statusSelect.value);
            window.location = '?' + p.toString();
        }
        searchInput.addEventListener('keyup', applyFilter);
        statusSelect.addEventListener('change', applyFilter);
    </script>
@endsection
