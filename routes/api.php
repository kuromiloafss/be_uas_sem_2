<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\KategoriController;
use App\Http\Controllers\Api\V1\BarangController;
use App\Http\Controllers\Api\V1\LaporanKehilanganController;
use App\Http\Controllers\Api\V1\KlaimController;
use App\Http\Controllers\Api\V1\Admin\AdminController;

Route::prefix('v1')->group(function () {
    
    // Public Routes
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/check-nim', [AuthController::class, 'checkNim']);
    Route::get('/kategori', [KategoriController::class, 'index']);
    Route::get('/gedung', [KategoriController::class, 'gedungs']);
    Route::get('/barang', [BarangController::class, 'index']);
    Route::get('/barang/{id}', [BarangController::class, 'show']);
    Route::get('/barang-temuan/{id}', [BarangController::class, 'showTemuan']);
    
    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        
        // User Features
        Route::post('/laporan-kehilangan', [LaporanKehilanganController::class, 'store']);
        Route::get('/user/laporan', [LaporanKehilanganController::class, 'myReports']);
        
        Route::post('/klaim', [KlaimController::class, 'store']);
        Route::get('/user/klaim', [KlaimController::class, 'myClaims']);
        Route::get('/klaim/{id}', [KlaimController::class, 'show']);

        // Admin/Staff Features (Ideally with separate middleware, but role check can be inside)
        Route::prefix('admin')->group(function () {
            Route::get('/dashboard-stats', [AdminController::class, 'dashboardStats']);
            Route::get('/barang-temuan', [AdminController::class, 'indexBarangTemuan']);
            Route::get('/barang-hilang', [AdminController::class, 'indexBarangHilang']);
            Route::post('/barang-temuan', [AdminController::class, 'storeBarangTemuan']);
            Route::put('/barang/{id}/status', [AdminController::class, 'updateBarangStatus']);
            Route::post('/barang/{id}/tarik-data', [AdminController::class, 'convertLostToFound']);
            Route::delete('/barang/{id}', [AdminController::class, 'destroyBarang']);
            Route::get('/klaim', [KlaimController::class, 'index']); 
            Route::put('/klaim/{id}/verify', [AdminController::class, 'verifyClaim']);

            // Kategori CRUD
            Route::post('/kategori', [KategoriController::class, 'storeKategori']);
            Route::put('/kategori/{id}', [KategoriController::class, 'updateKategori']);
            Route::delete('/kategori/{id}', [KategoriController::class, 'destroyKategori']);

            // Gedung CRUD
            Route::post('/gedung', [KategoriController::class, 'storeGedung']);
            Route::put('/gedung/{id}', [KategoriController::class, 'updateGedung']);
            Route::delete('/gedung/{id}', [KategoriController::class, 'destroyGedung']);
        });
    });

});
