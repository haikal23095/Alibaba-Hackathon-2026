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
            'pnr' => 'required|string|max:10',
            // id: Bahasa UI yang sedang aktif (dikirim Alpine dari this.lang) — memaksa bahasa balasan AI
            // en: The active UI language (sent by Alpine from this.lang) — forces the AI reply language
            'lang' => 'nullable|in:id,en',
        ]);

        $user = $request->user();
        $userMessage = trim($request->input('message'));
        $pnrCode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $request->input('pnr')));
        $lang = $request->input('lang', 'id') === 'en' ? 'en' : 'id';

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
            $aiData = $this->callQwenLLM($userMessage, $flightContext, $chatHistory, $lang);

            // id: Tanpa mesin simulasi — jika Qwen tidak tersedia (API key belum ada, offline,
            //     atau output tidak valid), balas dengan pesan gangguan yang jujur agar frontend
            //     tidak pernah menampilkan jawaban tiruan.
            // en: No simulation engine — when Qwen is unavailable (missing API key, offline, or
            //     invalid output), reply with an honest disruption message so the frontend never
            //     presents fabricated answers.
            if ($aiData === null) {
                return response()->json([
                    'type' => 'text',
                    'replyId' => 'Layanan AI Qwen belum tersedia (API key belum dikonfigurasi atau layanan sedang gangguan). Silakan coba lagi sebentar lagi.',
                    'replyEn' => 'The Qwen AI service is currently unavailable (API key not configured or the service is disrupted). Please try again shortly.',
                    'showTicketPolicy' => false,
                    'showRecommendation' => false,
                ], 503);
            }

            // 8. Simpan balasan AI ke database
            // id: Bentuk tersimpan kanonik adalah BAHASA INGGRIS (replyEn) sesuai keputusan produk;
            //     frontend tetap memilih tampilan id/en dari kedua field respons saat pesan baru diterima.
            // en: The canonical stored form is ENGLISH (replyEn) per product decision; the frontend
            //     still picks the id/en display from both response fields when a fresh message arrives.
            ChatMessage::create([
                'session_id' => $session->id,
                'sender' => 'agent',
                'message_content' => $aiData['replyEn'] ?: $aiData['replyId'],
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
     * id: Lapis 2 saran prompt kontekstual — meminta Qwen merumuskan dua saran pertanyaan singkat
     *     berdasarkan konteks penerbangan riil PNR milik user. Jika Qwen belum dikonfigurasi / gagal,
     *     respons membawa daftar kosong (source 'none') sehingga frontend mempertahankan saran
     *     lapis-1 — tanpa jawaban tiruan dari mesin simulasi.
     * en: Layer-2 contextual prompt suggestions — asks Qwen to craft two short question suggestions
     *     based on the real flight context of the user's PNR. When Qwen is unconfigured / fails, the
     *     response carries an empty list (source 'none') so the frontend keeps the layer-1
     *     suggestions — no fabricated answers from a simulation engine.
     */
    public function aiSuggestions(Request $request)
    {
        $request->validate([
            'pnr' => 'required|string|max:10',
            'lang' => 'nullable|in:id,en',
        ]);

        $user = $request->user();
        $pnrCode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $request->input('pnr')));
        $lang = $request->input('lang', 'id') === 'en' ? 'en' : 'id';

        // id: PNR harus terverifikasi milik user yang sedang login
        // en: The PNR must be verified as belonging to the logged-in user
        $validPnr = UserPnr::where('user_id', $user->id)
                           ->where('pnr_code', $pnrCode)
                           ->first();

        if (!$validPnr) {
            return response()->json([
                'status' => 'error',
                'suggestions' => [],
            ], 403);
        }

        $gdsBooking = MockGdsBooking::where('pnr_code', $pnrCode)->first();
        $flightContext = [
            'pnr' => $pnrCode,
            'flight_number' => $gdsBooking?->flight_number ?? $pnrCode,
            'route' => ($gdsBooking?->from_code ?? 'CGK') . ' ➔ ' . ($gdsBooking?->to_code ?? 'SIN'),
            'departure_time' => $gdsBooking?->departure_time?->format('Y-m-d H:i') ?? '',
            'cabin_class' => $gdsBooking?->cabin_class ?? 'Economy',
            'status' => $gdsBooking?->status ?? 'active',
            'waiver_eligible' => in_array($gdsBooking?->status, ['delayed', 'cancelled']),
        ];

        $aiSuggestions = $this->callQwenForSuggestions($flightContext, $lang);

        if ($aiSuggestions !== null) {
            return response()->json([
                'status' => 'success',
                'source' => 'ai',
                'pnr_code' => $pnrCode,
                'suggestions' => $aiSuggestions,
            ], 200);
        }

        // id: Qwen tidak tersedia — kembalikan daftar kosong sehingga frontend mempertahankan
        //     saran lapis-1 (rule-based) yang sudah dirender dari dashboard.
        // en: Qwen unavailable — return an empty list so the frontend keeps the layer-1
        //     (rule-based) suggestions already rendered from the dashboard.
        return response()->json([
            'status' => 'success',
            'source' => 'none',
            'pnr_code' => $pnrCode,
            'suggestions' => [],
        ], 200);
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
     * id: Prompt khusus generator saran — output JSON murni berisi maksimal 3 saran bilingual
     *     yang relevan dengan kondisi penerbangan (delay/batal/normal) tanpa halusinasi jadwal.
     * en: Dedicated suggestion-generator prompt — pure JSON output containing up to 3 bilingual
     *     suggestions relevant to the flight condition (delayed/cancelled/normal) without hallucinating schedules.
     */
    private function getSuggestionPrompt(): string
    {
        return <<<PROMPT
Anda adalah mesin saran prompt untuk REBOUND, asisten krisis penerbangan enterprise.

TUGAS: Rumuskan 2 saran pertanyaan pendek yang paling relevan untuk penumpang berdasarkan DATA PENERBANGAN (flight_context) yang diberikan.

ATURAN WAJIB:
1. Respons HARUS berupa JSON murni tanpa pembungkus Markdown: {"suggestions":[{"id":"...","en":"..."},{"id":"...","en":"..."}]}
2. Field "id" ditulis dalam Bahasa Indonesia dan "en" dalam Bahasa Inggris; keduanya harus bermakna sama.
3. Setiap saran maksimal 90 karakter, berbentuk pertanyaan atau permintaan singkat yang bisa langsung dikirim penumpang ke asisten.
4. DILARANG mengarang jadwal, waktu, atau kebijakan yang tidak ada di flight_context.
5. Saran harus mengikuti kondisi status: delay/pembatalan fokus pada rebooking, kompensasi, dan waiver; status normal fokus pada check-in, bagasi, fasilitas bandara, atau cuaca rute.
PROMPT;
    }

    /**
     * id: Memanggil Qwen khusus untuk menghasilkan saran prompt; mengembalikan null bila API key
     *     belum dikonfigurasi, panggilan gagal, atau output JSON tidak valid (frontend lalu
     *     mempertahankan saran lapis-1 yang sudah tampil lebih dulu — tanpa jawaban tiruan).
     * en: Calls Qwen exclusively to generate prompt suggestions; returns null when the API key is
     *     unconfigured, the call fails, or the JSON output is invalid (the frontend then keeps the
     *     layer-1 suggestions already on screen — no fabricated answers).
     */
    private function callQwenForSuggestions(array $flightContext, string $lang): ?array
    {
        $apiKey = config('services.qwen.api_key')
            ?: env('QWEN_API_KEY', env('DASHSCOPE_API_KEY', env('QODER_API_KEY')));

        $endpoint = config('services.qwen.endpoint')
            ?: env('QWEN_API_ENDPOINT', 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1/chat/completions');

        $model = config('services.qwen.model')
            ?: env('QWEN_MODEL_NAME', 'qwen-max');

        if (empty($apiKey)) {
            return null;
        }

        try {
            $messagesPayload = [
                ['role' => 'system', 'content' => $this->getSuggestionPrompt()],
                [
                    'role' => 'system',
                    'content' => "DATA PENERBANGAN RESMI GDS ATLAS (flight_context):\n" . json_encode($flightContext, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                ],
                [
                    'role' => 'system',
                    'content' => $lang === 'en'
                        ? 'The user\'s app language setting is ENGLISH. Prioritize suggestions that read naturally in English for the "en" field; keep "id" in Bahasa Indonesia.'
                        : 'Pengaturan bahasa aplikasi pengguna adalah BAHASA INDONESIA. Prioritaskan saran yang luwes dibaca dalam Bahasa Indonesia untuk field "id"; tetap tulis "en" dalam Bahasa Inggris.',
                ],
                [
                    'role' => 'user',
                    'content' => 'Buat saran prompt sekarang berdasarkan flight_context di atas.',
                ],
            ];

            // id: Timeout 30 detik — saran adalah fitur pelengkap; jangan memblokir UI lebih lama dari chat utama
            // en: 30s timeout — suggestions are supplementary; never block the UI longer than the main chat
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . trim($apiKey),
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint, [
                    'model' => $model,
                    'messages' => $messagesPayload,
                    'temperature' => 0.5,
                    'response_format' => ['type' => 'json_object']
                ]);

            if ($response->successful()) {
                return $this->parseSuggestionJson($response->json('choices.0.message.content'));
            }

            Log::warning('Qwen suggestion API error: ' . $response->body());
        } catch (Exception $e) {
            Log::warning('Qwen suggestion HTTP exception, keeping layer-1 suggestions: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * id: Memvalidasi output JSON saran dari Qwen — maksimal 3 saran, tiap entri wajib punya id & en.
     * en: Validates Qwen's suggestion JSON output — up to 3 suggestions, each entry must carry id & en.
     */
    private function parseSuggestionJson(?string $rawContent): ?array
    {
        if (empty($rawContent)) {
            return null;
        }

        $clean = trim($rawContent);
        $clean = preg_replace('/^```(?:json)?\s*/i', '', $clean);
        $clean = preg_replace('/\s*```$/i', '', $clean);

        $decoded = json_decode(trim($clean), true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($decoded['suggestions']) || !is_array($decoded['suggestions'])) {
            return null;
        }

        $items = [];
        foreach ($decoded['suggestions'] as $suggestion) {
            if (!is_array($suggestion) || empty($suggestion['id']) || empty($suggestion['en'])) {
                continue;
            }
            $items[] = [
                'id' => mb_substr((string) $suggestion['id'], 0, 140),
                'en' => mb_substr((string) $suggestion['en'], 0, 140),
            ];
            if (count($items) >= 3) {
                break;
            }
        }

        return count($items) > 0 ? $items : null;
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
5. Bahasa: "replyId" HARUS selalu dalam Bahasa Indonesia dan "replyEn" HARUS selalu dalam Bahasa Inggris, terlepas dari bahasa apa pun yang dipakai pengguna saat bertanya. Jangan pernah mencampur bahasa atau mengikuti bahasa pertanyaan pengguna.
PROMPT;
    }

    /**
     * id: Mengirimkan HTTP Request ke Qwen API (Alibaba Cloud Model Studio / DashScope / Qoder API).
     *     Mengembalikan null bila API key belum dikonfigurasi, panggilan gagal, atau output tidak
     *     valid — TIDAK ADA mesin simulasi; kegagalan diteruskan sebagai pesan gangguan yang jujur.
     * en: Sends the HTTP request to the Qwen API (Alibaba Cloud Model Studio / DashScope / Qoder API).
     *     Returns null when the API key is unconfigured, the call fails, or the output is invalid —
     *     there is NO simulation engine; failures surface as an honest disruption message.
     */
    private function callQwenLLM(string $userMessage, array $flightContext, array $chatHistory, string $lang = 'id'): ?array
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
                    ],
                    [
                        // id: Paksa bahasa balasan mengikuti pengaturan bahasa aplikasi (this.lang di frontend),
                        //     terlepas dari bahasa apa pun yang dipakai pengguna saat bertanya.
                        // en: Force the reply language to follow the app language setting (frontend this.lang),
                        //     regardless of whatever language the user writes their question in.
                        'role' => 'system',
                        'content' => $lang === 'en'
                            ? 'The user\'s app language setting is ENGLISH. The visible reply shown to the user is "replyEn", so "replyEn" MUST be written in English even if the user asks their question in another language. Keep "replyId" in Bahasa Indonesia.'
                            : 'Pengaturan bahasa aplikasi pengguna adalah BAHASA INDONESIA. Balasan yang ditampilkan ke pengguna adalah "replyId", jadi "replyId" HARUS ditulis dalam Bahasa Indonesia meskipun pengguna bertanya dalam bahasa lain. Tetap tulis "replyEn" dalam Bahasa Inggris.',
                    ],
                ];

                foreach ($chatHistory as $prevMsg) {
                    $messagesPayload[] = $prevMsg;
                }

                $messagesPayload[] = ['role' => 'user', 'content' => $userMessage];

                // id: Timeout 45 detik — model thinking Qwen bisa lambat pada prompt besar; di bawah itu berisiko gagal lalu berakhir sebagai pesan gangguan
                // en: 45s timeout — Qwen thinking models can be slow on large prompts; less risks failing and ending as a disruption message
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

        // id: Qwen tidak tersedia (tanpa API key / offline / output tidak valid) — null diteruskan
        //     ke pemanggil untuk diubah menjadi pesan gangguan yang jujur, BUKAN jawaban simulasi.
        // en: Qwen unavailable (no API key / offline / invalid output) — null is passed back to the
        //     caller to become an honest disruption message, NOT a simulated answer.
        return null;
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