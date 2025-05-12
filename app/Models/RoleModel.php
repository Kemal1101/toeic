<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\UserModel;

class RoleModel extends Model
{
    use HasFactory;

    protected $table = 'role';
    protected $primaryKey = 'role_id';
    protected $fillable = [
        'role'
    ];

    public function user(): HasMany
    {
        return $this->hasMany(UserModel::class, 'role_id', 'role_id');
    }
}
