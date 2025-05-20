<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\UserModel;

class Data_PendaftaranModel extends Model
{
    use HasFactory;
    protected $table = 'data_pendaftaran';
    protected $primaryKey = 'data_pendaftaran_id';

    protected $fillable = ['data_pendaftaran_id','user_id','nim','nik','no_wa','alamat_asal','alamat_sekarang',
        'program_studi','jurusan','kampus','pas_foto','ktm_atau_ktp', 'verifikasi_data'];	

    //relasi
    public function user(): belongsTo{
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
}
