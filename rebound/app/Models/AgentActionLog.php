<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentActionLog extends Model
{
    protected $fillable = [
        'session_id',
        'pnr_code',
        'tool_name',
        'tool_arguments',
        'policy_rationale',
        'status',
    ];

    // Casting struktur data AI agar terbaca sebagai JSON/Array
    protected $casts = [
        'tool_arguments' => 'array',
        'policy_rationale' => 'array',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AgentChatSession::class, 'session_id');
    }
}