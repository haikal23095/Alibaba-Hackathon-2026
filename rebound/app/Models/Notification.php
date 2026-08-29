<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// id: Notifikasi operasional milik user (delay, alternatif, bagasi, rebooking, sistem).
//     Nama kelas sengaja bukan "DatabaseNotification" agar tidak tertukar dengan
//     notifikasi bawaan Laravel — ini tabel notifications khusus REBOUND.
// en: Operational notifications belonging to a user (delay, alternatives, baggage, rebooking, system).
//     The class name is intentionally distinct from Laravel's built-in DatabaseNotification —
//     this is REBOUND's own notifications table.
class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'pnr_code',
        'type',
        'title_id',
        'title_en',
        'message_id',
        'message_en',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
