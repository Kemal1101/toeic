<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneralSettingController extends Controller
{
    public function togglePendaftaran(Request $request)
    {
        $value = $request->value;

        DB::table('generalSettings')
            ->updateOrInsert(
                ['gs_nama' => 'isPendaftaranOpen'],
                ['gs_value' => $value]
            );

        if($value == 'n'){
            return response()->json([
                'status' => true,
                'message' => 'Pendaftaran Berhasil Ditutup'
            ]);
        }elseif($value == 'y'){
            return response()->json([
                'status' => true,
                'message' => 'Pendaftaran Berhasil Dibuka'
            ]);
        }
    }
}
