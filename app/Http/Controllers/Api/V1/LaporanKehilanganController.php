<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\LaporanKehilangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanKehilanganController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:100',
            'kategori_id' => 'required|exists:kategori_barang,kategori_barang_id',
            'gedung_id' => 'required|exists:gedung,gedung_id',
            'lokasi_detail' => 'nullable|string',
            'tanggal_hilang' => 'required|date',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|max:2048', // 2MB max
        ]);

        try {
            DB::beginTransaction();

            // 1. Simpan data Barang
            $barang = new Barang();
            $barang->nama_barang = $request->nama_barang;
            $barang->kategori_barang_id = $request->kategori_id;
            $barang->deskripsi = $request->deskripsi;
            $barang->status = 'belum_ditemukan';
            
            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('public/barang');
                $barang->foto_barang = basename($path);
            }

            $barang->save();

            // Generate kode_barang setelah save agar dapat ID
            $barang->kode_barang = 'HIL-' . str_pad($barang->barang_id, 4, '0', STR_PAD_LEFT);
            $barang->save();

            // 2. Simpan data Laporan Kehilangan
            $laporan = new LaporanKehilangan();
            $laporan->barang_id = $barang->barang_id;
            $laporan->gedung_id = $request->gedung_id;
            $laporan->lokasi_detail = $request->lokasi_detail;
            $laporan->tanggal_hilang = $request->tanggal_hilang;
            $laporan->tanggal_lapor = now();
            $laporan->status_laporan = 'menunggu';
            $laporan->user_id = auth()->id();
            $laporan->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Laporan kehilangan berhasil dikirim',
                'data' => $laporan
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan laporan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function myReports()
    {
        $reports = LaporanKehilangan::with(['barang.kategori', 'gedung'])
            ->where('user_id', auth()->id())
            ->orderBy('laporan_id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Mark a loss report as self-resolved by the owner
     * (e.g. "I found it myself" / "Ketemu Sendiri").
     * Only the report owner can call this.
     */
    public function markAsFound(Request $request, $id)
    {
        $laporan = LaporanKehilangan::with('barang')->find($id);

        if (!$laporan) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan'
            ], 404);
        }

        // Ownership check: only the reporter can cancel their own report
        if ($laporan->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Anda bukan pemilik laporan ini.'
            ], 403);
        }

        // Prevent cancelling an already-resolved report
        if ($laporan->status_laporan === 'dibatalkan' || $laporan->status_laporan === 'selesai') {
            return response()->json([
                'success' => false,
                'message' => 'Laporan ini sudah diselesaikan atau dibatalkan.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Update laporan status
            $laporan->status_laporan = 'dibatalkan';
            $laporan->save();

            // Update parent barang status
            if ($laporan->barang) {
                $laporan->barang->status = 'dibatalkan';
                $laporan->barang->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dibatalkan. Semoga barang Anda sudah ketemu!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan laporan: ' . $e->getMessage()
            ], 500);
        }
    }
}
