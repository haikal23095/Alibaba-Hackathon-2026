<?php

use App\Models\MockGdsBooking;
use App\Models\UserPnr;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// id: Verifikasi PNR ke GDS (Mock GDS: tabel mock_gds_bookings) — jika GDS menjawab valid,
//     kode PNR + ID user yang login dicatat ke tabel user_pnrs di database lokal MySQL.
// en: GDS (Mock GDS: mock_gds_bookings table) PNR verification — when the GDS answers valid,
//     the PNR code + logged-in user ID are recorded in the user_pnrs table of the local MySQL database.

function gdsBooking(): MockGdsBooking
{
    return MockGdsBooking::create([
        'pnr_code' => 'ABC123',
        'last_name' => 'ZAKARIA',
        'flight_number' => 'GA 826',
        'from_code' => 'CGK',
        'to_code' => 'SIN',
        'departure_time' => '2026-08-28 08:25:00',
        'cabin_class' => 'Economy',
        'status' => 'delayed',
    ]);
}

test('PNR yang dijawab valid oleh GDS dicatat ke database user lokal', function () {
    gdsBooking();
    $user = pnrUser();

    $this->actingAs($user)
        ->postJson('/api/pnr/verify', ['pnr' => 'abc-123', 'passenger' => 'Zakaria'])
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.pnr_code', 'ABC123')
        ->assertJsonPath('data.flight.flight_number', 'GA 826');

    $stored = UserPnr::where('user_id', $user->id)->where('pnr_code', 'ABC123')->first();
    expect($stored)->not->toBeNull()
        ->and($stored->status)->toBe('active')
        ->and($stored->last_name)->toBe('Zakaria');
});

test('nama penumpang boleh lebih panjang dari nama di GDS', function () {
    gdsBooking();
    $user = pnrUser();

    $this->actingAs($user)
        ->postJson('/api/pnr/verify', ['pnr' => 'ABC123', 'passenger' => 'Zakaria MP (MR)'])
        ->assertOk();

    expect(UserPnr::where('user_id', $user->id)->where('pnr_code', 'ABC123')->exists())->toBeTrue();
});

test('PNR yang tidak ada di GDS tidak dicatat ke database', function () {
    $user = pnrUser();

    $this->actingAs($user)
        ->postJson('/api/pnr/verify', ['pnr' => 'ZZZ999', 'passenger' => 'Budi Santoso'])
        ->assertNotFound()
        ->assertJsonPath('status', 'invalid');

    expect(UserPnr::where('user_id', $user->id)->count())->toBe(0);
});

test('PNR ada di GDS tapi nama penumpang salah tetap ditolak', function () {
    gdsBooking();
    $user = pnrUser();

    $this->actingAs($user)
        ->postJson('/api/pnr/verify', ['pnr' => 'ABC123', 'passenger' => 'Budi Santoso'])
        ->assertNotFound()
        ->assertJsonPath('status', 'invalid');

    expect(UserPnr::where('user_id', $user->id)->count())->toBe(0);
});

test('PNR valid menyingkirkan PNR aktif lama milik user', function () {
    gdsBooking();
    $user = pnrUser();
    $user->pnrs()->create(['pnr_code' => 'GA826', 'last_name' => 'Budi', 'status' => 'active']);

    $this->actingAs($user)
        ->postJson('/api/pnr/verify', ['pnr' => 'ABC123', 'passenger' => 'Zakaria'])
        ->assertOk();

    expect($user->pnrs()->where('pnr_code', 'GA826')->value('status'))->toBe('changed')
        ->and($user->pnrs()->where('status', 'active')->count())->toBe(1);
});

test('format PNR yang salah ditolak sebelum mengecek GDS', function () {
    gdsBooking();
    $user = pnrUser();

    $this->actingAs($user)
        ->postJson('/api/pnr/verify', ['pnr' => 'AB1', 'passenger' => 'Budi'])
        ->assertStatus(422);

    expect(UserPnr::count())->toBe(0);
});

test('guest tidak bisa memanggil verifikasi GDS', function () {
    $this->postJson('/api/pnr/verify', ['pnr' => 'ABC123', 'passenger' => 'Budi'])
        ->assertUnauthorized();
});
