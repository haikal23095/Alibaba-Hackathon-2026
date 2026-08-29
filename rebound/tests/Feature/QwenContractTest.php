<?php

use App\Models\ChatMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

// id: Kontrak endpoint chat tanpa mesin simulasi — Qwen adalah satu-satunya sumber balasan;
//     bila Qwen tidak tersedia, API menjawab dengan pesan gangguan yang jujur (HTTP 503),
//     dan tes jalur sukses memalsukan endpoint Qwen lewat Http::fake().
// en: Chat endpoint contract without a simulation engine — Qwen is the only source of replies;
//     when Qwen is unavailable the API answers with an honest disruption message (HTTP 503),
//     and success-path tests fake the Qwen endpoint via Http::fake().

test('tanpa API key chat membalas pesan gangguan jujur dengan status 503', function () {
    $user = pnrUser();
    $this->actingAs($user)->postJson('/api/pnr/activate', ['pnr_code' => 'GA826'])->assertOk();

    $response = $this->actingAs($user)
        ->postJson('/api/chat/send', ['message' => 'halo', 'pnr' => 'GA826'])
        ->assertStatus(503)
        ->assertJsonPath('type', 'text');

    expect(str_contains($response->json('replyId'), 'belum tersedia'))->toBeTrue();
    expect(str_contains($response->json('replyEn'), 'unavailable'))->toBeTrue();

    // id: Tidak ada balasan agen tiruan yang tersimpan di database
    // en: No fabricated agent reply is stored in the database
    expect(ChatMessage::where('sender', 'agent')->count())->toBe(0);
});

test('output Qwen tidak valid juga menghasilkan 503 tanpa jawaban simulasi', function () {
    config([
        'services.qwen.api_key' => 'testing',
        'services.qwen.endpoint' => 'https://qwen.test/chat/completions',
        'services.qwen.model' => 'qwen-test',
    ]);
    Http::fake([
        'qwen.test/*' => Http::response([
            'choices' => [['message' => ['content' => 'ini bukan JSON']]],
        ]),
    ]);

    $user = pnrUser();
    $this->actingAs($user)->postJson('/api/pnr/activate', ['pnr_code' => 'GA826'])->assertOk();

    $this->actingAs($user)
        ->postJson('/api/chat/send', ['message' => 'halo', 'pnr' => 'GA826'])
        ->assertStatus(503);

    expect(ChatMessage::where('sender', 'agent')->count())->toBe(0);
});

test('balasan Qwen asli diparse dan disimpan ke database', function () {
    fakeQwenChat();
    $user = pnrUser();
    $this->actingAs($user)->postJson('/api/pnr/activate', ['pnr_code' => 'GA826'])->assertOk();

    $this->actingAs($user)
        ->postJson('/api/chat/send', ['message' => 'halo', 'pnr' => 'GA826'])
        ->assertOk()
        ->assertJsonPath('type', 'text')
        ->assertJsonPath('replyEn', 'Test reply.');

    $stored = ChatMessage::where('sender', 'agent')->latest('id')->first();
    expect($stored->message_content)->toBe('Test reply.');
});
