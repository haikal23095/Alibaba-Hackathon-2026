<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// id: Tabel rebooking — status hasil rebooking user (penerbangan alternatif terpilih) yang
//     sebelumnya disimpan di localStorage frontend kini dipindah ke database agar bertahan
//     lintas perangkat/browser dan tercatat di server. Satu baris per kombinasi user + PNR;
//     objek penerbangan alternatif disimpan utuh sebagai JSON di alternative_flight.
// en: Rebookings table — the user's rebooking result (chosen alternative flight) previously kept
//     in frontend localStorage now lives in the database so it survives across devices/browsers
//     and is recorded server-side. One row per user + PNR pair; the alternative flight object is
//     stored whole as JSON in alternative_flight.
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rebookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('pnr_code', 10);
            $table->json('alternative_flight'); // Objek alternatif lengkap (flightNumber, airline, gate, depTime, dst.)
            $table->timestamps();
            $table->unique(['user_id', 'pnr_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rebookings');
    }
};
