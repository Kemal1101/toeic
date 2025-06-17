<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'gs_nama' => 'isPendaftaranOpen',
            'gs_value' => 'y',
            'gs_deskripsi' => 'untuk settingan pendaftaran dibuka atau ditutup'
        ];

        DB::table('generalsettings')->insert($data);
    }
}
