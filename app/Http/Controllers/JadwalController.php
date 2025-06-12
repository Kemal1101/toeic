<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalModel;
use App\Models\UserModel;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwal = JadwalModel::with('user')->get();
        return view('jadwal.jadwal', compact('jadwal'));
    }

    public function getJadwal(Request $request)
    {
        $query = JadwalModel::with('user');

        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        return DataTables::of($query)
            ->addColumn('nama_lengkap', function ($jadwal) {
                return $jadwal->user ? $jadwal->user->nama_lengkap : '-';
            })
            ->addColumn('username', function ($jadwal) {
                return $jadwal->user ? $jadwal->user->username : '-';
            })
            ->addColumn('tanggal_pelaksanaan_tanggal', function ($jadwal) {
                \Carbon\Carbon::setLocale('id');

                return $jadwal->tanggal_pelaksanaan
                    ? \Carbon\Carbon::parse($jadwal->tanggal_pelaksanaan)->translatedFormat('d F Y')
                    : '-';
            })
            ->addColumn('tanggal_pelaksanaan_jam', function ($jadwal) {
                return $jadwal->tanggal_pelaksanaan ? \Carbon\Carbon::parse($jadwal->tanggal_pelaksanaan)->format('h:i A') : '-';
            })
            ->addColumn('link_zoom', function ($jadwal) {
                return $jadwal->link_zoom ? $jadwal->link_zoom : '-';
            })

            ->make(true);
    }

    public function getJadwalPelaksanaan(Request $request)
    {
        $user_id = Auth::user()->user_id;

        $jadwal = JadwalModel::with('user')
                    ->where('user_id', $user_id)
                    ->first();

        return view('jadwal.jadwal_pelaksanaan', compact('jadwal'));
    }

    public function import()
     {
         return view('jadwal.import');
     }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'data_jadwal_peserta' => ['required', 'mimes:xls,xlsx', 'max:5024']
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('data_jadwal_peserta');

            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            $insert = [];
            if (count($data) > 1) {
                foreach ($data as $baris => $value) {
                    if ($baris === 0) continue; // skip header

                    $username = trim($value['A'] ?? '');
                    $tanggal = $value['B'] ?? null;
                    $jam = $value['C'] ?? null;
                    $link_zoom = $value['D'] ?? null;

                    if (!$username || $tanggal === null || $jam === null) {
                        continue;
                    }

                    $user = UserModel::where('username', $username)->first();
                    if (!$user) {
                        Log::warning("User tidak ditemukan: $username");
                        continue;
                    }

                    try {
                        // Konversi dari serial Excel ke DateTime
                        if (is_numeric($tanggal)) {
                            $tanggalObj = Date::excelToDateTimeObject($tanggal);
                            $tanggalStr = $tanggalObj->format('Y-m-d');
                        } else {
                            $tanggalStr = Carbon::parse($tanggal)->format('Y-m-d');
                        }

                        if (is_numeric($jam)) {
                            $jamStr = gmdate('H:i:s', $jam * 86400);
                        } else {
                            $jamStr = str_replace('.', ':', $jam);
                        }

                        $tanggalPelaksanaan = Carbon::parse("$tanggalStr $jamStr");

                        $insert[] = [
                            'user_id' => $user->user_id,
                            'tanggal_pelaksanaan' => $tanggalPelaksanaan,
                            'link_zoom' => $link_zoom,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                    } catch (\Exception $e) {
                        continue;
                    }
                }


                if (count($insert) > 0) {
                    JadwalModel::insertOrIgnore($insert);

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
        $user = UserModel::select('user_id', 'username')->get();
        return view('jadwal.create_ajax', ['username' => $user]);
    }

    public function store_ajax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|exists:user,username',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal!',
                'msgField' => $validator->errors()
            ]);
        }

        // Cari user_id berdasarkan username
        $user = UserModel::where('username', $request->username)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Username tidak ditemukan.'
            ]);
        }

        // Gabungkan tanggal dan jam menjadi satu datetime
        $tanggalPelaksanaan = $request->tanggal . ' ' . $request->jam . ':00';

        // Simpan ke database
        $jadwal = new JadwalModel();
        $jadwal->user_id = $user->user_id;
        $jadwal->tanggal_pelaksanaan = $tanggalPelaksanaan;
        $jadwal->save();

        return response()->json([
            'status' => true,
            'message' => 'Jadwal berhasil ditambahkan.'
        ]);
    }

    public function confirm_ajax(String $id){
        $jadwal = JadwalModel::find($id);
        return view('jadwal.confirm_ajax', ['jadwal' => $jadwal]);
    }

    public function delete_ajax(Request $request){
        $jadwal = JadwalModel::find(request()->id);
        $jadwal->delete();
        return response()->json([
            'status' => true,
            'message' => 'Jadwal berhasil dihapus.'
        ]);
    }

}
