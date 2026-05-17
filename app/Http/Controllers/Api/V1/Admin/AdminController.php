<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangTemuan;
use App\Models\KlaimBarang;
use App\Models\LaporanKehilangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboardStats()
    {
        $totalFound = BarangTemuan::count();
        $totalLost = LaporanKehilangan::count();
        $pendingClaims = KlaimBarang::where('status', 'menunggu')->count();
        $recentFound = BarangTemuan::with('barang')->orderBy('temuan_id', 'desc')->limit(5)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_found' => $totalFound,
                'total_lost' => $totalLost,
                'pending_claims' => $pendingClaims,
                'recent_found' => $recentFound
            ]
        ]);
    }

    public function storeBarangTemuan(Request $request)
    {
        $request->validate([
            'nama_barang'       => 'required|string',
            'kategori_id'       => 'required|exists:kategori_barang,kategori_barang_id',
            'gedung_id'         => 'required|exists:gedung,gedung_id',
            'tanggal_ditemukan' => 'required|date',
            'lokasi_ditemukan'  => 'nullable|string|max:255',
            'ditemukan_oleh'    => 'nullable|string|max:100',
            'deskripsi'         => 'nullable|string',
            'foto'              => 'nullable|image|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $barang = new Barang();
            $barang->nama_barang       = $request->nama_barang;
            $barang->kategori_barang_id = $request->kategori_id;
            $barang->deskripsi         = $request->deskripsi;
            $barang->status            = 'diunggah';

            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('public/barang');
                $barang->foto_barang = basename($path);
            }
            $barang->save();

            // Generate kode_barang setelah save agar dapat ID
            $barang->kode_barang = 'TEM-' . str_pad($barang->barang_id, 4, '0', STR_PAD_LEFT);
            $barang->save();

            $temuan = new BarangTemuan();
            $temuan->barang_id           = $barang->barang_id;
            $temuan->gedung_ditemukan_id = $request->gedung_id;
            $temuan->lokasi_ditemukan    = $request->lokasi_ditemukan;
            $temuan->tanggal_ditemukan   = $request->tanggal_ditemukan;
            $temuan->tanggal_diunggah    = now();
            $temuan->ditemukan_oleh      = $request->ditemukan_oleh;
            $temuan->status              = 'diunggah';
            $temuan->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Barang temuan berhasil diupload',
                'data'    => $temuan
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function indexBarangTemuan()
    {
        $barangs = Barang::has('temuan')
            ->with(['kategori', 'temuan.gedung'])
            ->orderBy('barang_id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $barangs
        ]);
    }

    public function indexBarangHilang()
    {
        $barangs = Barang::has('laporan')
            ->with(['kategori', 'laporan.gedung'])
            ->orderBy('barang_id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $barangs
        ]);
    }

    public function verifyClaim(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
        ]);

        $klaim = KlaimBarang::with('temuan')->find($id);
        if (!$klaim) return response()->json(['success' => false, 'message' => 'Klaim tidak ditemukan'], 404);

        $klaim->status = $request->status;
        $klaim->save();

        // Jika disetujui, update status barang
        if ($request->status === 'disetujui' && $klaim->temuan) {
            $barang = Barang::find($klaim->temuan->barang_id);
            if ($barang) {
                $barang->status = 'diklaim';
                $barang->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status klaim diperbarui'
        ]);
    }

    public function updateBarangStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $barang = Barang::find($id);
        if (!$barang) return response()->json(['success' => false, 'message' => 'Barang tidak ditemukan'], 404);

        $barang->status = $request->status;
        $barang->save();

        return response()->json([
            'success' => true,
            'message' => 'Status barang berhasil diperbarui'
        ]);
    }

    public function convertLostToFound(Request $request, $id)
    {
        $request->validate([
            'lokasi_ditemukan'  => 'required|string|max:255',
            'tanggal_ditemukan' => 'required|date',
            'ditemukan_oleh'    => 'nullable|string|max:100',
        ]);

        $barangHilang = Barang::with('laporan')->find($id);
        if (!$barangHilang) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);

        // Cegah duplikasi: jika sudah ada temuan untuk barang ini
        if (BarangTemuan::where('barang_id', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Barang ini sudah ada di daftar temuan.'], 400);
        }

        try {
            DB::beginTransaction();

            $barangHilang->status = 'ditemukan';
            $barangHilang->save();

            $temuan = new BarangTemuan();
            $temuan->barang_id           = $barangHilang->barang_id;
            $temuan->lokasi_ditemukan    = $request->lokasi_ditemukan;
            $temuan->tanggal_ditemukan   = $request->tanggal_ditemukan;
            $temuan->tanggal_diunggah    = now();
            $temuan->ditemukan_oleh      = $request->ditemukan_oleh;
            $temuan->status              = 'diunggah';
            $temuan->save();

            // Generate kode_barang jika belum ada
            if (!$barangHilang->kode_barang) {
                $barangHilang->kode_barang = 'TEM-' . str_pad($barangHilang->barang_id, 4, '0', STR_PAD_LEFT);
                $barangHilang->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil tarik data! Laporan kini menjadi Barang Temuan.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function destroyBarang($id)
    {
        $barang = Barang::find($id);
        if (!$barang) return response()->json(['success' => false, 'message' => 'Barang tidak ditemukan'], 404);

        try {
            $barang->delete();
            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
