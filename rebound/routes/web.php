<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// id: Rute Autentikasi Pengguna (Login & Register Form) — hanya untuk guest,
//     user yang sudah login otomatis dialihkan ke dashboard oleh middleware 'guest'
// en: User Authentication Routes (Login & Register Form) — guests only,
//     authenticated users are redirected to the dashboard by the 'guest' middleware
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
});

// id: Autentikasi Firebase (Google Sign-In & Email/Password) - memverifikasi ID Token di server
// en: Firebase Authentication (Google Sign-In & Email/Password) - verifies ID Token on server
Route::post('/auth/firebase', [AuthController::class, 'loginWithFirebase'])->name('auth.firebase');

// id: Pengganti Bahasa (ID / EN) dengan persistensi sesi & respons JSON untuk AJAX
// en: Language Switcher (ID / EN) with session persistence & JSON response for AJAX
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    if (request()->wantsJson()) {
        return response()->json([
            'status' => 'success',
            'locale' => $locale,
            'messages' => trans('messages', [], $locale)
        ]);
    }
    return redirect()->back();
})->name('locale.switch');

use App\Models\AgentChatSession;
use App\Models\MockGdsBooking;

// id: Dashboard Utama & Asisten Penerbangan REBOUND (Wajib Login)
// en: Protected Dashboard & REBOUND Flight Assistant (Authentication Required)
Route::middleware('auth')->group(function () {
    
    Route::get('/', function () {
        $user = auth()->user();
        
        // id: Utamakan PNR dari sesi chat AI paling terbaru (berdasarkan updated_at)
        // en: Prioritize PNR from the user's most recent AI chat session (based on updated_at)
        $latestChatSession = AgentChatSession::where('user_id', $user->id)->latest('updated_at')->first();
        
        // Ambil PNR berstatus 'active' milik user sebagai cadangan jika belum ada chat
        $activePnr = $user->pnrs()->where('status', 'active')->first();

        // Kode PNR aktif utama yang akan langsung dibuka saat pertama kali dimuat/login
        $activePnrCode = $latestChatSession?->pnr_code ?? $activePnr?->pnr_code;

        // id: Daftar seluruh PNR asli milik user dari database — dipakai modal aktivasi
        //     untuk menampilkan tiket nyata, menggantikan skenario uji coba statis.
        // en: All real PNRs belonging to the user from the database — used by the activation
        //     modal to display actual tickets instead of static test scenarios.
        $userTickets = $user->pnrs()
            ->orderByRaw("status = 'active' desc")
            ->latest()
            ->get(['pnr_code', 'last_name', 'status']);


        // id: Sesi chat AI Agent milik pengguna dari database (agent_chat_sessions + chat_messages + mock_gds_bookings)
        // en: User's AI Agent chat sessions from database (agent_chat_sessions + chat_messages + mock_gds_bookings)
        $chatSessions = AgentChatSession::where('user_id', $user->id)
            ->with(['messages' => function ($query) {
                $query->latest('sent_at');
            }])
            ->latest('updated_at')
            ->get()
            ->map(function ($session) {
                $gdsBooking = MockGdsBooking::where('pnr_code', $session->pnr_code)->first();
                $latestMsg = $session->messages->first();

                return [
                    'id' => $session->id,
                    'pnr_code' => $session->pnr_code,
                    'context_summary' => $session->context_summary,
                    'last_message' => $latestMsg?->message_content ?? 'Belum ada pesan.',
                    'last_message_sender' => $latestMsg?->sender ?? 'system',
                    'last_message_time' => $latestMsg?->sent_at ? $latestMsg->sent_at->format('H:i') : $session->updated_at->format('H:i'),
                    'flight_number' => $gdsBooking?->flight_number ?? $session->pnr_code,
                    'from_code' => $gdsBooking?->from_code ?? 'CGK',
                    'to_code' => $gdsBooking?->to_code ?? 'SIN',
                    'departure_time' => $gdsBooking?->departure_time?->format('d M Y') ?? '',
                    'status' => $gdsBooking?->status ?? 'on_time',
                    'cabin_class' => $gdsBooking?->cabin_class ?? 'Economy',
                ];
            });

        // id: Notifikasi operasional milik user dari tabel notifications — dirender dropdown navbar,
        //     menggantikan tiga kartu alert statis yang dulu di-hardcode.
        // en: The user's operational notifications from the notifications table — rendered in the navbar
        //     dropdown, replacing the three static alert cards previously hardcoded.
        $notifications = $user->appNotifications()
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn ($notification) => [
                'id' => $notification->id,
                'pnr_code' => $notification->pnr_code,
                'type' => $notification->type,
                'title_id' => $notification->title_id,
                'title_en' => $notification->title_en,
                'message_id' => $notification->message_id,
                'message_en' => $notification->message_en,
                'is_read' => (bool) $notification->is_read,
                'created_at' => $notification->created_at->toIso8601String(),
            ]);

        return view('welcome', [
            'hasSetupPnr' => $activePnrCode !== null,
            'activePnrCode' => $activePnrCode,
            'userTickets' => $userTickets,
            'chatSessions' => $chatSessions,
            'notifications' => $notifications,
        ]);

    })->name('dashboard');

    Route::delete('/api/chat/session/{id}', [\App\Http\Controllers\ChatController::class, 'deleteSession']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


