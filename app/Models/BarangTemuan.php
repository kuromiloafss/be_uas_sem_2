<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangTemuan extends Model
{
    use SoftDeletes;

    protected $table = 'barang_temuan';
    protected $primaryKey = 'temuan_id';
    public $timestamps = false;

    protected $fillable = [
        'barang_id',
        'gedung_ditemukan_id',
        'lokasi_ditemukan',
        'tanggal_ditemukan',
        'tanggal_diunggah',
        'ditemukan_oleh',
        'status',
    ];

    // Alias agar frontend bisa akses temuan->lokasi_detail
    public function getLokasi_DetailAttribute()
    {
        return $this->lokasi_ditemukan;
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'gedung_ditemukan_id');
    }
}
