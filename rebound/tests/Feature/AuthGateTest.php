<?php

// Memastikan semua halaman/endpoint internal tidak bisa diakses tanpa login
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeUser(): User
{
    return User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
    ]);
}

test('guest ditolak dari dashboard', function () {
    $this->get('/')->assertRedirect(route('login'));
});

test('guest ditolak dari endpoint API internal', function () {
    $this->getJson('/api/user-profile')->assertUnauthorized();
    $this->getJson('/api/flights/alternatives')->assertUnauthorized();
    $this->postJson('/api/pnr/verify')->assertUnauthorized();
    $this->postJson('/api/chat/send', ['message' => 'halo'])->assertUnauthorized();
});

test('endpoint API bisa diakses setelah login via sesi web', function () {
    $this->actingAs(makeUser());

    $this->getJson('/api/user-profile')->assertOk();
    $this->getJson('/api/flights/alternatives')->assertOk();
});

test('user yang sudah login dialihkan dari halaman login/register ke dashboard', function () {
    $this->actingAs(makeUser());

    $this->get('/login')->assertRedirect('/');
    $this->get('/register')->assertRedirect('/');
});

test('endpoint publik tetap terbuka', function () {
    $this->getJson('/api/health')->assertOk();
    $this->get('/login')->assertOk();
    $this->get('/register')->assertOk();
});

// id: Regresi — tanpa $middleware->statefulApi() di bootstrap/app.php, cookie sesi browser
//     tidak pernah dibaca oleh route /api/* sehingga auth:sanctum membalas 401
//     (bug "Unauthenticated" saat verifikasi PNR dari modal). actingAs() tidak menangkap bug ini.
// en: Regression — without $middleware->statefulApi() in bootstrap/app.php, the browser session cookie
//     is never read by /api/* routes so auth:sanctum answers 401
//     (the "Unauthenticated" bug when verifying a PNR from the modal). actingAs() does not catch this.
test('grup middleware api menyertakan Sanctum stateful', function () {
    $groups = app('router')->getMiddlewareGroups();

    expect($groups['api'])->toContain(\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class);
});

test('sesi login browser dikenali oleh endpoint auth:sanctum', function () {
    $user = makeUser();

    // id: Isi sesi persis seperti SessionGuard saat login sungguhan (kunci login_web_<sha1>)
    // en: Populate the session exactly like SessionGuard does on a real login (login_web_<sha1> key)
    $this->withSession([
        'login_web_'.sha1(\Illuminate\Auth\SessionGuard::class) => $user->id,
    ]);

    $this->getJson('/api/user-profile')->assertOk();
});
