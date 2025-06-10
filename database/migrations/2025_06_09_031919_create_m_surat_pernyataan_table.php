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
        Schema::table('surat_pernyataan', function (Blueprint $table) {
            $table->id('surat_pernyataan_id');
            $table->unsignedBigInteger('user_id')->index();
            $table->string('sertifikat1')->nullable(); // misalnya untuk menyimpan nama file atau path
            $table->string('sertifikat2')->nullable(); // misalnya untuk menyimpan nama file atau path
            $table->enum('verifikasi_data', ['PENDING','DITOLAK', 'TERVERIFIKASI'])->default('PENDING');
            $table->string('notes_ditolak', 200);
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_pernyataan', function (Blueprint $table) {
            //
        });
    }
};
