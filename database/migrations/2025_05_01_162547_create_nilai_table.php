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
        Schema::create('nilai', function (Blueprint $table) {
            $table->id('nilai_id');
            $table->unsignedBigInteger('user_id')->index();
            $table->dateTime('tanggal_pelaksanaan'); //format YYYY-MM-DD
            $table->string('nilai_path')->nullable(); // misalnya untuk menyimpan nama file atau path
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai');
    }
};
