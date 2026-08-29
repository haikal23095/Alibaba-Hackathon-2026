<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// id: Hasil rebooking user — satu baris per user + PNR berisi objek penerbangan alternatif
//     yang dipilih. Menggantikan penyimpanan localStorage 'rebound_rebooked_<PNR>' di frontend
//     agar status rebooked bertahan lintas perangkat dan menjadi sumber kebenaran di server.
// en: A user's rebooking result — one row per user + PNR holding the chosen alternative flight
//     object. Replaces the frontend localStorage 'rebound_rebooked_<PNR>' storage so the
//     rebooked state survives across devices and becomes the server-side source of truth.
class Rebooking extends Model
{
    protected $fillable = [
        'user_id',
        'pnr_code',
        'alternative_flight',
    ];

    /**
     * id: Objek JSON alternative_flight otomatis jadi array PHP saat dibaca.
     * en: The alternative_flight JSON column is cast to a PHP array on read.
     */
    protected function casts(): array
    {
        return [
            'alternative_flight' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
