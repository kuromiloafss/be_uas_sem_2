<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';
    protected $primaryKey = 'staff_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'email',
        'jabatan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
