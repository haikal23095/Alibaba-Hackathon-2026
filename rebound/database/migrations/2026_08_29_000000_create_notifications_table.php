<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// id: Tabel notifikasi operasional — alert nyata (delay, alternatif, bagasi, rebooking)
//     milik tiap user, menggantikan kartu notifikasi statis yang dulu di-hardcode di navbar.
// en: Operational notifications table — real alerts (delay, alternatives, baggage, rebooking)
//     belonging to each user, replacing the static notification cards previously hardcoded in the navbar.
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // id: PNR terkait (nullable karena ada notifikasi yang tidak terikat tiket)
            // en: Related PNR (nullable because some notifications are not tied to a ticket)
            $table->string('pnr_code', 10)->nullable();
            // id: Jenis alert: delay | cancelled | alternative | rebooked | baggage | system
            // en: Alert type: delay | cancelled | alternative | rebooked | baggage | system
            $table->string('type', 30);
            // id: Judul & isi dwibahasa (id/en), mengikuti pola penyimpanan konten dwibahasa aplikasi
            // en: Bilingual title & body (id/en), following the app's bilingual content storage pattern
            $table->string('title_id');
            $table->string('title_en');
            $table->text('message_id');
            $table->text('message_en');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
