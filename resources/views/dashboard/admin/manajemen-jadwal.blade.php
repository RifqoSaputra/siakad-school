@extends('layouts.template')

@section('title', 'Manajemen Jadwal Pelajaran')

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
                    <i class="fas fa-calendar-alt text-indigo-600"></i>
                </span>
                Manajemen Jadwal Pelajaran
            </h1>

            <button onclick="showModal('modalTambah')"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i> Tambah Jadwal
            </button>
        </div>

        {{-- ================= SUMMARY ================= --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
                <p class="text-sm text-gray-500">Jadwal Aktif</p>
                <p class="text-2xl font-bold text-green-600">{{ $jadwalAktif }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-indigo-500">
                <p class="text-sm text-gray-500">Total Jadwal</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $totalJadwal }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
                <p class="text-sm text-gray-500">Jadwal Nonaktif</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $jadwalNonaktif }}</p>
            </div>
        </div>

        {{-- ================= FILTER ================= --}}
        <form id="filterForm" method="GET">
            <div class="bg-white p-4 rounded-xl shadow mb-5 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="text-sm font-semibold text-gray-600">Cari Jadwal</label>
                    <input type="text" name="search" value="{{ $request->search }}"
                        class="w-full px-3 py-2 border rounded-lg" placeholder="Kelas / Mapel / Guru" data-filter>
                </div>

                <div>
                    <label class="text-sm font-semibold text-gray-600">Filter Kelas</label>
                    <select name="kelas_id" class="w-full px-3 py-2 border rounded-lg" data-filter>
                        <option value="">Semua Kelas</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->kelas_id }}" {{ $request->kelas_id == $k->kelas_id ? 'selected' : '' }}>
                                {{ $k->nama_kelas_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold text-gray-600">Filter Mapel</label>
                    <select name="mapel_id" class="w-full px-3 py-2 border rounded-lg" data-filter>
                        <option value="">Semua Mapel</option>
                        @foreach ($mapel as $m)
                            <option value="{{ $m->mapel_id }}" {{ $request->mapel_id == $m->mapel_id ? 'selected' : '' }}>
                                {{ $m->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold text-gray-600">Status</label>
                    <select name="status" class="w-full px-3 py-2 border rounded-lg" data-filter>
                        <option value="">Semua</option>
                        <option value="1" {{ $request->status === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ $request->status === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>
        </form>

        {{-- ================= TABLE ================= --}}
        <div class="bg-white rounded-xl shadow">
            <table class="w-full text-sm text-gray-700">
                <thead class="bg-indigo-50">
                    <tr>
                        <th class="p-3">Kelas</th>
                        <th class="p-3">Mapel</th>
                        <th class="p-3">Guru</th>
                        <th class="p-3">Hari</th>
                        <th class="p-3">Jam</th>
                        <th class="p-3">Ruangan</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jadwal as $j)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-3 font-semibold">
                                {{ $j->kelas->nama_kelas_lengkap ?? '-' }}
                            </td>
                            <td class="p-3">
                                {{ $j->penugasan->mapel->nama_mapel ?? '-' }}
                            </td>
                            <td class="p-3">
                                {{ $j->penugasan->guru->nama_guru ?? '-' }}
                            </td>
                            <td class="p-3">{{ $j->hari }}</td>
                            <td class="p-3">
                                {{ $j->jam_mulai }} - {{ $j->jam_selesai }}
                            </td>
                            <td class="p-3">
                                {{ $j->ruangan->kode_ruangan ?? '-' }}
                            </td>
                            <td class="p-3 text-center">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $j->status ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $j->status ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <button class="text-indigo-600 font-semibold" onclick="openEdit(this)"
                                    data-id="{{ $j->jadwal_mapel_id }}" data-gurumapel="{{ $j->guru_mapel_id }}"
                                    data-kelas="{{ $j->kelas_id }}" data-ruangan="{{ $j->ruangan_id }}"
                                    data-hari="{{ $j->hari }}" data-mulai="{{ $j->jam_mulai }}"
                                    data-selesai="{{ $j->jam_selesai }}" data-status="{{ $j->status }}">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-gray-500 italic">
                                Belum ada data jadwal pelajaran
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- PAGINATION --}}
        <div class="p-4">
            {{ $jadwal->links() }}
        </div>
    </div>

    {{-- ================= MODAL TAMBAH ================= --}}
    <div id="modalTambah" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">

        <form method="POST" action="{{ route('admin.jadwal.store') }}"
            class="bg-white w-full max-w-3xl rounded-xl shadow-xl">
            @csrf

            {{-- HEADER --}}
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-indigo-700">Tambah Jadwal Pelajaran</h3>
                </div>
                <button type="button" onclick="closeModal('modalTambah')">
                    <i class="fas fa-times text-gray-400 hover:text-red-500"></i>
                </button>
            </div>

            {{-- BODY --}}
            <div class="p-6 space-y-4">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Kelas</label>
                        <select name="kelas_id" class="input">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->kelas_id }}">{{ $k->nama_kelas_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold">Mapel & Guru</label>
                        <select name="guru_mapel_id" class="input">
                            <option value="">-- Pilih Mapel --</option>
                            @foreach ($guruMapel as $gm)
                                <option value="{{ $gm->guru_mapel_id }}">
                                    {{ $gm->mapel->nama_mapel }} - {{ $gm->guru->nama_guru }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Hari</label>
                        <select name="hari" class="input">
                            @foreach (['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'] as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="input">
                    </div>

                    <div>
                        <label class="text-sm font-semibold">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="input">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Ruangan</label>
                        <select name="ruangan_id" class="input">
                            @foreach ($ruangan as $r)
                                <option value="{{ $r->ruangan_id }}">{{ $r->kode_ruangan }}</option>
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
            </div>

            {{-- FOOTER --}}
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalTambah')" class="btn-secondary">Batal</button>
                <button class="btn-primary">Simpan Jadwal</button>
            </div>
        </form>
    </div>

    {{-- ================= MODAL EDIT ================= --}}
    <div id="modalEdit" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">

        <form method="POST" action="{{ route('admin.jadwal.update') }}"
            class="bg-white w-full max-w-3xl rounded-xl shadow-xl">
            @csrf
            <input type="hidden" name="jadwal_mapel_id" id="edit_id">

            <div class="flex items-center justify-between px-6 py-4 border-b">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-indigo-700">Edit Jadwal Pelajaran</h3>
                </div>
                <button type="button" onclick="closeModal('modalEdit')">
                    <i class="fas fa-times text-gray-400 hover:text-red-500"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Kelas</label>
                        <select name="kelas_id" class="input">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->kelas_id }}">{{ $k->nama_kelas_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold">Mapel & Guru</label>
                        <select name="guru_mapel_id" class="input">
                            <option value="">-- Pilih Mapel --</option>
                            @foreach ($guruMapel as $gm)
                                <option value="{{ $gm->guru_mapel_id }}">
                                    {{ $gm->mapel->nama_mapel }} - {{ $gm->guru->nama_guru }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Hari</label>
                        <select name="hari" class="input">
                            @foreach (['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'] as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="input">
                    </div>

                    <div>
                        <label class="text-sm font-semibold">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="input">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Ruangan</label>
                        <select name="ruangan_id" class="input">
                            @foreach ($ruangan as $r)
                                <option value="{{ $r->ruangan_id }}">{{ $r->kode_ruangan }}</option>
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
            </div>

            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalEdit')" class="btn-secondary">Batal</button>
                <button class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <script>
        const edit_id = document.getElementById('edit_id');
        const edit_gurumapel = document.querySelector('#modalEdit select[name="guru_mapel_id"]');
        const edit_kelas = document.querySelector('#modalEdit select[name="kelas_id"]');
        const edit_ruangan = document.querySelector('#modalEdit select[name="ruangan_id"]');
        const edit_hari = document.querySelector('#modalEdit select[name="hari"]');
        const edit_mulai = document.querySelector('#modalEdit input[name="jam_mulai"]');
        const edit_selesai = document.querySelector('#modalEdit input[name="jam_selesai"]');
        const edit_status = document.querySelector('#modalEdit select[name="status"]');
        const filterForm = document.getElementById('filterForm');
        const filterInputs = document.querySelectorAll('[data-filter]');

        let typingTimer;

        filterInputs.forEach(el => {

            // Dropdown langsung submit
            if (el.tagName === 'SELECT') {
                el.addEventListener('change', () => {
                    filterForm.submit();
                });
            }

            // Input search pakai debounce
            if (el.tagName === 'INPUT') {
                el.addEventListener('keyup', () => {
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => {
                        filterForm.submit();
                    }, 500); // delay 0.5 detik
                });
            }
        });

        function showModal(id) {
            document.getElementById(id).classList.remove('hidden')
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden')
        }

        function openEdit(el) {
            edit_id.value = el.dataset.id
            edit_gurumapel.value = el.dataset.gurumapel
            edit_kelas.value = el.dataset.kelas
            edit_ruangan.value = el.dataset.ruangan
            edit_hari.value = el.dataset.hari
            edit_mulai.value = el.dataset.mulai
            edit_selesai.value = el.dataset.selesai
            edit_status.value = el.dataset.status
            showModal('modalEdit')
        }
    </script>
@endsection
