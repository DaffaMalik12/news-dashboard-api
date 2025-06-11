<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;


class ProgramController extends Controller
{
    //
    public function index(Request $request)
    {
        // Implement your logic to fetch and return programs
        // You can use pagination, filtering, etc. as needed
        $query = Program::query();

        // filter by id
        if ($request->has('id') && !empty($request->id)) {
            $query->where('id', $request->id);
        }

        // Filter by Status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // filter by nama program
        if ($request->has('nama_program') && !empty($request->status)) {
            $query->where('nama_program', $request->status);
        }

        // Eksekusi query untuk mendapatkan hasilnya
        $program = $query->get(); // <-- Panggil get() di akhir


        // Example response
        return response()->json([
            'message' => 'List of programs',
            'data' => $program // Replace with actual data
        ]);

        // jika data tidak ditemukan
        if ($program->isEmpty()) {
            return response()->json([
                'message' => 'No programs found based on provided filters'
            ], 404);
        }
    }
}
