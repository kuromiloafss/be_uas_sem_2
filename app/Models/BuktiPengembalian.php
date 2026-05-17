<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuktiPengembalian extends Model
{
    protected $table = 'bukti_pengembalian';
    protected $primaryKey = 'bukti_id';
    public $timestamps = false;

    protected $fillable = [
        'klaim_id',
        'kode_pengambilan',
        'gedung_pengambilan_id',
    ];

    public function klaim()
    {
        return $this->belongsTo(KlaimBarang::class, 'klaim_id');
    }

    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'gedung_pengambilan_id');
    }
}
