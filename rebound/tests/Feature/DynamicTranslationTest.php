<?php

use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// id: Terjemahan dinamis — katalog UI kini bersumber dari tabel translations (menimpa file
//     lang statis) dengan API baca & upsert, bukan lagi file statis semata.
// en: Dynamic translations — the UI catalogue now comes from the translations table
//     (overriding static lang files) with read & upsert APIs, no longer static files only.

test('dashboard memakai file lang statis sebagai fallback saat tabel kosong', function () {
    $user = pnrUser();

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertViewHas('translations', function ($translations) {
            return ($translations['id']['app_name'] ?? null) === 'REBOUND'
                && ($translations['en']['app_name'] ?? null) === 'REBOUND';
        });
});

test('baris database menimpa nilai file lang di dashboard', function () {
    $user = pnrUser();
    Translation::create([
        'key' => 'app_name',
        'text_id' => 'REBOUND Dinamis',
        'text_en' => 'REBOUND Dynamic',
    ]);

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertViewHas('translations', function ($translations) {
            return ($translations['id']['app_name'] ?? null) === 'REBOUND Dinamis'
                && ($translations['en']['app_name'] ?? null) === 'REBOUND Dynamic'
                // id: key lain tanpa baris DB tetap memakai nilai file
                // en: other keys without a DB row keep the file value
                && ($translations['en']['assistant'] ?? null) === 'Assistant';
        });
});

test('API katalog terjemahan mengembalikan gabungan DB dan file', function () {
    $user = pnrUser();
    Translation::create([
        'key' => 'tagline',
        'text_id' => 'Asisten Penerbangan AI',
        'text_en' => 'AI Flight Assistant',
    ]);

    $this->actingAs($user)
        ->getJson('/api/translations')
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('db_overrides', 1)
        ->assertJsonPath('data.id.tagline', 'Asisten Penerbangan AI')
        ->assertJsonPath('data.en.tagline', 'AI Flight Assistant')
        ->assertJsonPath('data.en.my_trips', 'My Trips');
});

test('guest tidak bisa mengakses endpoint terjemahan', function () {
    $this->getJson('/api/translations')->assertStatus(401);
    $this->postJson('/api/translations', ['key' => 'app_name', 'text_id' => 'x', 'text_en' => 'y'])
        ->assertStatus(401);
});

test('upsert terjemahan membuat lalu memperbarui baris yang sama', function () {
    $user = pnrUser();

    $this->actingAs($user)
        ->postJson('/api/translations', [
            'key' => 'monitoring_active_trip',
            'text_id' => 'Memantau 1 perjalanan aktif',
            'text_en' => 'Monitoring 1 active trip (updated)',
        ])
        ->assertOk()
        ->assertJsonPath('data.key', 'monitoring_active_trip');

    // id: Kiriman kedua dengan key sama memperbarui baris, bukan menambah baris baru
    // en: A second request with the same key updates the row instead of inserting a new one
    $this->actingAs($user)
        ->postJson('/api/translations', [
            'key' => 'monitoring_active_trip',
            'text_id' => 'Memantau perjalanan aktif',
            'text_en' => 'Monitoring active trips',
        ])
        ->assertOk();

    expect(Translation::where('key', 'monitoring_active_trip')->count())->toBe(1);

    $this->actingAs($user)
        ->getJson('/api/translations')
        ->assertJsonPath('data.id.monitoring_active_trip', 'Memantau perjalanan aktif')
        ->assertJsonPath('data.en.monitoring_active_trip', 'Monitoring active trips');
});

test('key terjemahan dengan karakter tidak valid ditolak', function () {
    $user = pnrUser();

    $this->actingAs($user)
        ->postJson('/api/translations', [
            'key' => 'key tidak valid!',
            'text_id' => 'Teks',
            'text_en' => 'Text',
        ])
        ->assertStatus(422);
});
