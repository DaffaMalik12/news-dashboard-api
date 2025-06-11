<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengumuman; // Ensure this model exists

class PengumumanController extends Controller
{
    //

    public function index(Request $request)
    {
        // Implement your logic to fetch and return announcements
        // You can use pagination, filtering, etc. as needed
        $query = Pengumuman::query();


        // Filter by id
        if ($request->has('id') && !empty($request->id)) {
            $query->where('id', $request->id);
        }

        // Filter by judul
        if ($request->has('judul') && !empty($request->judul)) {
            $query->where('judul', 'like', '%' . $request->judul . '%');
        }

        // Filter by tanggal_pengumuman
        if ($request->has('tanggal_pengumuman') && !empty($request->tanggal_pengumuman)) {
            $query->whereDate('tanggal_pengumuman', $request->tanggal_pengumuman);
        }

        // Execute query to get results
        $pengumuman = $query->get(); // <-- Call get() at the end

        // If no announcements found
        if ($pengumuman->isEmpty()) {
            return response()->json([
                'message' => 'No announcements found based on provided filters'
            ], 404);
        }

        // Example response
        return response()->json([
            'message' => 'List of announcements',
            'data' => $pengumuman // Replace with actual data
        ]);
    }
}
