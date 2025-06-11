<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\Api\StructureController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\DataStatistikController;

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
        Route::get('/{detail}', [StructureController::class, 'byDetail']);
    });
    Route::prefix('programs')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\ProgramController::class, 'index']);
        Route::get('/{id}', [App\Http\Controllers\Api\ProgramController::class, 'show']);
        Route::get('/{nama_program}', [App\Http\Controllers\Api\ProgramController::class, 'byNamaProgram']);
        Route::get('/{status}', [App\Http\Controllers\Api\ProgramController::class, 'byStatus']);
    });
    Route::prefix('statistik')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\DataStatistikController::class, 'index']);
    });
});

Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now()->toISOString()
    ]);
});
