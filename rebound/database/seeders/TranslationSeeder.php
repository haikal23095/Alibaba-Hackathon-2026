<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

// id: Sinkronisasi file lang statis ke tabel translations — menjadikan database sumber aktif
//     dengan nilai awal identik dengan lang/id/messages.php & lang/en/messages.php. Idempoten
//     (updateOrCreate) sehingga aman dijalankan berulang.
// en: Synchronize the static lang files into the translations table — making the database the
//     live source with initial values identical to lang/id/messages.php & lang/en/messages.php.
//     Idempotent (updateOrCreate) so it is safe to run repeatedly.
class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fileId = require base_path('lang/id/messages.php');
        $fileEn = require base_path('lang/en/messages.php');

        foreach ($fileId as $key => $textId) {
            Translation::updateOrCreate(
                ['key' => (string) $key],
                [
                    'text_id' => (string) $textId,
                    'text_en' => (string) ($fileEn[$key] ?? $textId),
                ]
            );
        }

        // id: Key yang hanya ada di file bahasa Inggris tetap ikut tersinkron
        // en: Keys that only exist in the English file are synchronized too
        foreach ($fileEn as $key => $textEn) {
            Translation::updateOrCreate(
                ['key' => (string) $key],
                [
                    'text_en' => (string) $textEn,
                    'text_id' => Translation::where('key', (string) $key)->value('text_id') ?? (string) ($fileId[$key] ?? $textEn),
                ]
            );
        }
    }
}
