<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\RoleModel;
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

}
