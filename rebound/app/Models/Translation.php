<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// id: Baris terjemahan dinamis — satu baris per key UI dengan teks Bahasa Indonesia & Inggris.
//     Baris database menimpa nilai dari file lang statis (lang/id/messages.php & lang/en/messages.php).
// en: Dynamic translation row — one row per UI key carrying Indonesian & English text.
//     Database rows override the static lang file values (lang/id/messages.php & lang/en/messages.php).
class Translation extends Model
{
    protected $fillable = [
        'key',
        'text_id',
        'text_en',
    ];

    /**
     * id: Bangun katalog terjemahan gabungan — mulai dari file lang statis sebagai baseline,
     *     lalu setiap baris database menimpa key yang sama. Hasil berbentuk
     *     ['id' => [...], 'en' => [...]] siap diteruskan ke view/frontend.
     * en: Build the merged translation catalogue — starts from the static lang files as the
     *     baseline, then every database row overrides the same key. Returns
     *     ['id' => [...], 'en' => [...]] ready to be passed to the view/frontend.
     */
    public static function catalogue(): array
    {
        $catalogueId = trans('messages', [], 'id');
        $catalogueEn = trans('messages', [], 'en');

        foreach (static::query()->get() as $row) {
            if ($row->text_id !== null && $row->text_id !== '') {
                $catalogueId[$row->key] = $row->text_id;
            }
            if ($row->text_en !== null && $row->text_en !== '') {
                $catalogueEn[$row->key] = $row->text_en;
            }
        }

        return [
            'id' => $catalogueId,
            'en' => $catalogueEn,
        ];
    }
}
