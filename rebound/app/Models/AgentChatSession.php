<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgentChatSession extends Model
{
    protected $fillable = [
        'user_id',
        'pnr_code',
        'context_summary',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Satu sesi punya banyak pesan
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'session_id');
    }

    // Relasi: Satu sesi punya riwayat log aksi AI
    public function actionLogs(): HasMany
    {
        return $this->hasMany(AgentActionLog::class, 'session_id');
    }
}