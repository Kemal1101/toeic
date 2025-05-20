<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Data_PendaftaranModel;

class WelcomeController extends Controller
{
    public function index() {
        $user_id = Auth::user()->user_id;
        $user = Auth::user();

        // Ambil data pendaftaran milik user yang login
        $dataPendaftaran = Data_PendaftaranModel::where('user_id', $user_id)->first();

        // Kirim ke view
        return view('welcome', compact('dataPendaftaran', 'user'));
    }

}
