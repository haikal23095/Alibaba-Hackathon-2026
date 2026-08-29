<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// id: Pusat notifikasi operasional — daftar alert milik user yang login (dari tabel notifications)
//     dan penandaan semua notifikasi sebagai sudah dibaca. Menggantikan kartu notifikasi statis
//     yang sebelumnya di-hardcode di navbar.
// en: Operational notification center — lists alerts belonging to the logged-in user (from the
//     notifications table) and marks them all as read. Replaces the static notification cards
//     previously hardcoded in the navbar.
class NotificationController extends Controller
{
    /**
     * id: GET /api/notifications — daftar notifikasi terbaru milik user yang login.
     * en: GET /api/notifications — the logged-in user's latest notifications.
     */
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->appNotifications()
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn ($notification) => $this->toPayload($notification));

        return response()->json([
            'status' => 'success',
            'data' => $notifications,
            'unread_count' => $notifications->where('is_read', false)->count(),
        ], 200);
    }

    /**
     * id: POST /api/notifications/read-all — tandai seluruh notifikasi user sebagai sudah dibaca.
     * en: POST /api/notifications/read-all — mark all of the user's notifications as read.
     */
    public function readAll(Request $request)
    {
        $markedRead = $request->user()
            ->appNotifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Semua notifikasi ditandai sudah dibaca.',
            'data' => ['marked_read' => $markedRead],
        ], 200);
    }

    // id: Bentuk payload notifikasi untuk frontend — sama dengan mapping route dashboard
    //     agar dropdown navbar bisa merender dari satu bentuk data yang konsisten.
    // en: Notification payload shape for the frontend — identical to the dashboard route mapping
    //     so the navbar dropdown can render from one consistent data shape.
    private function toPayload($notification): array
    {
        return [
            'id' => $notification->id,
            'pnr_code' => $notification->pnr_code,
            'type' => $notification->type,
            'title_id' => $notification->title_id,
            'title_en' => $notification->title_en,
            'message_id' => $notification->message_id,
            'message_en' => $notification->message_en,
            'is_read' => (bool) $notification->is_read,
            'created_at' => $notification->created_at->toIso8601String(),
        ];
    }
}
