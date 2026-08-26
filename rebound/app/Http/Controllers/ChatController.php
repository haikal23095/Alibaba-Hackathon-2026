<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgentChatSession;
use App\Models\ChatMessage;
use App\Models\UserPnr;
use Illuminate\Support\Facades\Http;
use Exception;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        // 1. Validasi input dari frontend
        $request->validate([
            'message' => 'required|string',
            // id: Kunci PNR/tiket bisa 5-6 karakter (mis. 'GA826'), bukan harus tepat 6
            // en: PNR/ticket key may be 5-6 chars (e.g. 'GA826'), not strictly 6
            'pnr' => 'required|string|max:10'
        ]);

        $user = $request->user();
        $userMessage = $request->input('message');
        $pnrCode = strtoupper($request->input('pnr'));

        try {
            // 2. Pastikan tiket (PNR) ini benar-benar milik user yang sedang login
            $validPnr = UserPnr::where('user_id', $user->id)
                               ->where('pnr_code', $pnrCode)
                               ->first();

            if (!$validPnr) {
                return response()->json([
                    'type' => 'text',
                    'replyId' => 'Maaf, PNR ini tidak ditemukan di akun Anda.',
                    'replyEn' => 'Sorry, this PNR is not found in your account.'
                ], 403);
            }

            // 3. Cari atau buat sesi obrolan (Chat Session) untuk PNR ini
            $session = AgentChatSession::firstOrCreate(
                ['user_id' => $user->id, 'pnr_code' => $pnrCode],
                ['context_summary' => 'Sesi obrolan baru untuk PNR ' . $pnrCode]
            );

            // 4. Simpan pesan pengguna (User) ke database
            ChatMessage::create([
                'session_id' => $session->id,
                'sender' => 'user',
                'message_content' => $userMessage,
            ]);

            // 5. HUBUNGI AI MODEL (Qwen / Qoder via HTTP Request)
            // Di sini kamu menembak API dari Alibaba Cloud Model Studio atau arsitektur Qoder kalian.
            // Contoh payload untuk dikirim ke agen AI:
            /*
            $aiResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('QWEN_API_KEY'),
            ])->post('https://api.qoder.cloud/v1/chat', [
                'prompt' => $userMessage,
                'context' => ['pnr' => $pnrCode, 'history' => $session->messages()->latest()->take(5)->get()]
            ]);
            $aiData = $aiResponse->json();
            */

            // SIMULASI RESPON AI (Untuk testing sebelum API Qwen aktif)
            // Hapus blok ini nanti setelah fungsi Http::post di atas sudah berjalan
            $aiData = $this->simulateAgenticAI($userMessage);

            // 6. Simpan balasan AI ke database
            // id: Enum kolom sender hanya 'user'|'agent'|'system' — balasan AI disimpan sebagai 'agent'
            // en: The sender column enum only allows 'user'|'agent'|'system' — AI replies are stored as 'agent'
            ChatMessage::create([
                'session_id' => $session->id,
                'sender' => 'agent',
                'message_content' => $aiData['replyId'], // Simpan teks utama
                'dynamic_ui_payload' => [
                    'type' => $aiData['type'],
                    'showTicketPolicy' => $aiData['showTicketPolicy'] ?? false,
                    'showRecommendation' => $aiData['showRecommendation'] ?? false,
                ]
            ]);

            // 7. Kembalikan format JSON persis seperti yang diharapkan oleh Alpine.js
            return response()->json($aiData, 200);

        } catch (Exception $e) {
            return response()->json([
                'type' => 'text',
                'replyId' => 'Sistem AI sedang mengalami gangguan: ' . $e->getMessage(),
                'replyEn' => 'AI System is experiencing issues: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * id: Mengambil riwayat percakapan tersimpan untuk PNR aktif milik user yang login,
     *     agar chat tetap ada setelah halaman di-refresh.
     * en: Retrieves the stored conversation history for the logged-in user's active PNR,
     *     so the chat persists after a page refresh.
     */
    public function history(Request $request)
    {
        $request->validate(['pnr' => 'required|string|max:10']);

        $user = $request->user();
        $pnrCode = strtoupper(trim($request->query('pnr')));

        // id: Pastikan PNR benar-benar milik user yang sedang login
        // en: Make sure the PNR really belongs to the logged-in user
        $validPnr = UserPnr::where('user_id', $user->id)
                           ->where('pnr_code', $pnrCode)
                           ->first();

        if (!$validPnr) {
            return response()->json(['messages' => []], 200);
        }

        $session = AgentChatSession::where('user_id', $user->id)
                                   ->where('pnr_code', $pnrCode)
                                   ->first();

        if (!$session) {
            return response()->json(['messages' => []], 200);
        }

        $messages = $session->messages()
            ->orderBy('sent_at')
            ->orderBy('id')
            ->get()
            ->map(fn (ChatMessage $m) => [
                // id: 'agent' di database dirender sebagai 'ai' di frontend Alpine
                // en: 'agent' in the database is rendered as 'ai' in the Alpine frontend
                'sender' => $m->sender === 'agent' ? 'ai' : $m->sender,
                'text' => $m->message_content,
                'type' => $m->dynamic_ui_payload['type'] ?? 'text',
                'showTicketPolicy' => $m->dynamic_ui_payload['showTicketPolicy'] ?? false,
                'showRecommendation' => $m->dynamic_ui_payload['showRecommendation'] ?? false,
                'time' => optional($m->sent_at)->format('H:i'),
            ]);

        return response()->json(['messages' => $messages], 200);
    }

    /**
     * Fungsi helper sementara untuk mensimulasikan logika penalaran AI.
     * Nantinya, fungsi ini akan digantikan sepenuhnya oleh output dari Qwen.
     */
    private function simulateAgenticAI($message)
    {
        $lowerMsg = strtolower($message);

        if (str_contains($lowerMsg, 'aturan') || str_contains($lowerMsg, 'policy')) {
            return [
                'type' => 'policy_card',
                'replyId' => 'Berdasarkan analisis saya terhadap aturan tiket Anda, berikut rinciannya.',
                'replyEn' => 'Based on my analysis of your ticket rules, here are the details.',
                'showTicketPolicy' => true
            ];
        }

        if (str_contains($lowerMsg, 'opsi') || str_contains($lowerMsg, 'lain') || str_contains($lowerMsg, 'jadwal')) {
            return [
                'type' => 'options_list',
                'replyId' => 'Berikut daftar penerbangan alternatif dari sistem GDS Atlas yang tersedia hari ini.',
                'replyEn' => 'Here are the alternative flights from the GDS Atlas system available today.'
            ];
        }

        return [
            'type' => 'text',
            'replyId' => 'Pesan diterima. Saya dapat membantu mencari rute atau mengecek status penerbangan Anda.',
            'replyEn' => 'Message received. I can help find routes or check your flight status.'
        ];
    }
}