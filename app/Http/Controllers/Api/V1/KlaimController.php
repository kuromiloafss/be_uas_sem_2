<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\KlaimBarang;
use App\Models\BarangTemuan;
use Illuminate\Http\Request;

class KlaimController extends Controller
{
    public function index()
    {
        $claims = KlaimBarang::with(['temuan.barang.kategori', 'temuan.gedung', 'user'])
            ->orderBy('klaim_id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $claims
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'temuan_id' => 'required|exists:barang_temuan,temuan_id',
            'verifikasi_kepemilikan' => 'required|string',
            'tempat_kehilangan' => 'required|string',
            'bukti_foto' => 'required|image|max:2048',
        ]);

        // Cek apakah barang sudah diklaim sebelumnya oleh user yang sama atau sudah disetujui
        $existing = KlaimBarang::where('temuan_id', $request->temuan_id)
            ->where('status', 'disetujui')
            ->first();
        
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Barang ini sudah berhasil diklaim oleh orang lain.'
            ], 400);
        }

        $klaim = new KlaimBarang();
        $klaim->temuan_id = $request->temuan_id;
        $klaim->user_id = auth()->id();
        $klaim->tanggal_klaim = now();
        $klaim->status = 'menunggu';
        $klaim->verifikasi_kepemilikan = $request->verifikasi_kepemilikan;
        $klaim->tempat_kehilangan = $request->tempat_kehilangan;

        if ($request->hasFile('bukti_foto')) {
            $path = $request->file('bukti_foto')->store('public/bukti_klaim');
            $klaim->bukti_foto = basename($path);
        }

        $klaim->save();

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan klaim berhasil dikirim',
            'data' => $klaim
        ], 201);
    }

    public function myClaims()
    {
        $claims = KlaimBarang::with(['temuan.barang', 'temuan.gedung'])
            ->where('user_id', auth()->id())
            ->orderBy('klaim_id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $claims
        ]);
    }

    public function show($id)
    {
        $klaim = KlaimBarang::with(['temuan.barang.kategori', 'temuan.gedung', 'user'])
            ->find($id);

        if (!$klaim) {
            return response()->json([
                'success' => false,
                'message' => 'Data klaim tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $klaim
        ]);
    }
}
