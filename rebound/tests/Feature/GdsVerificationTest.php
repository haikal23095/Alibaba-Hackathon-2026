<?php

use App\Models\AgentChatSession;
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

// id: Add Ticket PNR juga membuat sesi chat AI Agent (agent_chat_sessions)
//     agar tiket baru langsung tampil di sidebar kiri.
// en: Add Ticket PNR also creates an AI Agent chat session (agent_chat_sessions)
//     so the new ticket immediately appears in the left sidebar.

test('PNR valid membuat sesi chat baru dan mengirim kartu sidebar ke frontend', function () {
    gdsBooking();
    $user = pnrUser();

    $this->actingAs($user)
        ->postJson('/api/pnr/verify', ['pnr' => 'ABC123', 'passenger' => 'Zakaria'])
        ->assertOk()
        ->assertJsonPath('data.session.pnr_code', 'ABC123')
        ->assertJsonPath('data.session.flight_number', 'GA 826')
        ->assertJsonPath('data.session.from_code', 'CGK')
        ->assertJsonPath('data.session.to_code', 'SIN')
        ->assertJsonPath('data.session.status', 'delayed');

    $session = AgentChatSession::where('user_id', $user->id)->where('pnr_code', 'ABC123')->first();
    expect($session)->not->toBeNull()
        ->and($session->context_summary)->toContain('GA 826');
});

test('verifikasi ulang PNR yang sama tidak menduplikasi sesi chat', function () {
    gdsBooking();
    $user = pnrUser();

    $this->actingAs($user)
        ->postJson('/api/pnr/verify', ['pnr' => 'ABC123', 'passenger' => 'Zakaria'])
        ->assertOk();

    $this->actingAs($user)
        ->postJson('/api/pnr/verify', ['pnr' => 'ABC123', 'passenger' => 'Zakaria'])
        ->assertOk();

    expect(AgentChatSession::where('user_id', $user->id)->where('pnr_code', 'ABC123')->count())->toBe(1);
});

test('aktivasi tiket dari database juga menyiapkan sesi chat sidebar', function () {
    $user = pnrUser();

    $this->actingAs($user)
        ->postJson('/api/pnr/activate', ['pnr_code' => 'GA826', 'last_name' => 'Zakaria'])
        ->assertOk();

    expect(AgentChatSession::where('user_id', $user->id)->where('pnr_code', 'GA826')->exists())->toBeTrue();
});

test('dashboard meneruskan sesi chat baru ke sidebar kiri', function () {
    gdsBooking();
    $user = pnrUser();

    $this->actingAs($user)
        ->postJson('/api/pnr/verify', ['pnr' => 'ABC123', 'passenger' => 'Zakaria'])
        ->assertOk();

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertViewHas('chatSessions', function ($sessions) {
            return collect($sessions)->contains(fn ($s) => $s['pnr_code'] === 'ABC123');
        });
});
