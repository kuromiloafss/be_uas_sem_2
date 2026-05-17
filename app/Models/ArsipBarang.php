<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArsipBarang extends Model
{
    protected $table = 'arsip_barang';
    protected $primaryKey = 'arsip_id';
    public $timestamps = false;

    protected $fillable = [
        'barang_id',
        'tanggal_arsip',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
