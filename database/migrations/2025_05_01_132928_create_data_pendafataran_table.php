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
        Schema::create('data_pendaftaran', function (Blueprint $table) {
            $table->id('data_pendaftaran_id'); // primary key auto increment
            $table->unsignedBigInteger('user_id')->index();
            $table->string('nim', 10)->unique();
            $table->string('nik', 16)->unique();
            $table->string('no_wa', 13)->unique();
            $table->text('alamat_asal');
            $table->text('alamat_sekarang');
            $table->string('program_studi', 100);
            $table->string('jurusan', 100);
            $table->string('kampus', 100);
            $table->string('pas_foto')->nullable(); // misalnya untuk menyimpan nama file atau path
            $table->string('ktm_atau_ktp')->nullable(); // misalnya untuk menyimpan nama file atau path

            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_pendaftaran');
    }
};
