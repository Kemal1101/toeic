<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\suratPernyataanModel;
use App\Models\Data_PendaftaranModel;
use Yajra\DataTables\DataTables;
use App\Models\UserModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class SuratKeteranganController extends Controller
{
    public function index(){
        return view('suratPernyataan.index');
    }

    public function uploadSertifikat(){
        return view('suratPernyataan.uploadSertifikat');
    }

    public function store_ajax(Request $request)
    {
        // Cek apakah request berupa AJAX atau ingin JSON response
        if ($request->ajax() || $request->wantsJson()) {
            // Validasi
            $request->validate([
                'sertifikat1' => 'required|file|mimes:jpg,jpeg,png|max:5120', // 5 MB = 5120 KB
                'sertifikat2' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ]);


            // Simpan file dengan nama baru
            if ($request->hasFile('sertifikat1')) {
                $sertifikat1 = $request->file('sertifikat1');
                $sertifikat1_nama = 'sertifikat1_' .  Auth::user()->username . '.' . $sertifikat1->getClientOriginalExtension();
                $sertifikat1->move(public_path('uploads/sertifikat_surat_keterangan'), $sertifikat1_nama);
            }

            if ($request->hasFile('sertifikat2')) {
                $sertifikat2 = $request->file('sertifikat2');
                $sertifikat2_nama = 'sertifikat2_' .  Auth::user()->username . '.' . $sertifikat2->getClientOriginalExtension();
                $sertifikat2->move(public_path('uploads/sertifikat_surat_keterangan'), $sertifikat2_nama);
            }

            suratPernyataanModel::create([
                'user_id' => Auth::user()->user_id,
                'sertifikat1' => $sertifikat1_nama,
                'sertifikat2' => $sertifikat2_nama,
                'created_at' => now(),
                'updated_at' => now(),

            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil disimpan. Silahkan Menunggu'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Request tidak valid'
        ], 400);
    }

    public function dataRequestSuratPernyataan(){
        return view('suratPernyataan.dataRequestSK');
    }

    public function getDataSuratPernyataan(Request $request)
    {
        $query = suratPernyataanModel::with('user'); // eager loading user (username dan nama_lengkap)

        // Filter tahun (dari created_at)
        if ($request->filled('tahun')) {
            $query->whereYear('surat_pernyataan.created_at', $request->tahun);
        }

        // Filter status verifikasi
        if ($request->filled('verifikasi_data')) {
            $query->where('surat_pernyataan.verifikasi_data', $request->verifikasi_data);
        }
        // jika tidak di-filter, biarkan tampil semua data

        return DataTables::of($query)
            ->addColumn('username', function ($pendaftar) {
                return $pendaftar->user->username ?? '-';
            })
            ->addColumn('nama_lengkap', function ($pendaftar) {
                return $pendaftar->user->nama_lengkap ?? '-';
            })
           ->addColumn('sertifikat1', function ($pendaftar) {
                $url = asset('uploads/sertifikat_surat_keterangan/' . $pendaftar->sertifikat1);
                return "<img src='{$url}' alt='Pas Foto' width='80'>";
            })
            ->addColumn('sertifikat2', function ($pendaftar) {
                $url = asset('uploads/sertifikat_surat_keterangan/' . $pendaftar->sertifikat2);
                return "<img src='{$url}' alt='KTP/KTM' width='80'>";
            })
            ->rawColumns(['sertifikat1', 'sertifikat2'])
            // ->addColumn('aksi', function ($pendaftar) {
            //     // optional: tambahkan tombol aksi jika dibutuhkan
            //     return '<button class="btn btn-info btn-sm">Detail</button>';
            // })
            // ->rawColumns(['aksi']) // jika pakai HTML di kolom
            ->make(true);
    }

    public function verifikasi(String $id)
    {
        $dataPendaftar = suratPernyataanModel::find($id);

        if (!$dataPendaftar) {
            abort(404, 'Data pendaftar tidak ditemukan.');
        }

        $user = UserModel::select('user_id', 'username', 'nama_lengkap')
                    ->where('user_id', $dataPendaftar->user_id)
                    ->first();

        return view('suratPernyataan.dataRequestSKModal', [
            'dataPendaftar' => $dataPendaftar,
            'user' => $user
        ]);
    }

    public function notes(String $id)
    {
        $dataPendaftar = suratPernyataanModel::find($id);

        if (!$dataPendaftar) {
            return response()->json([
                'message' => 'Data pendaftar tidak ditemukan.'
            ], 404);
        }

        $user = UserModel::select('user_id', 'username', 'nama_lengkap')
                    ->where('user_id', $dataPendaftar->user_id)
                    ->first();

        return response()->view('suratPernyataan.notesTolakModal', [
            'dataPendaftar' => $dataPendaftar,
            'user' => $user
        ]);
    }


    public function verifikasiSetuju($id)
    {
        $data = suratPernyataanModel::find($id);

        if (!$data) {
            return redirect()->back()->with('error', 'Data pendaftar tidak ditemukan.');
        }

        $data->verifikasi_data = 'TERVERIFIKASI';
        $data->save();

        return redirect()->back()->with('success', 'Data berhasil diverifikasi.');
    }

    public function verifikasiTolak(Request $request, $id)
    {
        $data = suratPernyataanModel::find($id);

        if (!$data) {
            return redirect()->back()->with('error', 'Data pendaftar tidak ditemukan.');
        }

        $data->verifikasi_data = 'DITOLAK';
        $data->notes_ditolak = $request->notes_ditolak;
        $data->save();

        return redirect()->back()->with('error', 'Data berhasil ditolak.');
    }

    public function edit_ajax(String $id){
        $dataPendaftar = suratPernyataanModel::find($id);
        $user = UserModel::where('user_id', $dataPendaftar->user_id)->first();
        return view('suratPernyataan.edit_ajax', ['dataPendaftar' => $dataPendaftar, 'user' => $user]);
    }

    public function update_ajax(Request $request, String $id)
    {
        $dataPendaftar = SuratPernyataanModel::find($id);

        // Jika data pendaftar tidak ditemukan, kembalikan respons error
        if (!$dataPendaftar) {
            return response()->json([
                'status' => false,
                'message' => 'Data pendaftaran tidak ditemukan.'
            ], 404); // Menggunakan status HTTP 404 untuk Not Found
        }

        // Siapkan array data untuk update, kecuali file
        $updateData = $request->except(['sertifikat1', 'sertifikat2']);

        // === Logika untuk Sertifikat 1 ===
        if ($request->hasFile('sertifikat1')) {
            $sertifikat1 = $request->file('sertifikat1');
            $sertifikat1_nama = 'sertifikat1_' . Auth::user()->username . '.' . $sertifikat1->getClientOriginalExtension();
            $path = public_path('uploads/sertifikat_surat_keterangan/' . $sertifikat1_nama);

            // Hapus file lama jika ada dan file baru memiliki nama yang sama
            if ($dataPendaftar->sertifikat1 && File::exists($path)) {
                File::delete($path);
            }

            // Pindahkan file baru
            $sertifikat1->move(public_path('uploads/sertifikat_surat_keterangan'), $sertifikat1_nama);
            $updateData['sertifikat1'] = $sertifikat1_nama; // Simpan nama file baru
        }

        // === Logika untuk Sertifikat 2 ===
        if ($request->hasFile('sertifikat2')) {
            $sertifikat2 = $request->file('sertifikat2');
            $sertifikat2_nama = 'sertifikat2_' . Auth::user()->username . '.' . $sertifikat2->getClientOriginalExtension();
            $path = public_path('uploads/sertifikat_surat_keterangan/' . $sertifikat2_nama);

            // Hapus file lama jika ada dan file baru memiliki nama yang sama
            if ($dataPendaftar->sertifikat2 && File::exists($path)) {
                File::delete($path);
            }

            // Pindahkan file baru
            $sertifikat2->move(public_path('uploads/sertifikat_surat_keterangan'), $sertifikat2_nama);
            $updateData['sertifikat2'] = $sertifikat2_nama; // Simpan nama file baru
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

    public function generateSuratPernyataanToeic()
    {
        $pdf = new \setasign\Fpdi\Fpdi();

        // Gunakan path langsung
        $filePath = public_path('Contoh_Surat-Keterangan-Ujian-TOEIC-2x_UPA-Bahasa.docx.pdf');

        $pageCount = $pdf->setSourceFile($filePath);
        $templateId = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($templateId);

        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useTemplate($templateId);

        $pdf->SetFont('Times', '', 12);
        $pdf->SetTextColor(0, 0, 0); // Merah

        $nim = Auth::user()->username;
        $nama_lengkap = Auth::user()->nama_lengkap;
        $user_id = Auth::user()->user_id;
        $program_studi = Data_PendaftaranModel::where('user_id', $user_id)->value('program_studi');
        $jurusan = Data_PendaftaranModel::where('user_id', $user_id)->value('jurusan');
        $tempat_lahir = Auth::user()->tempat_lahir;

        \Carbon\Carbon::setLocale('id');
        $tanggal_lahir = \Carbon\Carbon::parse(Auth::user()->tanggal_lahir)->translatedFormat('d-F-Y');

        $alamat = Data_PendaftaranModel::where('user_id', $user_id)->value('alamat_sekarang');

        $pdf->SetXY(95, 129.53);
        $pdf->Write(0, $nama_lengkap);

        $pdf->SetXY(95, 134.3659);
        $pdf->Write(0, $nim);

        $pdf->SetXY(95, 139.33);
        $pdf->Write(0, $program_studi . ' / ' . $jurusan);

        $pdf->SetXY(95, 144.2);
        $pdf->Write(0, $tempat_lahir . ', ' . $tanggal_lahir);

        $pdf->SetXY(95, 148.99);
        $pdf->Write(0, $alamat);

        $pdf->Image(public_path('ttd/ttd_bu_atiqah.png'), 120, 190, 45  );


        $pdf->Output('I', 'Surat Pernyataan TOEIC.pdf');
    }

    public function confirm_ajax(String $id){
        $sk = suratPernyataanModel::find($id);
        return view('suratPernyataan.confirm_ajax', ['sk' => $sk]);
    }

    public function delete_ajax(Request $request){
        $sk = suratPernyataanModel::find(request()->id);
        $sk->delete();
        return response()->json([
            'status' => true,
            'message' => 'Data nilai berhasil dihapus.'
        ]);
    }
}
