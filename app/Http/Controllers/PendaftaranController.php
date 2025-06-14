<?php

namespace App\Http\Controllers;

use App\Models\Data_PendaftaranModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Style\Font;

class PendaftaranController extends Controller
{
    public function index()
    {
        $user_id = Auth::user()->user_id;

        $sudahTerdaftar = Data_PendaftaranModel::where('user_id', $user_id)->exists();

        if ($sudahTerdaftar) {
            return redirect()->back()->with('status', 'anda_sudah_mendaftar');
        }

        $isPendaftaranOpen = DB::table('generalsettings')->where('gs_nama', 'isPendaftaranOpen')->value('gs_value');

        if ($isPendaftaranOpen == 'n') {
            return redirect()->back()->with('status', 'pendaftaran_tidak_dibuka');
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
                'pas_foto' => 'required|file|mimes:jpg,jpeg,png|max:5120', // 5 MB = 5120 KB
                'ktm_atau_ktp' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
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
                'message' => 'Data Pendaftar berhasil disimpan'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Request tidak valid'
        ], 400);
    }

    public function data_pendaftar()
    {
        $status = DB::table('generalSettings')
        ->where('gs_nama', 'isPendaftaranOpen')
        ->value('gs_value');

    return view('pendaftaran.dataPendaftar', compact('status'));
    }

    public function getPendaftar(Request $request)
    {
        $query = Data_PendaftaranModel::with('user');

        // Filter tahun
        if ($request->filled('tahun')) {
            $query->whereYear('data_pendaftaran.created_at', $request->tahun);
        }

        // Filter status verifikasi
        if ($request->filled('verifikasi_data')) {
            $query->where('data_pendaftaran.verifikasi_data', $request->verifikasi_data);
        }

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

    public function confirm_ajax(String $id){
        $dataPendaftar = Data_PendaftaranModel::find($id);
        return view('pendaftaran.confirm_ajax', ['dataPendaftar' => $dataPendaftar]);
    }

    public function delete_ajax(String $id){
        $dataPendaftar = Data_PendaftaranModel::find($id);
        if ($dataPendaftar) {
            $dataPendaftar->delete();
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

    public function edit_ajax(String $id){
        $dataPendaftar = Data_PendaftaranModel::find($id);
        $user = UserModel::where('user_id', $dataPendaftar->user_id)->first();
        return view('pendaftaran.edit_ajax', ['dataPendaftar' => $dataPendaftar, 'user' => $user]);
    }

    public function update_ajax(Request $request, String $id)
    {
        // Temukan data pendaftar berdasarkan ID
        $dataPendaftar = Data_PendaftaranModel::find($id);

        // Jika data pendaftar tidak ditemukan, kembalikan respons error 404
        if (!$dataPendaftar) {
            return response()->json([
                'status' => false,
                'message' => 'Data pendaftaran tidak ditemukan.'
            ], 404);
        }

        // Siapkan array data untuk update, kecualikan input file
        $updateData = $request->except(['pas_foto', 'ktm_atau_ktp']);

        // --- Logika untuk Pas Foto ---
        if ($request->hasFile('pas_foto')) {
            $foto = $request->file('pas_foto');
            $foto_nama = 'pasfoto_' . Auth::user()->username . '.' . $foto->getClientOriginalExtension();
            $path_foto = public_path('uploads/pasfoto/' . $foto_nama); // Path lengkap untuk file baru

            // Hapus file pas foto lama jika ada dan file baru memiliki nama yang sama
            // Penting: Periksa apakah $dataPendaftar->pas_foto ada di database
            if ($dataPendaftar->pas_foto && File::exists($path_foto)) {
                File::delete($path_foto);
            }

            // Pindahkan file pas foto yang baru
            $foto->move(public_path('uploads/pasfoto'), $foto_nama);
            $updateData['pas_foto'] = $foto_nama; // Simpan nama file baru ke array update
        }

        // --- Logika untuk KTM atau KTP ---
        if ($request->hasFile('ktm_atau_ktp')) {
            $ktp = $request->file('ktm_atau_ktp');
            $ktp_nama = 'ktmktp_' . Auth::user()->username . '.' . $ktp->getClientOriginalExtension();
            $path_ktp = public_path('uploads/ktmktp/' . $ktp_nama); // Path lengkap untuk file baru

            // Hapus file KTM/KTP lama jika ada dan file baru memiliki nama yang sama
            // Penting: Periksa apakah $dataPendaftar->ktm_atau_ktp ada di database
            if ($dataPendaftar->ktm_atau_ktp && File::exists($path_ktp)) {
                File::delete($path_ktp);
            }

            // Pindahkan file KTM/KTP yang baru
            $ktp->move(public_path('uploads/ktmktp'), $ktp_nama);
            $updateData['ktm_atau_ktp'] = $ktp_nama; // Simpan nama file baru ke array update
        }

        // Set status verifikasi data menjadi PENDING setiap kali ada update
        $updateData['verifikasi_data'] = 'PENDING';

        // Lakukan update pada record di database
        $dataPendaftar->update($updateData);

        // Kembalikan respons JSON sukses
        return response()->json([
            'status' => true,
            'message' => 'Data berhasil diupdate.'
        ]);
    }

    public function export_modal(){
        return view('pendaftaran.modal_export_pdf');
    }
    public function export_pdf(Request $request)
    {
        // Ambil data pendaftar dengan relasi ke user
        $query = Data_PendaftaranModel::with('user');

        // Filter berdasarkan tahun (dari field created_at)
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        // Filter berdasarkan status verifikasi
        if ($request->filled('verifikasi_data')) {
            $query->where('verifikasi_data', $request->verifikasi_data);
        }

        // Eksekusi query
        $data = $query->get();

        // Generate PDF menggunakan view
        $pdf = Pdf::loadView('pendaftaran.export_pdf', ['data' => $data]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);

        // Tampilkan PDF di browser
        return $pdf->stream('Data_Pendaftar_' . date('Ymd_His') . '.pdf');
    }

    public function view($type, $encrypted)
    {
        try {
            $filename = Crypt::decryptString($encrypted);

            switch ($type) {
                case 'ktmktp':
                    $path = public_path('uploads/ktmktp/' . $filename);
                    break;
                case 'pasfoto':
                    $path = public_path('uploads/pasfoto/' . $filename);
                    break;
                default:
                    abort(404, 'Tipe file tidak dikenali.');
            }

            if (file_exists($path)) {
                return response()->file($path);
            } else {
                abort(404, 'File tidak ditemukan.');
            }

        } catch (\Exception $e) {
            abort(400, 'Link tidak valid.');
        }
    }
    public function export_modal_excel(){
        return view('pendaftaran.modal_export_excel');
    }
    public function export_excel(Request $request)
    {
        $query = Data_PendaftaranModel::with('user');

        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        // Filter berdasarkan status verifikasi
        if ($request->filled('verifikasi_data')) {
            $query->where('verifikasi_data', $request->verifikasi_data);
        }

        // load library excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        // ambil sheet yang aktif

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'NIM');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'NIK');
        $sheet->setCellValue('D1', 'No WA');
        $sheet->setCellValue('E1', 'Alamat Asal');
        $sheet->setCellValue('F1', 'Alamat Sekarang');
        $sheet->setCellValue('G1', 'Jurusan');
        $sheet->setCellValue('H1', 'Prodi');
        $sheet->setCellValue('I1', 'Kampus');
        $sheet->setCellValue('J1', 'Pas Foto');
        $sheet->setCellValue('K1', 'KTM/KTP');


        $sheet->getStyle('A1:I1')->getFont()->setBold(true); // bold header
        $no = 1; // nomor data dimulai dari 1
        $baris = 2; // baris data dimulai dari baris ke 2
        foreach ($query->get() as $key => $value) {
            $encryptedKTP = Crypt::encryptString($value->ktm_atau_ktp);
            $linkktpktm = route('file.view', ['type' => 'ktmktp', 'encrypted' => $encryptedKTP]);

            $encryptedFoto = Crypt::encryptString($value->pas_foto);
            $linkpasfoto = route('file.view', ['type' => 'pasfoto', 'encrypted' => $encryptedFoto]);
            // $linkktp = asset('uploads/ktmktp/' . $value->ktm_atau_ktp);

            $sheet->setCellValue('A'.$baris, $value->user->username);
            $sheet->setCellValue('B'.$baris, $value->user->nama_lengkap);
            $sheet->setCellValue('C'.$baris, $value->nik);
            $sheet->setCellValue('D'.$baris, $value->no_wa);
            $sheet->setCellValue('E'.$baris, $value->alamat_asal);
            $sheet->setCellValue('F'.$baris, $value->alamat_sekarang);
            $sheet->setCellValue('G'.$baris, $value->jurusan);
            $sheet->setCellValue('H'.$baris, $value->program_studi);
            $sheet->setCellValue('I'.$baris, $value->kampus);

            $sheet->setCellValue('J'.$baris, 'Lihat File Pas Foto');
            $sheet->getStyle('J' . $baris)->getFont()->getColor()->setRGB('0000FF'); // Biru
            $sheet->getStyle('J' . $baris)->getFont()->setUnderline(Font::UNDERLINE_SINGLE); // Garis bawah
            $sheet->getCell('J'.$baris)->getHyperlink()->setUrl($linkpasfoto);

            $sheet->setCellValue('K' . $baris, 'Lihat File KTM/KTP');
            $sheet->getStyle('K' . $baris)->getFont()->getColor()->setRGB('0000FF'); // Biru
            $sheet->getStyle('K' . $baris)->getFont()->setUnderline(Font::UNDERLINE_SINGLE); // Garis bawah
            $sheet->getCell('K' . $baris)->getHyperlink()->setUrl($linkktpktm);

            $baris++;
            $no++;
        }
        foreach(range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $sheet->setTitle('Data Pendaftar Toeic'); // set title sheet

        $writer = IOFactory :: createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Pendaftar Toeic '.date('Y-m-d H:i:s').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument. spreadsheetml. sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header ('Cache-Control: max-age=0');
        header ('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s' ) . ' GMT' );
        header ('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

}
