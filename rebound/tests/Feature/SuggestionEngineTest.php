<?php

use App\Models\MockGdsBooking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

// id: Mesin saran prompt kontekstual — lapis 1 rule-based dari dashboard (suggestionsByPnr)
//     dan lapis 2 endpoint POST /api/chat/suggestions (hanya Qwen; tanpa API key mengembalikan
//     daftar kosong source 'none', tanpa mesin simulasi).
// en: Contextual prompt suggestion engine — layer 1 rule-based from the dashboard (suggestionsByPnr)
//     and layer 2 POST /api/chat/suggestions endpoint (Qwen only; without an API key it returns an
//     empty list with source 'none', with no simulation engine).

function suggestionBooking(string $status = 'delayed', string $pnr = 'QZ502'): MockGdsBooking
{
    return MockGdsBooking::create([
        'pnr_code' => $pnr,
        'last_name' => 'ZAKARIA',
        'flight_number' => 'QZ 502',
        'from_code' => 'CGK',
        'to_code' => 'DPS',
        'departure_time' => '2026-08-30 09:00:00',
        'cabin_class' => 'Economy',
        'status' => $status,
    ]);
}

function activateSuggestionPnr($user, string $pnr = 'QZ502'): void
{
    test()->actingAs($user)
        ->postJson('/api/pnr/activate', ['pnr_code' => $pnr, 'last_name' => 'ZAKARIA'])
        ->assertOk();
}

// id: Memalsukan endpoint saran Qwen untuk tes jalur AI (source 'ai')
// en: Fakes the Qwen suggestion endpoint for the AI-path tests (source 'ai')
function fakeQwenSuggestions(): void
{
    config([
        'services.qwen.api_key' => 'testing',
        'services.qwen.endpoint' => 'https://qwen.test/chat/completions',
        'services.qwen.model' => 'qwen-test',
    ]);

    Http::fake([
        'qwen.test/*' => Http::response([
            'choices' => [[
                'message' => [
                    'content' => json_encode([
                        'suggestions' => [
                            ['id' => 'Saran Qwen satu', 'en' => 'Qwen suggestion one'],
                            ['id' => 'Saran Qwen dua', 'en' => 'Qwen suggestion two'],
                        ],
                    ]),
                ],
            ]],
        ]),
    ]);
}

test('dashboard melewatkan saran lapis-1 per PNR mengikuti status GDS', function () {
    $user = pnrUser();
    suggestionBooking('delayed');
    activateSuggestionPnr($user);

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertViewHas('suggestionsByPnr', function ($suggestions) {
            $delayed = $suggestions['QZ502'] ?? [];

            return count($delayed) === 2
                && str_contains($delayed[0]['id'], 'QZ 502')
                && str_contains($delayed[0]['en'], 'QZ 502');
        });
});

test('saran lapis-1 tiket on_time tidak menawarkan rebooking', function () {
    $user = pnrUser();
    suggestionBooking('on_time');
    activateSuggestionPnr($user);

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertViewHas('suggestionsByPnr', function ($suggestions) {
            $onTime = $suggestions['QZ502'] ?? [];

            return count($onTime) === 2
                && str_contains($onTime[0]['id'], 'QZ 502')
                && ! str_contains($onTime[0]['id'], 'rebooking');
        });
});

test('guest tidak bisa meminta saran AI', function () {
    $this->postJson('/api/chat/suggestions', ['pnr' => 'QZ502'])
        ->assertStatus(401);
});

test('PNR milik user lain ditolak saat meminta saran AI', function () {
    $owner = pnrUser();
    suggestionBooking('delayed');
    activateSuggestionPnr($owner);

    $intruder = \App\Models\User::create([
        'name' => 'Intruder',
        'email' => 'intruder@example.com',
        'password' => 'password',
    ]);

    $this->actingAs($intruder)
        ->postJson('/api/chat/suggestions', ['pnr' => 'QZ502'])
        ->assertStatus(403)
        ->assertJsonPath('suggestions', []);
});

test('tanpa API key saran AI mengembalikan daftar kosong source none', function () {
    $user = pnrUser();
    suggestionBooking('delayed');
    activateSuggestionPnr($user);

    // id: Tanpa mesin simulasi — frontend mempertahankan saran lapis-1 dari dashboard
    // en: No simulation engine — the frontend keeps the dashboard's layer-1 suggestions
    $this->actingAs($user)
        ->postJson('/api/chat/suggestions', ['pnr' => 'QZ502', 'lang' => 'id'])
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('source', 'none')
        ->assertJsonPath('pnr_code', 'QZ502')
        ->assertJsonCount(0, 'suggestions');
});

test('saran AI memakai jawaban Qwen ketika endpoint dipalsukan', function () {
    fakeQwenSuggestions();
    $user = pnrUser();
    suggestionBooking('cancelled');
    activateSuggestionPnr($user);

    $this->actingAs($user)
        ->postJson('/api/chat/suggestions', ['pnr' => 'QZ502'])
        ->assertOk()
        ->assertJsonPath('source', 'ai')
        ->assertJsonPath('suggestions.0.id', 'Saran Qwen satu')
        ->assertJsonPath('suggestions.1.en', 'Qwen suggestion two');
});

test('output Qwen tidak valid menghasilkan daftar kosong tanpa jawaban tiruan', function () {
    config([
        'services.qwen.api_key' => 'testing',
        'services.qwen.endpoint' => 'https://qwen.test/chat/completions',
        'services.qwen.model' => 'qwen-test',
    ]);
    Http::fake([
        'qwen.test/*' => Http::response([
            'choices' => [['message' => ['content' => 'bukan json valid']]],
        ]),
    ]);

    $user = pnrUser();
    suggestionBooking('delayed');
    activateSuggestionPnr($user);

    $this->actingAs($user)
        ->postJson('/api/chat/suggestions', ['pnr' => 'QZ502'])
        ->assertOk()
        ->assertJsonPath('source', 'none')
        ->assertJsonCount(0, 'suggestions');
});

test('PNR tanpa booking GDS tanpa API key tetap mengembalikan source none', function () {
    $user = pnrUser();
    // id: PNR terverifikasi tanpa baris mock_gds_bookings (booking GDS belum ada)
    // en: Verified PNR without a mock_gds_bookings row (no GDS booking yet)
    activateSuggestionPnr($user, 'ABC123');

    $this->actingAs($user)
        ->postJson('/api/chat/suggestions', ['pnr' => 'ABC123'])
        ->assertOk()
        ->assertJsonPath('source', 'none')
        ->assertJsonCount(0, 'suggestions');
});
