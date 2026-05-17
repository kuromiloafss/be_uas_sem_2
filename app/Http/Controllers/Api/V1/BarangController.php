<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        // Ambil semua barang yang aktif (bukan diarsipkan)
        $barangs = Barang::with(['kategori', 'temuan.gedung'])
            ->where('status', '!=', 'diarsipkan')
            ->orderBy('barang_id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $barangs
        ]);
    }

    public function show($id)
    {
        $barang = Barang::with(['kategori', 'temuan.gedung'])->find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $barang
        ]);
    }

    public function showTemuan($id)
    {
        $barang = Barang::whereHas('temuan', function($q) use ($id) {
            $q->where('temuan_id', $id);
        })->with(['kategori', 'temuan.gedung'])->first();

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang temuan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $barang
        ]);
    }
}
