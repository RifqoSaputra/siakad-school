@extends('layouts.template')

@section('title', 'Notifikasi Pengumuman')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Notifikasi Pengumuman</h4>
                <p class="text-muted mb-0">Semua notifikasi pengumuman yang pernah dikirim ke akun Anda.</p>
            </div>
            <form action="{{ route('pengumuman.notif.readAll') }}" method="POST">
                @csrf
                <button class="btn btn-outline-primary">
                    <i class="bi bi-check2-all"></i> Tandai semua sudah dibaca
                </button>
            </form>
        </div>

        <div class="list-group shadow-sm">
            @forelse ($notifications as $notification)
                <div class="list-group-item d-flex justify-content-between align-items-start {{ $notification->is_read ? '' : 'bg-light' }}">
                    <div class="ms-2 me-auto">
                        <div class="fw-semibold">
                            {{ $notification->pengumuman->judul ?? 'Pengumuman' }}
                            @unless ($notification->is_read)
                                <span class="badge bg-danger ms-2">Baru</span>
                            @endunless
                        </div>
                        <small class="text-muted">
                            {{ optional($notification->pengumuman)->target_role ? strtoupper($notification->pengumuman->target_role) . ' • ' : '' }}
                            {{ optional($notification->pengumuman)->created_at?->format('d M Y H:i') }}
                        </small>
                    </div>
                    <form action="{{ route('pengumuman.notif.read', $notification->id) }}" method="POST" class="ms-3">
                        @csrf
                        <button class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-check2"></i> Tandai dibaca
                        </button>
                    </form>
                </div>
            @empty
                <div class="list-group-item text-center text-muted">
                    Belum ada notifikasi pengumuman.
                </div>
            @endforelse
        </div>

        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    </div>
@endsection