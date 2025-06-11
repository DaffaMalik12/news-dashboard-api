<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\structure; // Pastikan ini sudah diimport
use Illuminate\Http\Request;

class StructureController extends Controller
{
    /**
     * Display a listing of the resource (all structures) with optional filters.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) // <-- Ambil instance Request
    {
        // Mulai membangun query
        $query = Structure::query(); // <-- Gunakan query() untuk memulai query builder

        // Jika ada filter berdasarkan ID (query parameter: ?id=...)
        if ($request->has('id')) {
            $query->where('id', $request->get('id'));
        }

        // Jika ada filter berdasarkan nama (query parameter: ?nama=...)
        if ($request->has('nama')) {
            $query->where('Nama', 'like', '%' . $request->get('nama') . '%'); // Perhatikan 'Nama' jika case-sensitive
        }

        // Jika ada filter berdasarkan jabatan (query parameter: ?jabatan=...)
        if ($request->has('jabatan')) {
            $query->where('Jabatan', 'like', '%' . $request->get('jabatan') . '%'); // Perhatikan 'Jabatan' jika case-sensitive
        }

        // jika ada filter berdasarkan detail (query paramater: ?detail-...)
        if ($request->has('detail')) {
            $query->where('Detail', 'like', '%' . $request->get('detail') . '%'); // Perhatikan 'Detail' jika case-sensitive
        }

        // Eksekusi query untuk mendapatkan hasilnya
        $structures = $query->get(); // <-- Panggil get() di akhir

        // Jika tidak ada data ditemukan
        if ($structures->isEmpty()) {
            return response()->json([
                'message' => 'No structures found based on provided filters'
            ], 404);
        }

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
