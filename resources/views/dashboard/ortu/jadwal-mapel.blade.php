@extends('layouts.template') {{-- Sesuaikan dengan nama layout utama Anda --}}

@section('title', 'Jadwal Pelajaran Siswa')

@push('styles')
    @vite(['resources/css/announcement.css'])
@endpush

@push('scripts')
    @vite(['resources/js/announcement.js'])
@endpush

@section('content')
    @php
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    @endphp

    <div class="ann-layout ann-layout--ortu">
        <div class="ann-bar ann-bar--stack">
            <div>
                <h4 class="ann-title mb-1">Jadwal Pelajaran</h4>
                <p class="text-muted mb-0">Jadwal mingguan untuk <strong>{{ $siswa->nama ?? 'Siswa' }}</strong>.</p>
                <div class="ann-meta" style="margin-top:4px;">
                    @if (isset($siswaKelas))
                        <span class="ann-pill">Kelas: {{ $siswaKelas->kelas->nama_kelas ?? 'N/A' }}</span>
                    @endif
                    <span class="ann-pill">TA: {{ $selectedTahunAjaran ?? 'N/A' }}</span>
                    <span class="ann-pill">Semester: {{ $selectedSemester ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        @if ($error)
            <div class="ann-section">
                <div class="ann-error">{{ $error }}</div>
            </div>
        @endif

        <form action="{{ route('ortu.jadwal') }}" method="GET" class="ann-toolbar__row ann-toolbar__standalone ann-toolbar--inline">
            <div class="ann-toolbar__chunk ann-toolbar__chunk--search">
                <div class="ann-search__cluster">
                    <div class="ann-search__field">
                        <label class="ann-field__label">Filter Tingkat Kelas</label>
                        <select name="tingkat_kelas" id="tingkat_kelas" class="ann-input">
                            <option value="">Pilih Tingkat Kelas</option>
                            @foreach ($semuaTingkatKelas as $tingkat => $val)
                                <option value="{{ $tingkat }}" {{ $tingkat == $selectedTingkatKelas ? 'selected' : '' }}>
                                    Kelas {{ $tingkat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="ann-btn ann-btn--secondary">
                        Tampilkan
                    </button>
                </div>
            </div>
        </form>

        @foreach ($days as $hari)
            <div class="ann-section">
                <div class="ann-bar ann-bar--stack" style="margin-bottom:8px;">
                    <div class="ann-bar__left">
                        <h5 class="ann-title mb-0">{{ $hari }}</h5>
                        <div class="ann-meta">
                            <span class="ann-meta__dot"></span>
                            <span>Jadwal pelajaran</span>
                        </div>
                    </div>
                </div>
                <div class="ann-table ann-table--student-schedule">
                    <div class="ann-table__head">
                        <div>No</div>
                        <div>Tanggal</div>
                        <div>Jam</div>
                        <div>Mapel</div>
                        <div>Guru</div>
                        <div>Ruangan</div>
                    </div>
                    <div class="ann-table__body">
                        @forelse ($jadwalPerHari->get($hari, []) as $index => $jadwal)
                            <div class="ann-row">
                                <div class="cell">{{ $index + 1 }}</div>
                                <div class="cell">{{ \Carbon\Carbon::parse($jadwal->tanggal_jadwal)->translatedFormat('d M Y') }}</div>
                                <div class="cell">{{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }}</div>
                                <div class="cell ann-row__title">{{ $jadwal->penugasan->mapel->nama_mapel ?? 'N/A' }}</div>
                                <div class="cell">{{ $jadwal->penugasan->guru->nama_guru ?? 'N/A' }}</div>
                                <div class="cell">{{ $jadwal->ruangan->kode_ruangan ?? 'N/A' }}</div>
                            </div>
                        @empty
                            <div class="ann-empty">Tidak ada jadwal pelajaran di hari {{ $hari }} pada minggu ini berdasarkan filter.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
