<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use App\Models\UserModel;
use App\Models\Data_PendaftaranModel;
use Illuminate\Support\Facades\Auth;
use setasign\Fpdi\Fpdi;

class SertifikatController extends Controller
{
    public function index()
    {
        $user_id = Auth::user()->user_id;

        $sertif = DB::table('sertifikat')
        ->join('user', 'sertifikat.user_id', '=', 'user.user_id')
        ->select('sertifikat.*', 'user.username', 'user.nama_lengkap')
        ->where('sertifikat.user_id', $user_id)
        ->first(); // hanya ambil 1 baris

        return view('sertifikat.sertifikat', compact('sertif'));
    }

    public function dataSertifikat()
    {
        return view('sertifikat.dataSertifikat');
    }

    public function getSertif(Request $request)
    {
        $query = DB::table('sertifikat')
            ->join('user', 'sertifikat.user_id', '=', 'user.user_id')
            ->select('sertifikat.*', 'user.username', 'user.nama_lengkap');

        // Filter tahun (dari created_at)
        if ($request->filled('tahun')) {
            $query->whereYear('sertifikat.created_at', $request->tahun);
        }

        return DataTables::of($query)
            ->addColumn('username', function ($pendaftar) {
                return $pendaftar->username ?? '-';
            })
            ->addColumn('nama_lengkap', function ($pendaftar) {
                return $pendaftar->nama_lengkap ?? '-';
            })
            ->make(true);
    }

    public function toggleStatus(Request $request)
    {
        DB::table('sertifikat')
            ->where('sertifikat_id', $request->sertifikat_id)
            ->update(['is_taken' => $request->value]);

        return response()->json(['message' => 'Status berhasil diperbarui.']);
    }

    public function confirm_ajax(String $id)
    {
        $dataSertif = DB::table('sertifikat')
            ->join('user', 'sertifikat.user_id', '=', 'user.user_id')
            ->where('sertifikat.sertifikat_id', $id)
            ->select('sertifikat.*', 'user.username', 'user.nama_lengkap')
            ->first();

        return view('sertifikat.confirm_ajax', ['dataSertif' => $dataSertif]);
    }

    public function delete_ajax(String $id)
    {
        DB::table('sertifikat')->where('sertifikat_id', $id)->delete();
        return response()->json([
            'status'  => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }

    public function import(){
        return view('sertifikat.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'data_sertifPeserta' => ['required', 'mimes:xls,xlsx', 'max:5024']
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('data_sertifPeserta');

            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            $insert = [];
            if (count($data) > 1) {
                foreach ($data as $baris => $value) {
                    if ($baris === 0) continue; // skip header

                    $username = trim($value['B'] ?? '');

                    // Jika semua nilai kosong, lewati baris ini

                    $user = UserModel::where('username', $username)->first();
                    if (!$user) {
                        Log::warning("User tidak ditemukan: $username");
                        continue;
                    }

                    try {
                        $insert[] = [
                            'user_id' => $user->user_id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                if (count($insert) > 0) {
                    DB::table('sertifikat')->insertOrIgnore($insert);

                    return response()->json([
                        'status' => true,
                        'message' => 'Data berhasil diimport'
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Tidak ada data yang valid untuk diimport',
                        'debug' => [
                            'insert' => $insert
                        ]
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data yang diimport'
                ]);
            }
        }
        return redirect('/');
    }

    public function create_ajax(){
        return view('sertifikat.create_ajax');
    }

    public function store_ajax(Request $request){
        $request->validate([
            'username' => 'required|string|max:20',
        ]);

        $username = $request->input('username');
        $user = UserModel::where('username', $username)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Peserta tidak ditemukan'
            ]);
        }

        $insert = [[
            'user_id' => $user->user_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]];
        DB::table('sertifikat')->insertOrIgnore($insert);

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil disimpan'
        ]);
    }

}
