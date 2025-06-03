<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\UserModel;

class NilaiModel extends Model
{
    use HasFactory;

    protected $table = 'nilai';
    protected $primaryKey = 'nilai_id';

    protected $fillable = [
        'nilai_id',	
        'user_id',
        'listening',
        'reading',
        'total',
    ];

    public function user(): belongsTo{
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
}
