<?php

// Tes sementara: memastikan route '/' mengirim $activePnrCode ke view welcome
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('dashboard menerima activePnrCode dari PNR aktif user', function () {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $user->pnrs()->create([
        'pnr_code' => 'GA826K',
        'last_name' => 'User',
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->get('/');

    $response->assertOk();
    $response->assertSee('GA826K');
    $response->assertViewHas('activePnrCode', 'GA826K');
    $response->assertViewHas('hasSetupPnr', true);
});

test('dashboard tetap render tanpa PNR aktif', function () {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'nopnr@example.com',
        'password' => 'password',
    ]);

    $response = $this->actingAs($user)->get('/');

    $response->assertOk();
    $response->assertViewHas('activePnrCode', null);
    $response->assertViewHas('hasSetupPnr', false);
});
