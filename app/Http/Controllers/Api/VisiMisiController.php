<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VisiMisiController extends Controller
{
    //
    public function index()
    {
        // Mengambil semua data visi dan misi
        $visimisi = \App\Models\VisiMisi::all();

        // filter  berdasarkan id
        if (request()->has('id')) {
            $id = request()->input('id');
            $visimisi = $visimisi->where('id', $id);
        }

        // Mengembalikan response JSON
        return response()->json([
            'success' => true,
            'data' => $visimisi,
        ]);
    }
}
