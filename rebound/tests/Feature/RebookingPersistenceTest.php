<?php

use App\Models\Rebooking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// id: Persistensi rebooking berbasis database — status rebooked + penerbangan alternatif pilihan
//     disimpan ke tabel rebookings via POST /api/rebook dan dipulihkan lewat rebookingsByPnr di
//     dashboard, menggantikan penyimpanan localStorage frontend sepenuhnya.
// en: Database-backed rebooking persistence — the rebooked status + chosen alternative flight are
//     stored in the rebookings table via POST /api/rebook and restored through rebookingsByPnr on
//     the dashboard, fully replacing the frontend localStorage storage.

function rebookPayload(string $pnr = 'GA826', string $flight = 'GA830'): array
{
    return [
        'pnr' => $pnr,
        'alternative' => [
            'flightNumber' => $flight,
            'airline' => 'Garuda Indonesia',
            'airlineCode' => 'GA',
            'gate' => '4A',
            'depTime' => '12:40',
            'arrTime' => '15:25',
        ],
    ];
}

test('guest tidak bisa menyimpan rebooking', function () {
    $this->postJson('/api/rebook', rebookPayload())->assertStatus(401);
});

test('rebooking tersimpan ke tabel rebookings per user dan PNR', function () {
    $user = pnrUser();

    $this->actingAs($user)
        ->postJson('/api/rebook', rebookPayload())
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.pnr_code', 'GA826');

    $this->assertDatabaseCount('rebookings', 1);
    expect(Rebooking::first()->alternative_flight['flightNumber'])->toBe('GA830');
});

test('rebooking ulang PNR yang sama memperbarui baris, bukan duplikat', function () {
    $user = pnrUser();

    $this->actingAs($user)->postJson('/api/rebook', rebookPayload())->assertOk();
    $this->actingAs($user)->postJson('/api/rebook', rebookPayload('GA826', 'SQ638'))->assertOk();

    $this->assertDatabaseCount('rebookings', 1);
    expect(Rebooking::first()->alternative_flight['flightNumber'])->toBe('SQ638');
});

test('tanpa data alternatif valid endpoint menolak dengan 422', function () {
    $user = pnrUser();

    $this->actingAs($user)
        ->postJson('/api/rebook', ['pnr' => 'GA826', 'alternative' => ['airline' => 'x']])
        ->assertStatus(422);
});

test('dashboard mengirim rebookingsByPnr milik user ke view', function () {
    $user = pnrUser();
    $other = User::create([
        'name' => 'Other User',
        'email' => 'other@example.com',
        'password' => 'password',
    ]);

    $this->actingAs($user)->postJson('/api/rebook', rebookPayload())->assertOk();
    $this->actingAs($other)->postJson('/api/rebook', rebookPayload('SQ951', 'SQ638'))->assertOk();

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertViewHas('rebookingsByPnr', function ($rebookingsByPnr) {
            return isset($rebookingsByPnr['GA826']['flightNumber'])
                && $rebookingsByPnr['GA826']['flightNumber'] === 'GA830'
                && ! array_key_exists('SQ951', $rebookingsByPnr);
        });
});
