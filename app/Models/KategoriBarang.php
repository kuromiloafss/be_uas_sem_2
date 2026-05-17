<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriBarang extends Model
{
    protected $table = 'kategori_barang';
    protected $primaryKey = 'kategori_barang_id';
    public $timestamps = false;

    protected $fillable = [
        'nama_kategori',
    ];
}
