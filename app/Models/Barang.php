<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'barang_id';
    public $timestamps = false;

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'deskripsi',
        'foto_barang',
        'kategori_barang_id',
        'status',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id');
    }

    public function temuan()
    {
        return $this->hasOne(BarangTemuan::class, 'barang_id');
    }

    public function laporan()
    {
        return $this->hasOne(LaporanKehilangan::class, 'barang_id');
    }
}
