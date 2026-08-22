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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('agent_chat_sessions')->onDelete('cascade');
            $table->enum('sender', ['user', 'agent', 'system']); // 'system' untuk notifikasi delay
            $table->text('message_content');
            $table->json('dynamic_ui_payload')->nullable(); // Data JSON untuk merender kartu penerbangan/QR Code
            $table->timestamp('sent_at')->useCurrent();
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
