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
        // total_lost counts LaporanKehilangan records whose associated barang status is 'belum_ditemukan'
        $totalLost = LaporanKehilangan::whereHas('barang', function ($query) {
            $query->where('status', 'belum_ditemukan');
        })->count();

        // total_found counts BarangTemuan records whose associated barang status is not archived ('arsip' or 'diarsipkan')
        $totalFound = BarangTemuan::whereHas('barang', function ($query) {
            $query->whereNotIn('status', ['arsip', 'diarsipkan']);
        })->count();

        $pendingClaims = KlaimBarang::where('status', 'menunggu')->count();
        
        $recentFound = BarangTemuan::whereHas('barang', function ($query) {
            $query->whereNotIn('status', ['arsip', 'diarsipkan']);
        })->with('barang')->orderBy('temuan_id', 'desc')->limit(5)->get();

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
        // Show all barang with a laporan EXCEPT those that are fully resolved:
        // - 'dikembalikan' = barang sudah diserahkan ke pemilik, tidak perlu tampil lagi
        // - 'dibatalkan'   = laporan dibatalkan oleh user sendiri
        // - 'diarsipkan' IS intentionally kept visible so staff can see archived records
        $barangs = Barang::has('laporan')
            ->whereNotIn('status', ['dikembalikan', 'dibatalkan'])
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

        try {
            DB::beginTransaction();

            $klaim = KlaimBarang::with('temuan')->find($id);
            if (!$klaim) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Klaim tidak ditemukan'], 404);
            }

            // Guard: Prevent double-processing a claim that has already been actioned
            if ($klaim->status !== 'menunggu') {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Klaim ini sudah diproses sebelumnya dan tidak dapat diubah lagi.'
                ], 400);
            }

            $klaim->status = $request->status;
            $klaim->save();

            if ($request->status === 'disetujui' && $klaim->temuan) {
                // On approval: mark barang and temuan as 'diklaim'.
                // 'dikembalikan' will only be set later when staff manually confirms handover.
                $barang = Barang::find($klaim->temuan->barang_id);
                if ($barang) {
                    $barang->status = 'diklaim';
                    $barang->save();

                    // Sync laporan kehilangan status if exists
                    if ($barang->laporan) {
                        $barang->laporan->status_laporan = 'diklaim';
                        $barang->laporan->save();
                    }
                }

                $klaim->temuan->status = 'diklaim';
                $klaim->temuan->save();

            } elseif ($request->status === 'ditolak' && $klaim->temuan) {
                // On rejection: revert barang and temuan status so the item is claimable again
                $barang = Barang::find($klaim->temuan->barang_id);
                if ($barang) {
                    $barang->status = 'diunggah';
                    $barang->save();

                    // Sync laporan kehilangan status if exists
                    if ($barang->laporan) {
                        $barang->laporan->status_laporan = 'ditemukan';
                        $barang->laporan->save();
                    }
                }

                $klaim->temuan->status = 'diunggah';
                $klaim->temuan->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status klaim diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses klaim: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateBarangStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $barang = Barang::with(['temuan', 'laporan'])->find($id);
        if (!$barang) return response()->json(['success' => false, 'message' => 'Barang tidak ditemukan'], 404);

        // Normalize alias statuses
        $status = $request->status;
        if ($status === 'arsip' || $status === 'diarsipkan') {
            $status = 'diarsipkan';
        }

        try {
            DB::beginTransaction();

            $barang->status = $status;
            $barang->save();

            // Sync child BarangTemuan status to mirror parent barang status
            if ($barang->temuan) {
                if ($status === 'belum_ditemukan') {
                    // Cascade-delete temuan and all associated claims/bukti when reverting to lost
                    $temuan = $barang->temuan;
                    $claims = KlaimBarang::where('temuan_id', $temuan->temuan_id)->get();
                    foreach ($claims as $claim) {
                        \App\Models\BuktiPengembalian::where('klaim_id', $claim->klaim_id)->delete();
                        $claim->delete();
                    }
                    $temuan->delete();
                } else {
                    // For all other statuses (diarsipkan, dikembalikan, etc.): update status, preserve history
                    $barang->temuan->status = $status;
                    $barang->temuan->save();
                }
            }

            // Sync child LaporanKehilangan status_laporan to mirror parent barang status
            if ($barang->laporan) {
                $laporanStatus = match($status) {
                    'belum_ditemukan' => 'menunggu',
                    'diunggah'        => 'ditemukan',
                    'diklaim'         => 'diklaim',
                    'dikembalikan'    => 'selesai',
                    'diarsipkan'      => 'diarsipkan',
                    default           => $barang->laporan->status_laporan,
                };
                $barang->laporan->status_laporan = $laporanStatus;
                $barang->laporan->save();
            }

            // When staff manually marks as 'dikembalikan', create BuktiPengembalian
            // if there is an approved claim that does not yet have a proof record
            if ($status === 'dikembalikan' && $barang->temuan) {
                $approvedKlaim = KlaimBarang::where('temuan_id', $barang->temuan->temuan_id)
                    ->where('status', 'disetujui')
                    ->first();
                if ($approvedKlaim) {
                    $alreadyExists = \App\Models\BuktiPengembalian::where('klaim_id', $approvedKlaim->klaim_id)->exists();
                    if (!$alreadyExists) {
                        $bukti = new \App\Models\BuktiPengembalian();
                        $bukti->klaim_id = $approvedKlaim->klaim_id;
                        $bukti->kode_pengambilan = 'PNM-' . str_pad($approvedKlaim->klaim_id, 4, '0', STR_PAD_LEFT);
                        $bukti->gedung_pengambilan_id = $barang->temuan->gedung_ditemukan_id;
                        $bukti->save();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status barang berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage()
            ], 500);
        }
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
        // withTrashed() agar bisa menemukan barang yang sudah soft-deleted sebelumnya
        $barang = Barang::withTrashed()->find($id);
        if (!$barang) return response()->json(['success' => false, 'message' => 'Barang tidak ditemukan'], 404);

        try {
            DB::beginTransaction();

            // 1. Clean up BarangTemuan and its descendants (including soft-deleted ones)
            $temuan = BarangTemuan::withTrashed()->where('barang_id', $id)->first();
            if ($temuan) {
                // Hard delete all BuktiPengembalian and KlaimBarang linked to this temuan
                $claims = KlaimBarang::where('temuan_id', $temuan->temuan_id)->get();
                foreach ($claims as $claim) {
                    \App\Models\BuktiPengembalian::where('klaim_id', $claim->klaim_id)->delete(); // no SoftDeletes, already hard delete
                    $claim->delete(); // no SoftDeletes, already hard delete
                }
                $temuan->forceDelete(); // permanent delete
            }

            // 2. Hard delete LaporanKehilangan records (including soft-deleted)
            LaporanKehilangan::withTrashed()->where('barang_id', $id)->forceDelete();

            // 3. Hard delete the Barang record
            $barang->forceDelete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
