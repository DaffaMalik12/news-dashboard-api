<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BeritaController;

Route::middleware('api.key')->prefix('v1')->group(function () {
    Route::prefix('berita')->group(function () {
        Route::get('/', [BeritaController::class, 'index']);
        Route::get('/latest', [BeritaController::class, 'latest']);
        Route::get('/categories', [BeritaController::class, 'categories']);
        Route::get('/kategori/{kategori}', [BeritaController::class, 'byKategori']);
        Route::get('/{slug}', [BeritaController::class, 'show']);
    });
});

Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now()->toISOString()
    ]);
});