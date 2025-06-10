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
        Schema::create('generalSettings', function (Blueprint $table) {
            $table->id('generalSettings_id');
            $table->string('gs_nama');
            $table->char('gs_value', 3);
            $table->string('gs_deskripsi', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generalSettings', function (Blueprint $table) {
            //
        });
    }
};
