<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agent_action_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('agent_chat_sessions');
            $table->string('pnr_code', 6);
            $table->string('tool_name'); // misal: 'reissue_ticket', 'hold_seat'[cite: 2]
            $table->json('tool_arguments'); // Parameter yang dikirim AI (misal: jadwal baru)
            $table->json('policy_rationale')->nullable(); // Alasan AI mengambil keputusan (berdasarkan Fare Rules)[cite: 2]
            $table->string('status')->default('success'); // success, failed, requires_human
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
