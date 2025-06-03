<?php

use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\NilaiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::pattern('id','[0-9e]+');

Route::get('login', [AuthController::class, 'login' ])->name('login');
Route::post('login', [AuthController::class, 'postlogin' ]);
Route::get('logout', [AuthController ::class,'logout' ])->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/mahasiswa', [WelcomeController::class, 'indexMahasiswa'])->name('dashboard.mahasiswa');
    Route::get('/dashboard/upa', [WelcomeController::class, 'indexUpa'])->name('dashboard.upa');

    Route::group(['prefix' => 'user'], function () {
        Route::get('/', [UserController::class, 'index'])->name('user');
        Route::get('/getUsers', [UserController::class, 'getUsers'])->name('user.getUsers');
        Route::get('/import', [UserController::class, 'import'])->name('user.import');
        Route::post('/import_ajax', [UserController::class, 'import_ajax'])->name('user.import_ajax');
         //route edit ajax
        Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax'])->name('user.edit_ajax');
        Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax'])->name('user.update_ajax');
        //route hapus ajax
        Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax'])->name('user.confirm_ajax');
        Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax'])->name('user.delete_ajax');

        Route::get('/create_ajax', [UserController::class, 'create_ajax'])->name('user.create_ajax');
        Route::post('/store_ajax', [UserController::class, 'store_ajax'])->name('user.store_ajax');
    });

    Route::group(['prefix' => 'pendaftaran'], function () {
        Route::get('/', [PendaftaranController::class, 'index'])->name('pendaftaran');
        Route::post('/store_ajax', [PendaftaranController::class, 'store_ajax'])->name('pendaftaran.store_ajax');
        Route::get('/data_pendaftar', [PendaftaranController::class, 'data_pendaftar'])->name('pendaftaran.data_pendaftar');
        Route::get('/getPendaftar', [PendaftaranController::class, 'getPendaftar'])->name('pendaftaran.getPendaftar');
        Route::get('/verifikasi/{id}', [PendaftaranController::class, 'verifikasi'])->name('pendaftaran.verifikasi');
        Route::put('/confrimverifikasi/{id}', [PendaftaranController::class, 'verifikasiSetuju'])->name('pendaftaran.verifikasi.setuju');
        Route::put('/confrimtolak/{id}', [PendaftaranController::class, 'verifikasiTolak'])->name('pendaftaran.verifikasi.tolak');
        Route::get('/notes/{id}', [PendaftaranController::class, 'notes'])->name('pendaftaran.notes');

        Route::get('/{id}/edit_ajax', [PendaftaranController::class, 'edit_ajax'])->name('pendaftaran.edit_ajax');
        Route::put('/{id}/update_ajax', [PendaftaranController::class, 'update_ajax'])->name('pendaftaran.update_ajax');

        Route::get('/{id}/delete_ajax', [PendaftaranController::class, 'confirm_ajax'])->name('pendaftaran.confirm_ajax');
        Route::delete('/{id}/delete_ajax', [PendaftaranController::class, 'delete_ajax'])->name('pendaftaran.delete_ajax');

        Route::get('/modal_export_pdf', [PendaftaranController::class, 'export_modal'])->name('data_pendaftar.modal_export_pdf');
        Route::get('/export_pdf', [PendaftaranController::class, 'export_pdf'])->name('data_pendaftar.export_pdf');

    });

     Route::group(['prefix' => 'jadwal'], function () {
        Route::get('/', [JadwalController::class, 'index'])->name('jadwal');
        Route::get('/getJadwal', [JadwalController::class, 'getJadwal'])->name('jadwal.getJadwal');
        Route::get('/getJadwalPelaksanaan', [JadwalController::class, 'getJadwalPelaksanaan'])->name('jadwal.getJadwalPelaksanaan');

        Route::get('/import', [JadwalController::class, 'import'])->name('jadwal.import');
        Route::post('/import_ajax', [JadwalController::class, 'import_ajax'])->name('jadwal.import_ajax');

        Route::get('/create_ajax', [JadwalController::class, 'create_ajax'])->name('jadwal.create_ajax');
        Route::post('/store_ajax', [JadwalController::class, 'store_ajax'])->name('jadwal.store_ajax');

        Route::get('/{id}/delete_ajax', [JadwalController::class, 'confirm_ajax'])->name('jadwal.confirm_ajax');
        Route::delete('/{id}/delete_ajax', [JadwalController::class, 'delete_ajax'])->name('jadwal.delete_ajax');
    });

    Route::group(['prefix' => 'nilai'], function () {
        Route::get('/', [NilaiController::class, 'index'])->name('nilai');
        Route::get('/getNilai', [NilaiController::class, 'getNilai'])->name('nilai.getNilai');

        Route::get('/getNilaiPeserta', [NilaiController::class, 'nilai'])->name('nilai.getNilaiPeserta');

        Route::get('/import', [NilaiController::class, 'import'])->name('nilai.import');
        Route::post('/import_ajax', [NilaiController::class, 'import_ajax'])->name('nilai.import_ajax');
    });
});

