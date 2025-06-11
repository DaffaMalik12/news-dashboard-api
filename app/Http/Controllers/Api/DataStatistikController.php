<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataStatistik; // Pastikan model ini sudah ada
use Illuminate\Support\Facades\Redis;

class DataStatistikController extends Controller
{
    //
    public function index(Request $request)
    {
        // Implement your logic to fetch and return data statistics
        // You can use pagination, filtering, etc. as needed
        $query = Datastatistik::query();

        // filter by id
        if ($request->has('id') && !empty($request->id)) {
            $query->where('id', $request->id);
        }

        // filter by nama_data
        if ($request->has('nama_data') && !empty($request->nama_data)) {
            $query->where('nama_data', 'like', '%' . $request->nama_data . '%');
        }

        // Eksekusi query untuk mendapatkan hasilnya
        $dataStatistik = $query->get(); // <-- Panggil get() di akhir

        // jika data tidak ditemukan
        if ($dataStatistik->isEmpty()) {
            return response()->json([
                'message' => 'No data statistics found based on provided filters'
            ], 404);
        }

        // Example response
        return response()->json([
            'message' => 'List of data statistics',
            'data' => $dataStatistik // Replace with actual data
        ]);
    }
}
