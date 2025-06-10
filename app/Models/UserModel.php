<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\RoleModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Data_PendaftaranModel;
use App\Models\JadwalModel;
use App\Models\NilaiModel;
use App\Models\suratPernyataanModel;
use Illuminate\Foundation\Auth\User as Authenticable;

class UserModel extends Authenticable
{
    use HasFactory;

    protected $table = 'user';
    protected $primaryKey = 'user_id';

    protected $fillable = ['role_id', 'username', 'nama_lengkap', 'password', 'tanggal_lahir'];

    protected $hidden = ['password'];
    protected $casts = ['password' => 'hashed'];
    //relasi
    public function role(): belongsTo{
        return $this->belongsTo(RoleModel::class, 'role_id', 'role_id');
    }

    public function data_pendaftaran(): HasMany
    {
        return $this->hasMany(Data_PendaftaranModel::class, 'user_id', 'user_id');
    }
    public function jadwal(): HasMany
    {
        return $this->hasMany(JadwalModel::class, 'user_id', 'user_id');
    }
    public function nilai(): HasMany
    {
        return $this->hasMany(NilaiModel::class, 'user_id', 'user_id');
    }
    public function suratPernyataan(): HasMany
    {
        return $this->hasMany(suratPernyataanModel::class, 'user_id', 'user_id');
    }

}
