<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// id: Tabel terjemahan dinamis — katalog teks UI (key + teks id/en) yang sebelumnya beku
//     di lang/id/messages.php & lang/en/messages.php kini dapat diubah lewat database/API
//     tanpa deploy ulang. Baris tabel menimpa nilai file; key tanpa baris tetap memakai file.
// en: Dynamic translations table — the UI text catalogue (key + id/en text) previously frozen
//     in lang/id/messages.php & lang/en/messages.php can now be edited via database/API without
//     redeploying. Table rows override file values; keys without a row keep the file value.
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();
            $table->text('text_id');
            $table->text('text_en');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
