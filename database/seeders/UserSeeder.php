<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'role_id' => 1,
            'username' => 'admin',
            'password' => Hash::make('1234567890'),
            'nama_lengkap' => 'Muhammad Kemal Syahru Ramadhan',
            'tanggal_lahir' => '2003-11-01'
        ];

        DB::table('user')->insert($data);
    }
}
