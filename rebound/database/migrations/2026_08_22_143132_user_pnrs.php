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
        Schema::create('user_pnrs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('pnr_code', 6); // Kode booking (misal: ABC123)
            $table->string('last_name'); // Keamanan tambahan untuk mencocokkan PNR
            $table->enum('status', ['active', 'changed', 'cancelled', 'flown'])->default('active');
            $table->timestamps();

            // Pastikan satu user tidak memasukkan PNR yang sama berulang kali
            $table->unique(['user_id', 'pnr_code']);
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
