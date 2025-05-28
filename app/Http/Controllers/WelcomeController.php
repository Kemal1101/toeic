<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserModel;
use App\Models\Data_PendaftaranModel;
use Carbon\Carbon;

class WelcomeController extends Controller
{
    public function indexMahasiswa() {
        $user_id = Auth::user()->user_id;
        $user = Auth::user();

        // Ambil data pendaftaran milik user yang login
        $Data_PendaftaranModel = Data_PendaftaranModel::where('user_id', $user_id)->first();

        // Kirim ke view
        return view('dashboardMahasiswa', compact('Data_PendaftaranModel', 'user'));
    }

    public function indexUpa(){
        $tahunIni = Carbon::now()->year;

        $jumlahPendaftar = Data_PendaftaranModel::whereYear('created_at', $tahunIni)->count();

        $mahasiswaIds = UserModel::where('role_id', 2)
            ->whereYear('created_at', $tahunIni)
            ->pluck('user_id');

        $sudahMendaftarIds = Data_PendaftaranModel::pluck('user_id')->unique();

        $jumlahBelumMendaftar = UserModel::whereIn('user_id', $mahasiswaIds)
            ->whereNotIn('user_id', $sudahMendaftarIds)
            ->count();

        $jumlahDitolak = Data_PendaftaranModel::whereYear('created_at', $tahunIni)
            ->where('verifikasi_data', 'DITOLAK')
            ->count();

        $jumlahPending = Data_PendaftaranModel::whereYear('created_at', $tahunIni)
            ->where('verifikasi_data', 'PENDING')
            ->count();

        $jumlahTerverifikasi = Data_PendaftaranModel::whereYear('created_at', $tahunIni)
            ->where('verifikasi_data', 'TERVERIFIKASI')
            ->count();

        return view('dashboardUpa', compact(
            'jumlahPendaftar',
            'jumlahBelumMendaftar',
            'jumlahDitolak',
            'jumlahPending',
            'jumlahTerverifikasi'
        ));
    }

}
