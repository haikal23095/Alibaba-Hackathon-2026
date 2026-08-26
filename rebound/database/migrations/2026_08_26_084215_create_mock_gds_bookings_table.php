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
        Schema::create('mock_gds_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('pnr_code', 6);
            $table->string('last_name');
            $table->string('flight_number');
            $table->string('from_code', 3);
            $table->string('to_code', 3);
            $table->dateTime('departure_time');
            $table->string('cabin_class');
            $table->string('status')->default('delayed'); // Sengaja dibuat delayed untuk demo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mock_gds_bookings');
    }
};
