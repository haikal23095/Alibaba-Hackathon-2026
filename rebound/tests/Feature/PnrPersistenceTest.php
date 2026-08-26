<?php

// Memastikan aktivasi PNR & percakapan chat bertahan setelah refresh (tersimpan di database)
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function pnrUser(): User
{
    return User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
    ]);
}

test('aktivasi PNR tersimpan di database sehingga dashboard tidak memunculkan modal lagi', function () {
    $user = pnrUser();

    $this->actingAs($user)
        ->postJson('/api/pnr/activate', ['pnr_code' => 'GA826', 'last_name' => 'ZAKARIA MP'])
        ->assertOk()
        ->assertJsonPath('data.pnr_code', 'GA826');

    // Simulasi refresh halaman: hasSetupPnr harus true dan kode PNR ikut terkirim ke view
    $this->actingAs($user)->get('/')
        ->assertOk()
        ->assertViewHas('hasSetupPnr', true)
        ->assertViewHas('activePnrCode', 'GA826');
});

test('hanya ada satu PNR aktif per user', function () {
    $user = pnrUser();

    $this->actingAs($user)->postJson('/api/pnr/activate', ['pnr_code' => 'GA826'])->assertOk();
    $this->actingAs($user)->postJson('/api/pnr/activate', ['pnr_code' => 'SQ951A'])->assertOk();

    expect($user->pnrs()->where('status', 'active')->count())->toBe(1);
    expect($user->pnrs()->where('status', 'active')->value('pnr_code'))->toBe('SQ951A');
});

test('percakapan chat tersimpan dan bisa dimuat kembali lewat history', function () {
    $user = pnrUser();
    $this->actingAs($user)->postJson('/api/pnr/activate', ['pnr_code' => 'GA826'])->assertOk();

    // Kirim pesan seperti yang dilakukan frontend Alpine
    $this->actingAs($user)
        ->postJson('/api/chat/send', ['message' => 'Cek status penerbangan saya', 'pnr' => 'GA826'])
        ->assertOk();

    // Simulasi refresh: riwayat dimuat ulang dari database
    $response = $this->actingAs($user)->getJson('/api/chat/history?pnr=GA826')->assertOk();

    $messages = $response->json('messages');
    expect($messages)->toHaveCount(2);
    expect($messages[0]['sender'])->toBe('user');
    expect($messages[0]['text'])->toBe('Cek status penerbangan saya');
    expect($messages[1]['sender'])->toBe('ai'); // 'agent' di DB dirender sebagai 'ai'
});

test('history chat PNR milik user lain tidak bisa diakses', function () {
    $user = pnrUser();
    $other = User::create(['name' => 'Other', 'email' => 'other@example.com', 'password' => 'password']);

    $this->actingAs($other)->postJson('/api/pnr/activate', ['pnr_code' => 'SQ951A'])->assertOk();
    $this->actingAs($other)
        ->postJson('/api/chat/send', ['message' => 'rahasia', 'pnr' => 'SQ951A'])
        ->assertOk();

    $this->actingAs($user)
        ->getJson('/api/chat/history?pnr=SQ951A')
        ->assertOk()
        ->assertJsonPath('messages', []);
});

test('guest tidak bisa mengakses endpoint aktivasi & history', function () {
    $this->postJson('/api/pnr/activate', ['pnr_code' => 'GA826'])->assertUnauthorized();
    $this->getJson('/api/chat/history?pnr=GA826')->assertUnauthorized();
});

test('dashboard mengirim daftar tiket asli user dari database ke modal', function () {
    $user = pnrUser();
    $this->actingAs($user)->postJson('/api/pnr/activate', ['pnr_code' => 'GA826', 'last_name' => 'ZAKARIA MP'])->assertOk();
    $this->actingAs($user)->postJson('/api/pnr/activate', ['pnr_code' => 'SQ951A', 'last_name' => 'ISTIQOMAH'])->assertOk();

    $response = $this->actingAs($user)->get('/')
        ->assertOk()
        ->assertViewHas('userTickets', fn ($tickets) => $tickets->count() === 2);

    $html = $response->getContent();
    // id: Data asli database ikut ter-render ke state Alpine (userTickets)
    // en: Real database data is rendered into the Alpine state (userTickets)
    expect($html)->toContain('"pnr_code":"GA826"');
    expect($html)->toContain('"pnr_code":"SQ951A"');
    // id: Skenario uji coba statis sudah dihapus
    // en: Static test scenarios have been removed
    expect($html)->not->toContain('Uji Coba Tiket');
    expect($html)->not->toContain("submitPnr('SQ951')");
});
