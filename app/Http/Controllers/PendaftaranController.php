<?php

namespace App\Http\Controllers;

use App\Models\Data_PendaftaranModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class PendaftaranController extends Controller
{
    public function index()
    {
        $user_id = Auth::user()->user_id;

        $sudahTerdaftar = Data_PendaftaranModel::where('user_id', $user_id)->exists();

        if ($sudahTerdaftar) {
            return redirect()->back()->with('status', 'anda_sudah_mendaftar');
        }

        $username = Auth::user()->username;
        $nama_lengkap = Auth::user()->nama_lengkap;

        return view('pendaftaran.pendaftaran', compact('username', 'nama_lengkap'));
    }

    public function store_ajax(Request $request)
    {
        // Cek apakah request berupa AJAX atau ingin JSON response
        if ($request->ajax() || $request->wantsJson()) {
            // Validasi
            $request->validate([
                'pas_foto' => 'required|file|mimes:jpg,jpeg,png|max:2048',
                'ktm_atau_ktp' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
                // validasi lain...
            ]);

            // Simpan file dengan nama baru
            if ($request->hasFile('pas_foto')) {
                $foto = $request->file('pas_foto');
                $foto_nama = 'pasfoto_' .  Auth::user()->username . '.' . $foto->getClientOriginalExtension();
                $foto->move(public_path('uploads/pasfoto'), $foto_nama);
            }

            if ($request->hasFile('ktm_atau_ktp')) {
                $ktp = $request->file('ktm_atau_ktp');
                $ktp_nama = 'ktmktp_' .  Auth::user()->username . '.' . $ktp->getClientOriginalExtension();
                $ktp->move(public_path('uploads/ktmktp'), $ktp_nama);
            }

            Data_PendaftaranModel::create([
                'user_id' => Auth::user()->user_id,
                'nik' => $request->nik,
                'no_wa' =>  $request->no_wa,
                'alamat_asal' => $request->alamat_asal,
                'alamat_sekarang' => $request->alamat_sekarang,
                'program_studi' => $request->program_studi,
                'jurusan' => $request->jurusan,
                'kampus' => $request->kampus,
                'pas_foto' => $foto_nama,
                'ktm_atau_ktp' => $ktp_nama

            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data user berhasil disimpan'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Request tidak valid'
        ], 400);
    }

    public function data_pendaftar()
    {
        return view('pendaftaran.dataPendaftar');
    }

    public function getPendaftar(Request $request)
    {
        $query = Data_PendaftaranModel::with('user'); // eager loading user (username dan nama_lengkap)

        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }
        // jika tidak di-filter, biarkan tampil semua data


        return DataTables::of($query)
            ->addColumn('username', function ($pendaftar) {
                return $pendaftar->user->username ?? '-';
            })
            ->addColumn('nama_lengkap', function ($pendaftar) {
                return $pendaftar->user->nama_lengkap ?? '-';
            })
           ->addColumn('pas_foto', function ($pendaftar) {
                $url = asset('uploads/pasfoto/' . $pendaftar->pas_foto);
                return "<img src='{$url}' alt='Pas Foto' width='80'>";
            })
            ->addColumn('ktm_atau_ktp', function ($pendaftar) {
                $url = asset('uploads/ktmktp/' . $pendaftar->ktm_atau_ktp);
                return "<img src='{$url}' alt='KTP/KTM' width='80'>";
            })
            ->rawColumns(['pas_foto', 'ktm_atau_ktp'])
            // ->addColumn('aksi', function ($pendaftar) {
            //     // optional: tambahkan tombol aksi jika dibutuhkan
            //     return '<button class="btn btn-info btn-sm">Detail</button>';
            // })
            // ->rawColumns(['aksi']) // jika pakai HTML di kolom
            ->make(true);
    }

    public function verifikasi(String $id)
    {
        $dataPendaftar = Data_PendaftaranModel::find($id);

        if (!$dataPendaftar) {
            abort(404, 'Data pendaftar tidak ditemukan.');
        }

        $user = UserModel::select('user_id', 'username', 'nama_lengkap')
                    ->where('user_id', $dataPendaftar->user_id)
                    ->first();

        return view('pendaftaran.dataPendaftarModal', [
            'dataPendaftar' => $dataPendaftar,
            'user' => $user
        ]);
    }

    public function notes(String $id)
    {
        $dataPendaftar = Data_PendaftaranModel::find($id);

        if (!$dataPendaftar) {
            return response()->json([
                'message' => 'Data pendaftar tidak ditemukan.'
            ], 404);
        }

        $user = UserModel::select('user_id', 'username', 'nama_lengkap')
                    ->where('user_id', $dataPendaftar->user_id)
                    ->first();

        return response()->view('pendaftaran.notesTolakModal', [
            'dataPendaftar' => $dataPendaftar,
            'user' => $user
        ]);
    }


    public function verifikasiSetuju($id)
    {
        $data = Data_PendaftaranModel::find($id);

        if (!$data) {
            return redirect()->back()->with('error', 'Data pendaftar tidak ditemukan.');
        }

        $data->verifikasi_data = 'TERVERIFIKASI';
        $data->save();

        return redirect()->back()->with('success', 'Data berhasil diverifikasi.');
    }

    public function verifikasiTolak(Request $request, $id)
    {
        $data = Data_PendaftaranModel::find($id);

        if (!$data) {
            return redirect()->back()->with('error', 'Data pendaftar tidak ditemukan.');
        }

        $data->verifikasi_data = 'DITOLAK';
        $data->notes_ditolak = $request->notes_ditolak;
        $data->save();

        return redirect()->back()->with('error', 'Data berhasil ditolak.');
    }

}
