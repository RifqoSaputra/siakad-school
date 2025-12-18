@extends('layouts.template')

@section('title', 'Dashboard')

@push('scripts')
    @vite(['resources/js/app-ui.js'])
@endpush

@section('content')
    @php
        use Carbon\Carbon;
    @endphp

    <div class="ann-layout ann-layout--ortu">
        <div class="ann-bar ann-bar--stack">
            <div>
                <h4 class="ann-title mb-1">Dashboard Orang Tua</h4>
                <p class="text-muted mb-0">Semua informasi anak ditampilkan langsung di sini.</p>
            </div>
        </div>

        @if (($anakList ?? collect())->isEmpty())
            <div class="ann-section">
                <h5 class="ann-title mb-2">Data anak belum tersedia</h5>
                <p class="text-muted mb-3">Pastikan akun Orang Tua sudah terhubung dengan data siswa di sistem.</p>
                <a href="{{ route('ortu.dashboard') }}" class="ann-btn ann-btn--primary" style="width:auto; min-width:0;">
                    Kembali ke Dashboard <span class="material-symbols-rounded">arrow_back</span>
                </a>
            </div>
        @else
            @php
                $kelasAktif = $siswaAktif->kelas_aktif ?? null;
                $usia = $siswaAktif?->tgl_lahir ? Carbon::parse($siswaAktif->tgl_lahir)->age : null;
            @endphp

            <div class="ann-summary-grid">
                <div class="ann-summary-card ann-summary-card--stat">
                    <div class="ann-summary-card__top">
                        <div class="ann-summary-card__label">Anak Aktif</div>
                        <span class="ann-summary-card__icon ann-summary-card__icon--subtle material-symbols-rounded">child_care</span>
                    </div>
                    <div class="ann-summary-card__value ann-summary-card__value--xl">{{ $siswaAktif->nama ?? 'Nama tidak tersedia' }}</div>
                    <div class="ann-summary-card__divider"></div>
                    <div class="ann-summary-card__meta">
                        <div class="ann-summary-card__meta-item">
                            <div class="ann-summary-card__meta-label">NIS</div>
                            <div class="ann-summary-card__meta-value">{{ $siswaAktif->nis ?? 'N/A' }}</div>
                        </div>
                        <div class="ann-summary-card__meta-item">
                            <div class="ann-summary-card__meta-label">Usia</div>
                            <div class="ann-summary-card__meta-value">{{ $usia ? $usia . ' th' : 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                <div class="ann-summary-card ann-summary-card--stat">
                    <div class="ann-summary-card__top">
                        <div class="ann-summary-card__label">Kelas Aktif</div>
                        <span class="ann-summary-card__icon ann-summary-card__icon--subtle material-symbols-rounded">class</span>
                    </div>
                    <div class="ann-summary-card__value ann-summary-card__value--xl">{{ $kelasAktif->nama_kelas ?? 'Belum ada kelas' }}</div>
                    <div class="ann-summary-card__divider"></div>
                    <div class="ann-summary-card__meta">
                        <div class="ann-summary-card__meta-item">
                            <div class="ann-summary-card__meta-label">Tahun Ajaran</div>
                            <div class="ann-summary-card__meta-value">{{ $kelasAktif->tahun_ajaran ?? '-' }}</div>
                        </div>
                        <div class="ann-summary-card__meta-item">
                            <div class="ann-summary-card__meta-label">Semester</div>
                            <div class="ann-summary-card__meta-value">{{ $kelasAktif->semester ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ann-section">
                <div class="ann-bar ann-bar--stack">
                    <div class="ann-bar__left">
                        <h5 class="ann-title mb-0">Biodata & Kontak</h5>
                        <div class="ann-meta">
                            <span class="ann-meta__dot"></span>
                            <span>Kota: {{ $siswaAktif->kota_rmh ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                <div class="ann-field-inline">
                    <div class="ann-field">
                        <div class="ann-field__label">Nama Lengkap</div>
                        <div class="cell">{{ $siswaAktif->nama ?? '-' }}</div>
                    </div>
                    <div class="ann-field">
                        <div class="ann-field__label">NIS</div>
                        <div class="cell">{{ $siswaAktif->nis ?? '-' }}</div>
                    </div>
                    <div class="ann-field">
                        <div class="ann-field__label">Agama</div>
                        <div class="cell">{{ $siswaAktif->agama ?? '-' }}</div>
                    </div>
                    <div class="ann-field">
                        <div class="ann-field__label">Tanggal Lahir</div>
                        <div class="cell">{{ $siswaAktif->tgl_lahir ? Carbon::parse($siswaAktif->tgl_lahir)->translatedFormat('d F Y') : '-' }}</div>
                    </div>
                    <div class="ann-field">
                        <div class="ann-field__label">Usia</div>
                        <div class="cell">{{ $usia ? $usia . ' tahun' : '-' }}</div>
                    </div>
                    <div class="ann-field">
                        <div class="ann-field__label">Kelas</div>
                        <div class="cell">{{ $kelasAktif->nama_kelas ?? '-' }}</div>
                    </div>
                    <div class="ann-field" style="grid-column:1 / -1;">
                        <div class="ann-field__label">Alamat</div>
                        <div class="cell">{{ $siswaAktif->alamat_rmh ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="ann-section">
                <div class="ann-bar ann-bar--stack">
                    <div class="ann-bar__left">
                        <h5 class="ann-title mb-0">Statistik Kehadiran</h5>
                    </div>
                    <div class="ann-toolbar__actions">
                        <a href="{{ route('ortu.absensi') }}" class="ann-btn ann-btn--secondary" style="width:auto; min-width:0;">
                            Lihat Detail <span class="material-symbols-rounded">open_in_new</span>
                        </a>
                    </div>
                </div>
                <div class="ann-toolbar__actions" style="flex-wrap:wrap; gap:12px;">
                    <span class="ann-pill">Hadir <strong style="font-weight:700;">95%</strong></span>
                    <span class="ann-pill">Izin <strong style="font-weight:700;">3%</strong></span>
                    <span class="ann-pill">Sakit <strong style="font-weight:700;">1%</strong></span>
                    <span class="ann-pill">Alpha <strong style="font-weight:700;">1%</strong></span>
                </div>
            </div>
        @endif
    </div>
@endsection
