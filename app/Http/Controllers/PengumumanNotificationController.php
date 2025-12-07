<?php

namespace App\Http\Controllers;

use App\Models\SIAKAD\SCHOOL\PengumumanUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengumumanNotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $notifications = $user->pengumumanNotifications()
            ->with('pengumuman')
            ->latest()
            ->paginate(10);

        if ($request->wantsJson()) {
            return response()->json($notifications);
        }

        return view('dashboard.notifikasi.pengumuman', compact('notifications'));
    }

    public function markAsRead($id, Request $request)
    {
        $userId = Auth::user()->users_id;
        $notification = PengumumanUser::where('id', $id)
            ->where('users_id', $userId)
            ->firstOrFail();

        if (!$notification->is_read) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Notifikasi ditandai dibaca.']);
        }

        return back()->with('success', 'Notifikasi pengumuman ditandai sudah dibaca.');
    }

    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        $user->pengumumanNotifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Semua notifikasi ditandai dibaca.']);
        }

        return back()->with('success', 'Semua notifikasi pengumuman ditandai sudah dibaca.');
    }
}
