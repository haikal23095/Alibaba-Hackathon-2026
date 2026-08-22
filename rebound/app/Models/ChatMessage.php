<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    // Nonaktifkan default timestamps karena kita pakai 'sent_at'
    public $timestamps = false; 

    protected $fillable = [
        'session_id',
        'sender',
        'message_content',
        'dynamic_ui_payload',
        'sent_at',
    ];

    // Casting JSON payload agar otomatis menjadi array/object di PHP
    protected $casts = [
        'dynamic_ui_payload' => 'array',
        'sent_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AgentChatSession::class, 'session_id');
    }
}