<?php

use App\Models\MockGdsBooking;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// id: Pusat notifikasi operasional — notifikasi nyata dari tabel notifications menggantikan
//     kartu alert statis di navbar: daftar via API, tandai semua dibaca, alert otomatis saat
//     GDS menyatakan penerbangan terganggu, dan penerusan ke view dashboard.
// en: Operational notification center — real notifications from the notifications table replacing
//     the static navbar alert cards: API listing, mark-all-read, automatic alerts when the GDS
//     reports a disrupted flight, and forwarding to the dashboard view.

function notifBooking(string $status = 'delayed'): MockGdsBooking
{
    return MockGdsBooking::create([
        'pnr_code' => 'NTF123',
        'last_name' => 'ZAKARIA',
        'flight_number' => 'GA 826',
        'from_code' => 'CGK',
        'to_code' => 'SIN',
        'departure_time' => '2026-08-28 08:25:00',
        'cabin_class' => 'Economy',
        'status' => $status,
    ]);
}

test('user bisa mengambil daftar notifikasinya sendiri dari API', function () {
    $user = pnrUser();
    Notification::create([
        'user_id' => $user->id,
        'pnr_code' => 'GA826',
        'type' => 'delay',
        'title_id' => 'Keterlambatan Penerbangan',
        'title_en' => 'Flight Delay',
        'message_id' => 'GA826 resmi ditunda.',
        'message_en' => 'GA826 officially delayed.',
    ]);

    $this->actingAs($user)
        ->getJson('/api/notifications')
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('unread_count', 1)
        ->assertJsonPath('data.0.type', 'delay')
        ->assertJsonPath('data.0.pnr_code', 'GA826');
});

test('user tidak melihat notifikasi milik user lain', function () {
    $owner = pnrUser();
    $other = \App\Models\User::create([
        'name' => 'Other User',
        'email' => 'other@example.com',
        'password' => 'password',
    ]);
    Notification::create([
        'user_id' => $owner->id,
        'type' => 'delay',
        'title_id' => 'Keterlambatan Penerbangan',
        'title_en' => 'Flight Delay',
        'message_id' => 'Pesan rahasia.',
        'message_en' => 'Secret message.',
    ]);

    $this->actingAs($other)
        ->getJson('/api/notifications')
        ->assertOk()
        ->assertJsonPath('data', []);
});

test('tandai dibaca mengubah semua notifikasi user menjadi sudah dibaca', function () {
    $user = pnrUser();
    foreach (['delay', 'alternative', 'baggage'] as $type) {
        Notification::create([
            'user_id' => $user->id,
            'pnr_code' => 'GA826',
            'type' => $type,
            'title_id' => 'Judul',
            'title_en' => 'Title',
            'message_id' => 'Isi',
            'message_en' => 'Body',
        ]);
    }

    $this->actingAs($user)
        ->postJson('/api/notifications/read-all')
        ->assertOk()
        ->assertJsonPath('data.marked_read', 3);

    expect(Notification::where('user_id', $user->id)->where('is_read', false)->count())->toBe(0);
});

test('guest tidak bisa mengakses pusat notifikasi', function () {
    $this->getJson('/api/notifications')->assertUnauthorized();
    $this->postJson('/api/notifications/read-all')->assertUnauthorized();
});

test('verifikasi PNR yang delayed di GDS otomatis membuat notifikasi delay', function () {
    notifBooking('delayed');
    $user = pnrUser();

    $this->actingAs($user)
        ->postJson('/api/pnr/verify', ['pnr' => 'NTF123', 'passenger' => 'Zakaria'])
        ->assertOk();

    $notif = Notification::where('user_id', $user->id)->where('pnr_code', 'NTF123')->first();
    expect($notif)->not->toBeNull()
        ->and($notif->type)->toBe('delay')
        ->and($notif->message_id)->toContain('GA 826');
});

test('verifikasi ulang tidak menduplikasi notifikasi gangguan', function () {
    notifBooking('delayed');
    $user = pnrUser();

    $this->actingAs($user)->postJson('/api/pnr/verify', ['pnr' => 'NTF123', 'passenger' => 'Zakaria'])->assertOk();
    $this->actingAs($user)->postJson('/api/pnr/verify', ['pnr' => 'NTF123', 'passenger' => 'Zakaria'])->assertOk();

    expect(Notification::where('user_id', $user->id)->where('pnr_code', 'NTF123')->count())->toBe(1);
});

test('PNR berstatus on_time tidak membuat notifikasi gangguan', function () {
    notifBooking('on_time');
    $user = pnrUser();

    $this->actingAs($user)
        ->postJson('/api/pnr/verify', ['pnr' => 'NTF123', 'passenger' => 'Zakaria'])
        ->assertOk();

    expect(Notification::where('user_id', $user->id)->count())->toBe(0);
});

test('aktivasi tiket dari database yang delayed juga membuat notifikasi', function () {
    notifBooking('delayed');
    $user = pnrUser();

    $this->actingAs($user)
        ->postJson('/api/pnr/activate', ['pnr_code' => 'NTF123', 'last_name' => 'Zakaria'])
        ->assertOk();

    expect(Notification::where('user_id', $user->id)->where('type', 'delay')->exists())->toBeTrue();
});

test('dashboard meneruskan notifikasi user ke view', function () {
    $user = pnrUser();
    Notification::create([
        'user_id' => $user->id,
        'pnr_code' => 'GA826',
        'type' => 'delay',
        'title_id' => 'Keterlambatan Penerbangan',
        'title_en' => 'Flight Delay',
        'message_id' => 'GA826 resmi ditunda.',
        'message_en' => 'GA826 officially delayed.',
    ]);

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertViewHas('notifications', function ($notifications) {
            return collect($notifications)->contains(fn ($n) => $n['pnr_code'] === 'GA826' && $n['type'] === 'delay');
        });
});
