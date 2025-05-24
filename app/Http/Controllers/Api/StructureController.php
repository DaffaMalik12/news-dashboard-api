<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\structure; // Pastikan ini sudah diimport
use Illuminate\Http\Request;

class StructureController extends Controller
{
    /**
     * Display a listing of the resource (all structures).
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Mengambil semua data structure dari database
        $structures = Structure::all();

        // Mengembalikan data dalam format JSON dengan status 200 OK
        return response()->json([
            'message' => 'Data structures retrieved successfully',
            'data' => $structures
        ], 200);
    }

    /**
     * Display the specified resource (single structure by ID).
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        // Mencari data structure berdasarkan ID yang diberikan
        $structure = Structure::find($id);

        // Jika data tidak ditemukan, kembalikan response 404 Not Found
        if (!$structure) {
            return response()->json([
                'message' => 'Structure not found'
            ], 404);
        }

        // Jika data ditemukan, kembalikan data dalam format JSON dengan status 200 OK
        return response()->json([
            'message' => 'Structure retrieved successfully',
            'data' => $structure
        ], 200);
    }

    // Metode store, update, dan destroy dihapus karena CRUD dihandle Filament.
}