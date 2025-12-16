@extends('layouts.template')

@section('title', 'Dashboard')

@push('styles')
    @vite(['resources/css/announcement.css'])
@endpush

@push('scripts')
    @vite(['resources/js/announcement.js'])
@endpush

@section('content')
    <div class="ann-layout ann-layout--guru-home">
        <div class="ann-bar ann-bar--stack">
            <div>
                <h4 class="ann-title">Dashboard Guru</h4>
                <p class="text-muted mb-0">Ringkasan pengumuman dan aktivitas mengajar.</p>
            </div>
        </div>

        <div class="ann-summary-grid">
            <a class="ann-summary-card ann-summary-card--stat" href="{{ route('guru.jadwal') }}">
                <div class="ann-summary-card__top">
                    <div class="ann-summary-card__label">Jadwal Hari Ini</div>
                    <span class="ann-summary-card__icon ann-summary-card__icon--subtle material-symbols-rounded">calendar_today</span>
                </div>
                <div class="ann-summary-card__value ann-summary-card__value--xl">{{ $jadwalHariIni->count() }}</div>
                <div class="ann-summary-card__divider"></div>
                <div class="ann-summary-card__meta">
                    <div class="ann-summary-card__meta-item">
                        <div class="ann-summary-card__meta-label">Absensi</div>
                        <div class="ann-summary-card__meta-value">Siap diisi</div>
                    </div>
                    <div class="ann-summary-card__meta-item">
                        <div class="ann-summary-card__meta-label">Lihat jadwal</div>
                        <div class="ann-summary-card__meta-value">Klik kartu</div>
                    </div>
                </div>
            </a>
            <a class="ann-summary-card ann-summary-card--stat" href="{{ route('guru.absensi.kelas') }}">
                <div class="ann-summary-card__top">
                    <div class="ann-summary-card__label">Total Siswa</div>
                    <span class="ann-summary-card__icon ann-summary-card__icon--subtle material-symbols-rounded">group</span>
                </div>
                <div class="ann-summary-card__value ann-summary-card__value--xl">{{ $totalSiswa ?? 0 }}</div>
                <div class="ann-summary-card__divider"></div>
                <div class="ann-summary-card__meta">
                    <div class="ann-summary-card__meta-item">
                        <div class="ann-summary-card__meta-label">Kelas</div>
                        <div class="ann-summary-card__meta-value">{{ $totalKelas ?? 0 }}</div>
                    </div>
                    <div class="ann-summary-card__meta-item">
                        <div class="ann-summary-card__meta-label">Absensi</div>
                        <div class="ann-summary-card__meta-value">Buka</div>
                    </div>
                </div>
            </a>
            <a class="ann-summary-card ann-summary-card--stat" href="{{ route('guru.absensi.kelas') }}">
                <div class="ann-summary-card__top">
                    <div class="ann-summary-card__label">Total Kelas</div>
                    <span class="ann-summary-card__icon ann-summary-card__icon--subtle material-symbols-rounded">home_work</span>
                </div>
                <div class="ann-summary-card__value ann-summary-card__value--xl">{{ $totalKelas ?? 0 }}</div>
                <div class="ann-summary-card__divider"></div>
                <div class="ann-summary-card__meta">
                    <div class="ann-summary-card__meta-item">
                        <div class="ann-summary-card__meta-label">Siswa</div>
                        <div class="ann-summary-card__meta-value">{{ $totalSiswa ?? 0 }}</div>
                    </div>
                    <div class="ann-summary-card__meta-item">
                        <div class="ann-summary-card__meta-label">Jadwal</div>
                        <div class="ann-summary-card__meta-value">{{ $jadwalHariIni->count() }}</div>
                    </div>
                </div>
            </a>
        </div>

        <div class="ann-summary-grid">
            @foreach ($ringkasanKelas as $item)
                <div class="ann-summary-card ann-summary-card--stat">
                    <div class="ann-summary-card__top">
                        <div class="ann-summary-card__label">{{ $loop->first ? 'Mata Pelajaran Aktif' : $item['nama'] }}</div>
                        <span class="ann-summary-card__icon ann-summary-card__icon--subtle material-symbols-rounded">{{ $item['ikon'] ?? 'menu_book' }}</span>
                    </div>
                    <div class="ann-summary-card__value ann-summary-card__value--xl">{{ $item['total'] }}</div>
                    <div class="ann-summary-card__divider"></div>
                    <div class="ann-summary-card__meta">
                        @foreach ($item['tingkatan'] as $tingkat)
                            <div class="ann-summary-card__meta-item">
                                <div class="ann-summary-card__meta-label">{{ $tingkat['label'] }}</div>
                                <div class="ann-summary-card__meta-value">{{ $tingkat['jumlah'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="ann-section">
            <div class="ann-bar ann-bar--stack">
                <div class="ann-bar__left">
                    <h5 class="ann-title mb-0">Jadwal Mengajar &amp; Input Absensi</h5>
                    <div class="ann-meta">
                        <span class="ann-meta__dot"></span>
                        <span>Data dummy, sesuaikan dengan jadwal_mapel</span>
                    </div>
                </div>
                <div class="ann-toolbar__actions">
                    <a href="{{ route('guru.absensi.kelas') }}" class="ann-btn ann-btn--primary">
                        Isi Absen <span class="material-symbols-rounded">edit_calendar</span>
                    </a>
                </div>
            </div>
            <div class="ann-table ann-table--schedule">
                <div class="ann-table__head">
                    <div>Waktu</div>
                    <div>Mata Pelajaran</div>
                    <div>Kelas</div>
                    <div>Ruang</div>
                    <div class="text-right">Aksi</div>
                </div>
                <div class="ann-table__body">
                    @forelse ($jadwalHariIni ?? [] as $jadwal)
                        <div class="ann-row">
                            <div class="cell">{{ $jadwal['jam_mulai'] }} - {{ $jadwal['jam_selesai'] }}</div>
                            <div class="cell ann-row__title">{{ $jadwal['mapel'] }}</div>
                            <div class="cell">{{ $jadwal['kelas'] }}</div>
                            <div class="cell">{{ $jadwal['ruang'] }}</div>
                            <div class="cell text-right">
                                <a href="{{ route('guru.absensi.kelas') }}" class="ann-btn ann-btn--secondary">
                                    Isi Absen <span class="material-symbols-rounded">checklist</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="ann-empty">Belum ada jadwal hari ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
