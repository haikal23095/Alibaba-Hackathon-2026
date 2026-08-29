<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgentChatSession;
use App\Models\ChatMessage;
use App\Models\UserPnr;
use App\Models\MockGdsBooking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ChatController extends Controller
{
    /**
     * Endpoint utama pengiriman pesan chat ke AI Agent (Qwen Model Studio / Qoder Gateway).
     */
    public function sendMessage(Request $request)
    {
        // 1. Validasi input dari frontend
        $request->validate([
            'message' => 'required|string',
            'pnr' => 'required|string|max:10'
        ]);

        $user = $request->user();
        $userMessage = trim($request->input('message'));
        $pnrCode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $request->input('pnr')));

        try {
            // 2. Pastikan PNR milik pengguna yang sedang login
            $validPnr = UserPnr::where('user_id', $user->id)
                               ->where('pnr_code', $pnrCode)
                               ->first();

            if (!$validPnr) {
                return response()->json([
                    'type' => 'text',
                    'replyId' => 'Maaf, kode PNR ini tidak terverifikasi di akun Anda.',
                    'replyEn' => 'Sorry, this PNR code is not verified in your account.',
                    'showTicketPolicy' => false,
                    'showRecommendation' => false,
                ], 403);
            }

            // 3. Cari atau buat sesi obrolan (Chat Session) untuk PNR ini
            $session = AgentChatSession::firstOrCreate(
                ['user_id' => $user->id, 'pnr_code' => $pnrCode],
                ['context_summary' => 'Sesi obrolan penerbangan untuk PNR ' . $pnrCode]
            );

            // 4. Simpan pesan pengguna (User) ke database
            ChatMessage::create([
                'session_id' => $session->id,
                'sender' => 'user',
                'message_content' => $userMessage,
                'sent_at' => now(),
            ]);

            // 5. Kumpulkan Konteks Penerbangan Resmi dari GDS (MockGdsBooking)
            $gdsBooking = MockGdsBooking::where('pnr_code', $pnrCode)->first();
            $flightContext = [
                'pnr' => $pnrCode,
                'passenger_last_name' => $validPnr->last_name ?? $user->name,
                'flight_number' => $gdsBooking?->flight_number ?? $pnrCode,
                'from_code' => $gdsBooking?->from_code ?? 'CGK',
                'to_code' => $gdsBooking?->to_code ?? 'SIN',
                'route' => ($gdsBooking?->from_code ?? 'CGK') . ' ➔ ' . ($gdsBooking?->to_code ?? 'SIN'),
                'departure_time' => $gdsBooking?->departure_time?->format('Y-m-d H:i') ?? '2026-08-28 08:25',
                'cabin_class' => $gdsBooking?->cabin_class ?? 'Economy',
                'status' => $gdsBooking?->status ?? 'delayed',
                'waiver_eligible' => in_array($gdsBooking?->status, ['delayed', 'cancelled']),
                'waiver_rule' => 'Rule 72A (Penalty Waiver / $0 Fee Rebooking)',
            ];

            // 6. Ambil Riwayat Percakapan Terakhir (Ingatan Konteks untuk LLM)
            $chatHistory = $session->messages()
                ->orderBy('sent_at', 'desc')
                ->take(6)
                ->get()
                ->reverse()
                ->map(fn (ChatMessage $m) => [
                    'role' => $m->sender === 'user' ? 'user' : 'assistant',
                    'content' => $m->message_content,
                ])
                ->values()
                ->toArray();

            // 7. HUBUNGI AI MODEL QWEN (Alibaba Cloud Model Studio / DashScope / Qoder API)
            $aiData = $this->callQwenLLM($userMessage, $flightContext, $chatHistory);

            // 8. Simpan balasan AI ke database
            ChatMessage::create([
                'session_id' => $session->id,
                'sender' => 'agent',
                'message_content' => $aiData['replyId'],
                'dynamic_ui_payload' => [
                    'type' => $aiData['type'],
                    'showTicketPolicy' => $aiData['showTicketPolicy'] ?? false,
                    'showRecommendation' => $aiData['showRecommendation'] ?? false,
                ],
                'sent_at' => now(),
            ]);

            // Update timestamp & context summary sesi chat
            $session->touch();

            // 9. Kembalikan format JSON persis seperti yang diharapkan oleh Alpine.js
            return response()->json($aiData, 200);

        } catch (Exception $e) {
            Log::error('ChatController error: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'type' => 'text',
                'replyId' => 'Sistem AI sedang mengalami gangguan sementara: ' . $e->getMessage(),
                'replyEn' => 'AI System is experiencing temporary issues: ' . $e->getMessage(),
                'showTicketPolicy' => false,
                'showRecommendation' => false,
            ], 500);
        }
    }

    /**
     * id: Mengambil riwayat percakapan tersimpan untuk PNR aktif milik user yang login.
     * en: Retrieves the stored conversation history for the logged-in user's active PNR.
     */
    public function history(Request $request)
    {
        $request->validate(['pnr' => 'required|string|max:10']);

        $user = $request->user();
        $pnrCode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $request->query('pnr')));

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
     * Merumuskan System Prompt (Instruksi Dasar AI) yang ketat.
     */
    private function getSystemPrompt(): string
    {
        return <<<PROMPT
Anda adalah REBOUND, Asisten Cerdas Penerbangan Enterprise tingkat lanjut yang dibangun untuk Alibaba Cloud × Atlas Agentic AI. Anda membantu penumpang dengan status penerbangan, aturan tiket (Waiver 72A), penanganan krisis delay/pembatalan, dan proses rebooking GDS Atlas.

ATURAN WAJIB (STRICT RULES):
1. Anda DILARANG keras berhalusinasi tentang jadwal penerbangan, waktu delay, atau kebijakan tiket. Gunakan HANYA data yang diberikan di dalam konteks penerbangan (flight_context) dan riwayat obrolan (chat_history).
2. Anda HARUS SELALU merespons HANYA menggunakan format JSON murni yang valid tanpa pembungkus Markdown seperti ```json atau ``` dan TANPA teks tambahan apapun di luar objek JSON.
3. Objek JSON output HARUS mengikuti skema berikut secara presisi:
{
  "type": "text" | "policy_card" | "options_list" | "disruption_alert" | "success_card",
  "replyId": "Pesan balasan dalam Bahasa Indonesia yang sopan, profesional, dan informatif",
  "replyEn": "Response message in English, polite, professional, and clear",
  "showTicketPolicy": boolean,
  "showRecommendation": boolean
}
4. Penentuan nilai "type":
   - Gunakan "policy_card" jika pengguna menanyakan aturan tiket, denda, refund, fee, atau waiver 72A (set showTicketPolicy = true).
   - Gunakan "options_list" jika pengguna meminta jadwal penerbangan alternatif, opsi lain, atau rebooking (set showRecommendation = true).
   - Gunakan "disruption_alert" jika menginformasikan delay parah, pembatalan, atau kompensasi krisis.
   - Gunakan "success_card" jika mengonfirmasi keberhasilan rebooking atau klaim voucher.
   - Gunakan "text" untuk pertanyaan umum, salam, atau info penerbangan standar.
PROMPT;
    }

    /**
     * Mengirimkan HTTP Request ke Qwen API (Alibaba Cloud Model Studio / DashScope / Qoder API)
     * dengan fallback otomatis ke mesin simulasi agen jika API Key belum dikonfigurasi.
     */
    private function callQwenLLM(string $userMessage, array $flightContext, array $chatHistory): array
    {
        $apiKey = config('services.qwen.api_key') 
            ?: env('QWEN_API_KEY', env('DASHSCOPE_API_KEY', env('QODER_API_KEY')));

        $endpoint = config('services.qwen.endpoint') 
            ?: env('QWEN_API_ENDPOINT', 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1/chat/completions');

        $model = config('services.qwen.model') 
            ?: env('QWEN_MODEL_NAME', 'qwen-max');

        // Jika API Key dikonfigurasi, lakukan HTTP call nyata ke Qwen LLM
        if (!empty($apiKey)) {
            try {
                $messagesPayload = [
                    ['role' => 'system', 'content' => $this->getSystemPrompt()],
                    [
                        'role' => 'system', 
                        'content' => "DATA PENERBANGAN RESMI GDS ATLAS (flight_context):\n" . json_encode($flightContext, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                    ]
                ];

                foreach ($chatHistory as $prevMsg) {
                    $messagesPayload[] = $prevMsg;
                }

                $messagesPayload[] = ['role' => 'user', 'content' => $userMessage];

                // id: Timeout 45 detik — model thinking Qwen bisa lambat pada prompt besar; di bawah itu berisiko fallback ke simulasi
                // en: 45s timeout — Qwen thinking models can be slow on large prompts; less risks falling back to simulation
                $response = Http::timeout(45)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . trim($apiKey),
                        'Content-Type' => 'application/json',
                    ])
                    ->post($endpoint, [
                        'model' => $model,
                        'messages' => $messagesPayload,
                        'temperature' => 0.3,
                        'response_format' => ['type' => 'json_object']
                    ]);

                if ($response->successful()) {
                    $rawContent = $response->json('choices.0.message.content');
                    $parsed = $this->parseStrictJson($rawContent);
                    if ($parsed) {
                        return $parsed;
                    }
                } else {
                    Log::warning('Qwen API Error Response: ' . $response->body());
                }
            } catch (Exception $e) {
                Log::warning('Qwen HTTP Client exception, fallback to simulation: ' . $e->getMessage());
            }
        }

        // Fallback otomatis ke Agen Penalaran Simulasi jika API Key belum dipasang / offline
        return $this->simulateAgenticAI($userMessage, $flightContext);
    }

    /**
     * Membersihkan tag Markdown ```json dan memvalidasi JSON output dari LLM.
     */
    private function parseStrictJson(?string $rawContent): ?array
    {
        if (empty($rawContent)) {
            return null;
        }

        // Hapus pembungkus markdown ```json atau ``` jika ada
        $clean = trim($rawContent);
        $clean = preg_replace('/^```(?:json)?\s*/i', '', $clean);
        $clean = preg_replace('/\s*```$/i', '', $clean);
        $clean = trim($clean);

        $decoded = json_decode($clean, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && isset($decoded['replyId'])) {
            return [
                'type' => $decoded['type'] ?? 'text',
                'replyId' => $decoded['replyId'],
                'replyEn' => $decoded['replyEn'] ?? $decoded['replyId'],
                'showTicketPolicy' => (bool) ($decoded['showTicketPolicy'] ?? false),
                'showRecommendation' => (bool) ($decoded['showRecommendation'] ?? false),
            ];
        }

        return null;
    }

    /**
     * Mesin Penalaran Simulasi Agen AI (Fallback Cerdas Bilingual).
     */
    private function simulateAgenticAI(string $message, array $flightContext = []): array
    {
        $lowerMsg = strtolower($message);
        $flightNo = $flightContext['flight_number'] ?? 'GA826';
        $status = $flightContext['status'] ?? 'delayed';
        $route = $flightContext['route'] ?? 'CGK ➔ SIN';

        // 1. Cuaca / Weather / Delay Disruption Check
        if (str_contains($lowerMsg, 'cuaca') || str_contains($lowerMsg, 'weather') || str_contains($lowerMsg, 'kondisi') || str_contains($lowerMsg, 'condition') || str_contains($lowerMsg, 'affecting') || str_contains($lowerMsg, 'forecast') || str_contains($lowerMsg, 'haneda') || str_contains($lowerMsg, 'hnd')) {
            return [
                'type' => 'disruption_alert',
                'replyId' => "Penerbangan {$flightNo} ({$route}) terpengaruh oleh cuaca buruk (hujan deras & angin kencang). Estimasi keberangkatan diperbarui dengan keterlambatan 4 jam 25 menit. Sistem pemantauan Rebound aktif 24/7.",
                'replyEn' => "Flight {$flightNo} ({$route}) is affected by severe weather conditions (heavy rain & high winds). Estimated departure updated with 4h 25m delay. Rebound monitoring active 24/7.",
                'showTicketPolicy' => true,
                'showRecommendation' => false,
            ];
        }

        // 2. Opsi Penerbangan / Jadwal Alternatif / Besok / Tomorrow / Rebooking Search
        if (str_contains($lowerMsg, 'opsi') || str_contains($lowerMsg, 'jadwal') || str_contains($lowerMsg, 'alternatif') || str_contains($lowerMsg, 'besok') || str_contains($lowerMsg, 'tomorrow') || str_contains($lowerMsg, 'ticket') || str_contains($lowerMsg, 'tickets') || str_contains($lowerMsg, 'flight') || str_contains($lowerMsg, 'morning') || str_contains($lowerMsg, 'schedule') || str_contains($lowerMsg, 'lain')) {
            return [
                'type' => 'options_list',
                'replyId' => "Berikut daftar jadwal penerbangan alternatif dari sistem GDS Atlas yang tersedia untuk rute {$route}.",
                'replyEn' => "Here is the list of available alternative flight schedules from the GDS Atlas system for route {$route}.",
                'showTicketPolicy' => true,
                'showRecommendation' => true,
            ];
        }

        // 3. Aturan Kebijakan Tiket / Waiver 72A / Refund / Fee / Kompensasi / Meals
        if (str_contains($lowerMsg, 'aturan') || str_contains($lowerMsg, 'policy') || str_contains($lowerMsg, 'kompensasi') || str_contains($lowerMsg, 'compensation') || str_contains($lowerMsg, 'meal') || str_contains($lowerMsg, 'makanan') || str_contains($lowerMsg, 'entitlement') || str_contains($lowerMsg, 'fee') || str_contains($lowerMsg, 'denda') || str_contains($lowerMsg, 'refund')) {
            return [
                'type' => 'policy_card',
                'replyId' => "Berdasarkan analisis kebijakan tiket untuk penerbangan {$flightNo}, Anda berhak atas Waiver 72A (Bebas Biaya Perubahan) serta snack/meal voucher untuk keterlambatan > 2 jam.",
                'replyEn' => "Based on policy analysis for flight {$flightNo}, you are eligible for Waiver 72A ($0 Rebooking Fee) and meal vouchers for delays over 2 hours.",
                'showTicketPolicy' => true,
                'showRecommendation' => false,
            ];
        }

        // 4. Facility Check (Lounge / Terminal 3)
        if (str_contains($lowerMsg, 'lounge') || str_contains($lowerMsg, 'plaza premium') || str_contains($lowerMsg, 'terminal')) {
            return [
                'type' => 'text',
                'replyId' => "Sebagai penumpang penerbangan ini, Anda berhak mengakses Plaza Premium Lounge di Terminal 3 Bandara Soekarno-Hatta (di dekat Gate 6).",
                'replyEn' => "As a passenger on this flight, you have complimentary access to the Plaza Premium Lounge at Terminal 3 (near Gate 6).",
                'showTicketPolicy' => false,
                'showRecommendation' => false,
            ];
        }

        // 5. Baggage Allowance
        if (str_contains($lowerMsg, 'bagasi') || str_contains($lowerMsg, 'baggage') || str_contains($lowerMsg, 'kabin') || str_contains($lowerMsg, 'cabin') || str_contains($lowerMsg, 'allowance')) {
            return [
                'type' => 'text',
                'replyId' => "Batas bagasi terdaftar untuk tiket Anda adalah 30 kg, ditambah 1 bagasi kabin maksimal 7 kg.",
                'replyEn' => "Checked baggage allowance for your ticket is 30 kg, plus 1 cabin baggage up to 7 kg.",
                'showTicketPolicy' => false,
                'showRecommendation' => false,
            ];
        }

        // 6. Rebook confirm
        if (str_contains($lowerMsg, 'pindah') || str_contains($lowerMsg, 'rebook') || str_contains($lowerMsg, 'ganti') || str_contains($lowerMsg, 'confirm')) {
            return [
                'type' => 'success_card',
                'replyId' => "Permintaan rebooking untuk penerbangan {$flightNo} berhasil diproses via GDS Atlas.",
                'replyEn' => "Rebooking request for flight {$flightNo} has been successfully processed via GDS Atlas.",
                'showTicketPolicy' => false,
                'showRecommendation' => true,
            ];
        }

        return [
            'type' => 'text',
            'replyId' => "Pesan diterima. Saya memantau penerbangan {$flightNo} ({$route}) dengan status: {$status}. Ada yang bisa saya bantu?",
            'replyEn' => "Message received. I am tracking flight {$flightNo} ({$route}) with status: {$status}. How can I assist you?",
            'showTicketPolicy' => false,
            'showRecommendation' => false,
        ];
    }

    /**
     * id: Menghapus sesi chat beserta seluruh riwayat pesan (chat_messages) dari database untuk menghemat ruang penyimpanan.
     * en: Deletes a chat session along with all its message history (chat_messages) from the database to save storage space.
     */
    public function deleteSession(Request $request, $id)
    {
        $user = $request->user();

        $session = AgentChatSession::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$session) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi chat tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        $pnrCode = $session->pnr_code;

        // Hapus sesi (pesan di chat_messages otomatis terhapus via CASCADE)
        $session->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Sesi chat PNR ' . $pnrCode . ' berhasil dihapus.',
            'pnr_code' => $pnrCode,
            'session_id' => (int) $id,
        ], 200);
    }
}