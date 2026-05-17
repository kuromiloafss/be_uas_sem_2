<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanKehilangan extends Model
{
    protected $table = 'laporan_kehilangan';
    protected $primaryKey = 'laporan_id';
    public $timestamps = false;

    protected $fillable = [
        'barang_id',
        'gedung_id',
        'lokasi_detail',
        'tanggal_hilang',
        'tanggal_lapor',
        'status_laporan',
        'user_id',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
