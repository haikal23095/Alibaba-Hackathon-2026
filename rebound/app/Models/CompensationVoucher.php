<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompensationVoucher extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'pnr_code',
        'qr_code_string',
        'voucher_type',
        'is_redeemed',
        'issued_at',
    ];

    protected $casts = [
        'is_redeemed' => 'boolean',
        'issued_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}