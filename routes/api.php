<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\Api\StructureController;

Route::middleware('api.key')->prefix('v1')->group(function () {
    Route::prefix('berita')->group(function () {
        Route::get('/', [BeritaController::class, 'index']);
        Route::get('/latest', [BeritaController::class, 'latest']);
        Route::get('/categories', [BeritaController::class, 'categories']);
        Route::get('/kategori/{kategori}', [BeritaController::class, 'byKategori']);
        Route::get('/{slug}', [BeritaController::class, 'show']);
       
    });
    Route::prefix('structures')->group(function () {
        Route::get('/', [StructureController::class, 'index']);
        Route::get('/{id}', [StructureController::class, 'show']);
        Route::get('/{jabatan}', [StructureController::class, 'byJabatan']);
        Route::get('/{nama}', [StructureController::class, 'byNama']);
        
    });
});

Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now()->toISOString()
    ]);
});