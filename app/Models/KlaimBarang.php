<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KlaimBarang extends Model
{
    protected $table = 'klaim_barang';
    protected $primaryKey = 'klaim_id';
    public $timestamps = false;

    protected $fillable = [
        'temuan_id',
        'tanggal_klaim',
        'status',
        'user_id',
        'bukti_foto',
        'verifikasi_kepemilikan',
        'tempat_kehilangan',
    ];

    public function temuan()
    {
        return $this->belongsTo(BarangTemuan::class, 'temuan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
