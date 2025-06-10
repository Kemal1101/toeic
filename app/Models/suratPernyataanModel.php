<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class suratPernyataanModel extends Model
{
    use HasFactory;

    protected $table = 'surat_pernyataan';
    protected $primaryKey = 'surat_pernyataan_id';

    protected $fillable = [
        'user_id',
        'sertifikat1',
        'sertifikat2',
        'verifikasi_data',
        'notes_ditolak',
    ];

    /**
     * Relasi ke model User.
     * Asumsinya: foreign key user_id → users.user_id
     */
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
}
