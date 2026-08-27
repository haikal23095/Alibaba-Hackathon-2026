<?php

namespace Database\Seeders;

use App\Models\AgentChatSession;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\UserPnr;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AgentChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Cari user utama demo
        $user1 = User::where('email', 'zakariamp@rebound.ai')->first();
        $user2 = User::where('email', 'haikal.firmansyah@rebound.ai')->first();
        $user3 = User::where('email', 'tiara.azzahra@rebound.ai')->first();

        if (!$user1) {
            return;
        }

        // 2. Pastikan user_pnrs terisi untuk User 1 (Zakaria MP)
        $pnrConfigs = [
            ['pnr_code' => 'GA826', 'last_name' => 'ZAKARIA', 'status' => 'active'],
            ['pnr_code' => 'SQ951', 'last_name' => 'MAULANA', 'status' => 'changed'],
            ['pnr_code' => 'SQ638', 'last_name' => 'ISTIQOMAH', 'status' => 'changed'],
            ['pnr_code' => 'QZ502', 'last_name' => 'AZZAHRA', 'status' => 'changed'],
            ['pnr_code' => 'JT028', 'last_name' => 'ZAKARIA', 'status' => 'changed'],
        ];

        foreach ($pnrConfigs as $config) {
            UserPnr::updateOrCreate(
                ['user_id' => $user1->id, 'pnr_code' => $config['pnr_code']],
                $config
            );
        }

        if ($user2) {
            UserPnr::updateOrCreate(
                ['user_id' => $user2->id, 'pnr_code' => 'GA826K'],
                ['last_name' => 'FIRMANSYAH', 'status' => 'active']
            );
        }

        if ($user3) {
            UserPnr::updateOrCreate(
                ['user_id' => $user3->id, 'pnr_code' => 'QZ502'],
                ['last_name' => 'AZZAHRA', 'status' => 'active']
            );
        }

        // 3. Buat Sesi Chat & Pesan Percakapan AI Agent
        $sessionsData = [
            [
                'user_id' => $user1->id,
                'pnr_code' => 'GA826',
                'context_summary' => 'Analisis krisis delay GA826 (4j 25m). AI menawarkan tiket alternatif GDS Atlas & waiver 72A.',
                'created_at' => Carbon::now()->subMinutes(30),
                'updated_at' => Carbon::now()->subMinutes(5),
                'messages' => [
                    [
                        'sender' => 'agent',
                        'message_content' => 'Halo Zakaria MP! Saya sedang memantau penerbangan GA826 Anda (Jakarta CGK -> Singapura SIN). Saat ini penerbangan Anda mengalami keterlambatan 4 jam 25 menit akibat cuaca buruk. Saya telah memeriksa aturan tiket dan menyiapkan opsi penerbangan alternatif untuk Anda.',
                        'dynamic_ui_payload' => [
                            'type' => 'greeting',
                            'showTicketPolicy' => true,
                            'showRecommendation' => true,
                        ],
                        'sent_at' => Carbon::now()->subMinutes(30),
                    ],
                    [
                        'sender' => 'user',
                        'message_content' => 'Apakah ada penerbangan alternatif tanpa biaya tambahan?',
                        'dynamic_ui_payload' => null,
                        'sent_at' => Carbon::now()->subMinutes(25),
                    ],
                    [
                        'sender' => 'agent',
                        'message_content' => 'Berdasarkan analisis kebijakan tiket Anda (Class Y - Rule 72A Waiver), Anda berhak melakukan perubahan jadwal tanpa biaya administrasi ($0 Fee). Berikut rincian opsi penerbangan terdekat yang tersedia di GDS Atlas.',
                        'dynamic_ui_payload' => [
                            'type' => 'policy_card',
                            'showTicketPolicy' => true,
                            'showRecommendation' => true,
                        ],
                        'sent_at' => Carbon::now()->subMinutes(20),
                    ],
                    [
                        'sender' => 'user',
                        'message_content' => 'Tampilkan opsi jadwal alternatif.',
                        'dynamic_ui_payload' => null,
                        'sent_at' => Carbon::now()->subMinutes(10),
                    ],
                    [
                        'sender' => 'agent',
                        'message_content' => 'Berikut daftar penerbangan alternatif dari sistem GDS Atlas yang tersedia hari ini.',
                        'dynamic_ui_payload' => [
                            'type' => 'options_list',
                            'showRecommendation' => true,
                        ],
                        'sent_at' => Carbon::now()->subMinutes(5),
                    ],
                ],
            ],
            [
                'user_id' => $user1->id,
                'pnr_code' => 'SQ951',
                'context_summary' => 'Konfirmasi tiket Business Class SQ951 & akses Plaza Premium Lounge Terminal 3.',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2)->addHours(1),
                'messages' => [
                    [
                        'sender' => 'agent',
                        'message_content' => 'Halo Zakaria MP! Tiket Business Class Singapore Airlines SQ951 (Jakarta CGK -> Singapura SIN) Anda telah terverifikasi. Keberangkatan dari Terminal 3, Gate 6 pukul 05:00 AM.',
                        'dynamic_ui_payload' => [
                            'type' => 'greeting',
                            'showRecommendation' => false,
                        ],
                        'sent_at' => Carbon::now()->subDays(2),
                    ],
                    [
                        'sender' => 'user',
                        'message_content' => 'Apakah saya bisa mengakses lounge di Terminal 3?',
                        'dynamic_ui_payload' => null,
                        'sent_at' => Carbon::now()->subDays(2)->addMinutes(15),
                    ],
                    [
                        'sender' => 'agent',
                        'message_content' => 'Ya, sebagai penumpang Business Class Singapore Airlines, Anda berhak mengakses Plaza Premium Lounge di Terminal 3 Bandara Soekarno-Hatta sebelum keberangkatan.',
                        'dynamic_ui_payload' => [
                            'type' => 'text',
                        ],
                        'sent_at' => Carbon::now()->subDays(2)->addMinutes(16),
                    ],
                ],
            ],
            [
                'user_id' => $user1->id,
                'pnr_code' => 'SQ638',
                'context_summary' => 'Pemantauan jadwal Singapore Airlines SQ638 (SIN -> HND). Status tepat waktu.',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5)->addMinutes(10),
                'messages' => [
                    [
                        'sender' => 'agent',
                        'message_content' => 'Halo Zakaria MP! Saya memantau tiket SQ638 Anda ke Tokyo (Haneda) pada 05 Desember 2026. Penerbangan saat ini dijadwalkan tepat waktu pukul 23:55 dari Bandara Changi (Terminal 3).',
                        'dynamic_ui_payload' => [
                            'type' => 'greeting',
                        ],
                        'sent_at' => Carbon::now()->subDays(5),
                    ],
                ],
            ],
            [
                'user_id' => $user1->id,
                'pnr_code' => 'QZ502',
                'context_summary' => 'Perjalanan QZ502 (DPS -> SIN) telah selesai.',
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now()->subDays(12),
                'messages' => [
                    [
                        'sender' => 'agent',
                        'message_content' => 'Perjalanan QZ502 (Bali DPS -> Singapura SIN) telah selesai. Terima kasih telah menggunakan Rebound AI.',
                        'dynamic_ui_payload' => [
                            'type' => 'text',
                        ],
                        'sent_at' => Carbon::now()->subDays(12),
                    ],
                ],
            ],
            [
                'user_id' => $user1->id,
                'pnr_code' => 'JT028',
                'context_summary' => 'Pembatalan penerbangan JT028 (CGK -> SUB) oleh maskapai.',
                'created_at' => Carbon::now()->subDays(18),
                'updated_at' => Carbon::now()->subDays(18),
                'messages' => [
                    [
                        'sender' => 'agent',
                        'message_content' => 'Penerbangan Lion Air JT028 (CGK -> SUB) telah dibatalkan oleh pihak maskapai. Pengajuan refund/voucher kompensasi telah dikirim ke sistem.',
                        'dynamic_ui_payload' => [
                            'type' => 'text',
                        ],
                        'sent_at' => Carbon::now()->subDays(18),
                    ],
                ],
            ],
        ];

        foreach ($sessionsData as $sData) {
            $messagesData = $sData['messages'];
            unset($sData['messages']);

            $session = AgentChatSession::updateOrCreate(
                ['user_id' => $sData['user_id'], 'pnr_code' => $sData['pnr_code']],
                $sData
            );

            // Bersihkan pesan lama agar tidak duplikat saat rerun seeder
            ChatMessage::where('session_id', $session->id)->delete();

            foreach ($messagesData as $m) {
                ChatMessage::create([
                    'session_id' => $session->id,
                    'sender' => $m['sender'],
                    'message_content' => $m['message_content'],
                    'dynamic_ui_payload' => $m['dynamic_ui_payload'],
                    'sent_at' => $m['sent_at'],
                ]);
            }
        }
    }
}
