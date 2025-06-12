<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NilaiModel;
use App\Models\UserModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    public function index()
    {
        return view('nilai.dataNilai');
    }

    public function getNilai(Request $request)
    {
        $query = NilaiModel::with('user'); // eager loading user (username dan nama_lengkap)

        // Filter tahun (dari created_at)
        if ($request->filled('tahun')) {
            $query->whereYear('nilai.created_at', $request->tahun);
        }

        return DataTables::of($query)
            ->addColumn('username', function ($pendaftar) {
                return $pendaftar->user->username ?? '-';
            })
            ->addColumn('nama_lengkap', function ($pendaftar) {
                return $pendaftar->user->nama_lengkap ?? '-';
            })
            ->addColumn('listening', function ($pendaftar) {
                return $pendaftar->listening ?? '-';
            })
            ->addColumn('reading', function ($pendaftar) {
                return $pendaftar->reading ?? '-';
            })
            ->addColumn('total', function ($pendaftar) {
                return $pendaftar->total ?? '-';
            })
            ->rawColumns(['pas_foto', 'ktm_atau_ktp'])
            ->make(true);
    }

    public function nilai(Request $request){
        $user_id = Auth::user()->user_id;

        $nilai = NilaiModel::with('user')
                    ->where('user_id', $user_id)
                    ->first();

        return view('nilai.nilai', compact('nilai'));
    }

    public function import(){
        return view('nilai.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'data_nilai' => ['required', 'mimes:xls,xlsx', 'max:5024']
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('data_nilai');

            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            $insert = [];
            if (count($data) > 1) {
                foreach ($data as $baris => $value) {
                    if ($baris === 0) continue; // skip header

                    $username = trim($value['C'] ?? '');
                    $listening = $value['D'] ?? null;
                    $reading = $value['E'] ?? null;
                    $total = $value['F'] ?? null;

                    // Jika semua nilai kosong, lewati baris ini
                    if (is_null($listening) && is_null($reading) && is_null($total)) {
                        continue;
                    }

                    $user = UserModel::where('username', $username)->first();
                    if (!$user) {
                        Log::warning("User tidak ditemukan: $username");
                        continue;
                    }

                    try {
                        $insert[] = [
                            'user_id' => $user->user_id,
                            'listening' => (int) $listening,
                            'reading' => (int) $reading,
                            'total' => (int) $total,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                if (count($insert) > 0) {
                    NilaiModel::insertOrIgnore($insert);

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

    public function confirm_ajax(String $id){
        $nilai = NilaiModel::find($id);
        return view('nilai.confirm_ajax', ['nilai' => $nilai]);
    }

    public function delete_ajax(Request $request){
        $nilai = NilaiModel::find(request()->id);
        $nilai->delete();
        return response()->json([
            'status' => true,
            'message' => 'Data nilai berhasil dihapus.'
        ]);
    }
}
