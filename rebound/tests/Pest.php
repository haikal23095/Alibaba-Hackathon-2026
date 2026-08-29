<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
 // ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

// id: Helper lintas file tes — user uji untuk alur PNR/chat.
// en: Cross-file test helper — test user for the PNR/chat flows.
function pnrUser(): \App\Models\User
{
    return \App\Models\User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
    ]);
}

// id: Mesin simulasi AI sudah dihapus — helper ini memalsukan endpoint chat Qwen lewat
//     Http::fake() dengan API key uji sehingga tes jalur AI deterministik tanpa panggilan nyata.
// en: The AI simulation engine has been removed — this helper fakes the Qwen chat endpoint via
//     Http::fake() with a test API key so AI-path tests stay deterministic without real calls.
function fakeQwenChat(): void
{
    config([
        'services.qwen.api_key' => 'testing',
        'services.qwen.endpoint' => 'https://qwen.test/chat/completions',
        'services.qwen.model' => 'qwen-test',
    ]);

    \Illuminate\Support\Facades\Http::fake([
        'qwen.test/*' => \Illuminate\Support\Facades\Http::response([
            'choices' => [[
                'message' => [
                    'content' => json_encode([
                        'type' => 'text',
                        'replyId' => 'Balasan uji coba.',
                        'replyEn' => 'Test reply.',
                        'showTicketPolicy' => false,
                        'showRecommendation' => false,
                    ]),
                ],
            ]],
        ]),
    ]);
}
