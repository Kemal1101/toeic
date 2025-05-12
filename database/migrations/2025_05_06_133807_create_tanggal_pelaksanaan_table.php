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
        Schema::create('tanggal_pelaksanaan', function (Blueprint $table) {
            $table->id('tanggal_pelaksanaan_id');
            $table->unsignedBigInteger('user_id')->index();
            $table->dateTime('tanggal_pelaksanaan');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanggalPelaksanaan');
    }
};
