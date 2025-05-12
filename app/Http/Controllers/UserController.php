<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Yajra\DataTables\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\RoleModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = UserModel::with('role')->get();
        return view('user.user', compact('users'));
    }
    public function getUsers(Request $request)
    {
        $query = UserModel::with('role');

        if ($request->has('role_id') && $request->role_id != '') {
            $query->where('role_id', $request->role_id);
        }

        return DataTables::of($query)
            ->addColumn('role', function ($user) {
                return $user->role ? $user->role->role : '-';
            })
            ->make(true);
    }

    public function import()
     {
         return view('user.import');
     }

     public function import_ajax(Request $request)
     {
         if($request->ajax() || $request->wantsJson()){
             $rules = [
                 // validasi file harus xls atau xlsx, max 1MB
                 'data_user' => ['required', 'mimes:xls,xlsx', 'max:1024']
             ];

             $validator = Validator::make($request->all(), $rules);
             if($validator->fails()){
                 return response()->json([
                     'status' => false,
                     'message' => 'Validasi Gagal',
                     'msgField' => $validator->errors()
                 ]);
             }

             $file = $request->file('data_user');  // ambil file dari request

             $reader = IOFactory::createReader('Xlsx');  // load reader file excel
             $reader->setReadDataOnly(true);             // hanya membaca data
             $spreadsheet = $reader->load($file->getRealPath()); // load file excel
             $sheet = $spreadsheet->getActiveSheet();    // ambil sheet yang aktif

             $data = $sheet->toArray(null, false, true, true);   // ambil data excel

             $insert = [];
             if(count($data) > 1){ // jika data lebih dari 1 baris
                 foreach ($data as $baris => $value) {
                    if ($baris > 1) { // baris ke-1 adalah header
                        $role = $value['C'];

                        // Cari role_id berdasarkan role
                        $role = RoleModel::where('role', $role)->first();

                        if ($role) {
                            $tanggal_lahir_excel = $value['D'];

                            // Cek apakah berupa serial number dan ubah ke format tanggal
                            if (is_numeric($tanggal_lahir_excel)) {
                                $tanggal_lahir = Date::excelToDateTimeObject($tanggal_lahir_excel)->format('Y-m-d');
                            } else {
                                // Jika sudah dalam format string tanggal
                                $tanggal_lahir = date('Y-m-d', strtotime($tanggal_lahir_excel));
                            }

                            $insert[] = [
                                'nama_lengkap' => $value['A'],
                                'username' => $value['B'],
                                'role_id' => $role->role_id,
                                'tanggal_lahir' => $tanggal_lahir,
                                'password' => Hash::make($value['E']),
                                'created_at' => now(),
                            ];
                        } else {
                            // Nama role tidak ditemukan
                            // Bisa ditambahkan ke array error atau log
                            Log::warning("role '$role' tidak ditemukan pada baris ke-$baris.");
                        }
                    }
                 }

                 if(count($insert) > 0){
                     // insert data ke database, jika data sudah ada, maka diabaikan
                     UserModel::insertOrIgnore($insert);
                 }

                 return response()->json([
                     'status' => true,
                     'message' => 'Data berhasil diimport'
                 ]);
             }else{
                 return response()->json([
                     'status' => false,
                     'message' => 'Tidak ada data yang diimport'
                 ]);
             }
         }
         return redirect('/');
     }
}
