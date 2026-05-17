<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $table = 'dosen';
    protected $primaryKey = 'dosen_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'nip',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
