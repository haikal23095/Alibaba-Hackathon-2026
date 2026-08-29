<?php

namespace App\Http\Controllers;

use App\Models\Translation;
use Illuminate\Http\Request;

// id: Endpoint terjemahan dinamis — katalog gabungan (database menimpa file lang statis)
//     dan pembuatan/pembaruan baris terjemahan tanpa deploy ulang.
// en: Dynamic translation endpoints — the merged catalogue (database overriding static lang
//     files) and creation/update of translation rows without redeploying.
class TranslationController extends Controller
{
    /**
     * id: Katalog terjemahan gabungan untuk konsumen API — bentuknya sama dengan yang
     *     diteruskan rute dashboard ke view (translations.id & translations.en).
     * en: The merged translation catalogue for API consumers — same shape as what the
     *     dashboard route passes to the view (translations.id & translations.en).
     */
    public function index()
    {
        $catalogue = Translation::catalogue();

        return response()->json([
            'status' => 'success',
            'db_overrides' => Translation::count(),
            'data' => $catalogue,
        ], 200);
    }

    /**
     * id: Buat atau perbarui satu baris terjemahan (upsert berdasarkan key). Teks id/en
     *     yang dikirim menggantikan nilai lama; key tanpa baris baru otomatis tetap
     *     memakai nilai file lang statis.
     * en: Create or update a single translation row (upsert by key). The submitted id/en
     *     text replaces the old values; keys left untouched keep the static lang file values.
     */
    public function upsert(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:80|regex:/^[a-z0-9_.]+$/i',
            'text_id' => 'required|string|max:2000',
            'text_en' => 'required|string|max:2000',
        ]);

        $translation = Translation::updateOrCreate(
            ['key' => strtolower($validated['key'])],
            [
                'text_id' => $validated['text_id'],
                'text_en' => $validated['text_en'],
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Terjemahan berhasil disimpan.',
            'data' => [
                'id' => $translation->id,
                'key' => $translation->key,
                'text_id' => $translation->text_id,
                'text_en' => $translation->text_en,
            ],
        ], 200);
    }
}
