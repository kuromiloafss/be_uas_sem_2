<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        // Fetch public items under distinct categories:
        // 1. "Barang Hilang" (Lost): must have 'laporan' relation and 'belum_ditemukan' status.
        // 2. "Barang Temuan" (Found): must have 'temuan' relation and status in ['diunggah', 'ditemukan'].
        $query = Barang::with(['kategori', 'temuan.gedung', 'laporan.gedung'])
            ->where(function ($query) {
                // Condition 1: Lost Items
                $query->where(function ($q) {
                    $q->has('laporan')
                      ->where('status', 'belum_ditemukan');
                })
                // Condition 2: Found Items (Verified / Tarik)
                ->orWhere(function ($q) {
                    $q->has('temuan')
                      ->whereIn('status', ['diunggah', 'ditemukan']);
                });
            });

        // Filter by kategori_id or name
        if ($request->has('kategori') && $request->kategori !== 'Semua' && $request->kategori !== '') {
            $kat = $request->kategori;
            $query->where(function ($q) use ($kat) {
                if (is_numeric($kat)) {
                    $q->where('kategori_id', $kat);
                } else {
                    $q->whereHas('kategori', function ($sub) use ($kat) {
                        $sub->where('nama_kategori', $kat);
                    });
                }
            });
        }

        // Filter by gedung_id or name
        if ($request->has('gedung') && $request->gedung !== 'Semua' && $request->gedung !== '') {
            $ged = $request->gedung;
            $query->where(function ($q) use ($ged) {
                if (is_numeric($ged)) {
                    $q->where(function ($sub) use ($ged) {
                        $sub->whereHas('laporan', function ($l) use ($ged) {
                            $l->where('gedung_id', $ged);
                        })->orWhereHas('temuan', function ($t) use ($ged) {
                            $t->where('gedung_id', $ged);
                        });
                    });
                } else {
                    $q->where(function ($sub) use ($ged) {
                        $sub->whereHas('laporan.gedung', function ($l) use ($ged) {
                            $l->where('nama_gedung', $ged);
                        })->orWhereHas('temuan.gedung', function ($t) use ($ged) {
                            $t->where('nama_gedung', $ged);
                        });
                    });
                }
            });
        }

        $barangs = $query->orderBy('barang_id', 'desc')->paginate(12);

        // Return the paginator directly via ->withPath().
        // Laravel's LengthAwarePaginator serialises as:
        //   { data: [...items], current_page, last_page, total, ... }
        // useFetch reads response.data.data → items array ✅
        // Wrapping in { 'data' => $barangs } would produce double-nesting:
        //   response.data      = { current_page, data: [...], ... }   ← wrong
        //   response.data.data = paginator object, NOT items array    ← breaks render
        return response()->json($barangs);
    }

    public function show($id)
    {
        $barang = Barang::with(['kategori', 'temuan.gedung', 'laporan.gedung'])->find($id);

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
        })->with(['kategori', 'temuan.gedung', 'laporan.gedung'])->first();

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
