<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\UserModel;

class JadwalModel extends Model
{
    use HasFactory;

    protected $table = 'tanggal_pelaksanaan';
    protected $primaryKey = 'tanggal_pelaksanaan_id';

    protected $fillable = ['tanggal_pelaksanaan', 'user_id'];

    public function user(): belongsTo{
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
}
