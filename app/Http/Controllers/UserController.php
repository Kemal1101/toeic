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
                            $tanggal_lahir_excel = $value['E'];

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
                                'tempat_lahir' => $value['D'],
                                'tanggal_lahir' => $tanggal_lahir,
                                'password' => Hash::make($value['F']),
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

     public function edit_ajax(String $id){
        $user = UserModel::find($id);
        $role = RoleModel::select('role_id', 'role')->get();

        return view('user.edit_ajax', ['user' => $user, 'role' => $role]);
    }

    public function update_ajax(Request $request, String $id){
        $user = UserModel::find($id);

        // Validasi manual bisa ditambahkan di sini jika perlu

        $user->update([
            'nama_lengkap' => $request->input('nama_lengkap'),
            'username' => $request->input('username'),
            'role_id' => $request->input('role_id'),
            'tempat_lahir' => $request->input('tempat_lahir'),
            'tanggal_lahir' => $request->input('tanggal_lahir'),
            'password' => $request->input('password') ? Hash::make($request->input('password')) : $user->password,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil diperbarui'
        ]);
    }

    public function confirm_ajax(String $id){
        $user = UserModel::find($id);
        return view('user.confirm_ajax', ['user' => $user]);
    }

    public function delete_ajax(String $id){
        $user = UserModel::find($id);
        if (\App\Models\Data_PendaftaranModel::where('user_id', $user->user_id)->exists()) {
            return response()->json([
                'status'  => false,
                'message' => 'Peserta Sudah Mendaftar, Tidak Dapat Dihapus'
            ]);
        }
        $user->delete();
        if ($user) {
            return response()->json([
                'status'  => true,
                'message' => 'Data berhasil dihapus'
            ]);
        }else{
            return response()->json([
                'status'  => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        return redirect('/user');
    }

    public function create_ajax(){
        $role = RoleModel::select('role_id', 'role')->get();
        return view('user.create_ajax', ['role' => $role]);
    }

    public function store_ajax(Request $request){
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:user,username',
            'role_id' => 'required|integer',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'password' => 'required|string|min:6'
        ]);

        $user = new UserModel();
        $user->nama_lengkap = $request->input('nama_lengkap');
        $user->username = $request->input('username');
        $user->role_id = $request->input('role_id');
        $user->tempat_lahir = $request->input('tempat_lahir');
        $user->tanggal_lahir = $request->input('tanggal_lahir');
        $user->password = Hash::make($request->input('password'));
        $user->created_at = now();
        $user->updated_at = now();
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil disimpan'
        ]);
    }

    public function edit_password_ajax(String $id){
        $user = UserModel::find($id);
        return view('user.edit_password_ajax', ['user' => $user]);
    }

}
