<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\KategoriBarang;
use App\Models\Gedung;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => KategoriBarang::all()
        ]);
    }

    public function gedungs()
    {
        return response()->json([
            'success' => true,
            'data' => Gedung::all()
        ]);
    }

    // CRUD Kategori Barang
    public function storeKategori(Request $request)
    {
        $request->validate(['nama_kategori' => 'required|string|unique:kategori_barang,nama_kategori']);
        $kategori = KategoriBarang::create($request->only('nama_kategori'));
        return response()->json(['success' => true, 'data' => $kategori], 201);
    }

    public function updateKategori(Request $request, $id)
    {
        $request->validate(['nama_kategori' => 'required|string']);
        $kategori = KategoriBarang::find($id);
        if (!$kategori) return response()->json(['success' => false, 'message' => 'Not found'], 404);
        $kategori->update($request->only('nama_kategori'));
        return response()->json(['success' => true, 'data' => $kategori]);
    }

    public function destroyKategori($id)
    {
        $kategori = KategoriBarang::find($id);
        if (!$kategori) return response()->json(['success' => false, 'message' => 'Not found'], 404);
        
        $hasBarang = \App\Models\Barang::withTrashed()->where('kategori_barang_id', $id)->exists();
        if ($hasBarang) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh beberapa barang.'
            ], 400);
        }
        
        $kategori->delete();
        return response()->json(['success' => true, 'message' => 'Deleted']);
    }

    // CRUD Gedung
    public function storeGedung(Request $request)
    {
        $request->validate(['nama_gedung' => 'required|string|unique:gedung,nama_gedung']);
        $gedung = Gedung::create($request->only('nama_gedung'));
        return response()->json(['success' => true, 'data' => $gedung], 201);
    }

    public function updateGedung(Request $request, $id)
    {
        $request->validate(['nama_gedung' => 'required|string']);
        $gedung = Gedung::find($id);
        if (!$gedung) return response()->json(['success' => false, 'message' => 'Not found'], 404);
        $gedung->update($request->only('nama_gedung'));
        return response()->json(['success' => true, 'data' => $gedung]);
    }

    public function destroyGedung($id)
    {
        $gedung = Gedung::find($id);
        if (!$gedung) return response()->json(['success' => false, 'message' => 'Not found'], 404);
        
        $hasTemuan = \App\Models\BarangTemuan::withTrashed()->where('gedung_ditemukan_id', $id)->exists();
        $hasBukti = \App\Models\BuktiPengembalian::where('gedung_pengambilan_id', $id)->exists();
        $hasLaporan = \App\Models\LaporanKehilangan::withTrashed()->where('gedung_id', $id)->exists();
        
        if ($hasTemuan || $hasBukti || $hasLaporan) {
            return response()->json([
                'success' => false,
                'message' => 'Gedung tidak dapat dihapus karena masih digunakan dalam riwayat/laporan barang.'
            ], 400);
        }
        
        $gedung->delete();
        return response()->json(['success' => true, 'message' => 'Deleted']);
    }
}
