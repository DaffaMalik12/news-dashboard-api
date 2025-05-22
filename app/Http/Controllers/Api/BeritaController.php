<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class BeritaController extends Controller
{
    /**
     * Get all berita with pagination and filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = Berita::query();

        // Filter by kategori
        if ($request->has('kategori') && !empty($request->kategori)) {
            $query->where('kategori', $request->kategori);
        }

        // Filter by penulis
        if ($request->has('penulis') && !empty($request->penulis)) {
            $query->where('penulis', 'like', '%' . $request->penulis . '%');
        }

        // Search by judul or isi
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('isi', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by date range
        if ($request->has('dari_tanggal') && !empty($request->dari_tanggal)) {
            $query->whereDate('tanggal_publish', '>=', $request->dari_tanggal);
        }

        if ($request->has('sampai_tanggal') && !empty($request->sampai_tanggal)) {
            $query->whereDate('tanggal_publish', '<=', $request->sampai_tanggal);
        }

        // Only published articles (optional - if you want to add status later)
        $query->whereDate('tanggal_publish', '<=', now());

        // Sorting
        $sortBy = $request->get('sort_by', 'tanggal_publish');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 10);
        $perPage = min($perPage, 100); // Max 100 items per page

        $beritas = $query->paginate($perPage);

        // Transform data
        $beritas->getCollection()->transform(function ($berita) {
            return [
                'id' => $berita->id,
                'judul' => $berita->judul,
                'slug' => $berita->slug,
                'isi' => $berita->isi,
                'excerpt' => $this->createExcerpt($berita->isi, 200),
                'kategori' => $berita->kategori,
                'penulis' => $berita->penulis,
                'tanggal_publish' => $berita->tanggal_publish,
                'tanggal_publish_formatted' => Carbon::parse($berita->tanggal_publish)->format('d M Y'),
                'gambar' => $berita->gambar ? asset('storage/' . $berita->gambar) : null,
                'created_at' => $berita->created_at,
                'updated_at' => $berita->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data berita berhasil diambil',
            'data' => $beritas->items(),
            'meta' => [
                'current_page' => $beritas->currentPage(),
                'from' => $beritas->firstItem(),
                'last_page' => $beritas->lastPage(),
                'per_page' => $beritas->perPage(),
                'to' => $beritas->lastItem(),
                'total' => $beritas->total(),
            ],
            'links' => [
                'first' => $beritas->url(1),
                'last' => $beritas->url($beritas->lastPage()),
                'prev' => $beritas->previousPageUrl(),
                'next' => $beritas->nextPageUrl(),
            ]
        ]);
    }

    /**
     * Get single berita by slug
     */
    public function show(string $slug): JsonResponse
    {
        $berita = Berita::where('slug', $slug)
            ->whereDate('tanggal_publish', '<=', now())
            ->first();

        if (!$berita) {
            return response()->json([
                'success' => false,
                'message' => 'Berita tidak ditemukan',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail berita berhasil diambil',
            'data' => [
                'id' => $berita->id,
                'judul' => $berita->judul,
                'slug' => $berita->slug,
                'isi' => $berita->isi,
                'kategori' => $berita->kategori,
                'penulis' => $berita->penulis,
                'tanggal_publish' => $berita->tanggal_publish,
                'tanggal_publish_formatted' => Carbon::parse($berita->tanggal_publish)->format('d M Y H:i'),
                'gambar' => $berita->gambar ? asset('storage/' . $berita->gambar) : null,
                'created_at' => $berita->created_at,
                'updated_at' => $berita->updated_at,
            ]
        ]);
    }

    /**
     * Get latest berita
     */
    public function latest(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 5);
        $limit = min($limit, 20); // Max 20 items

        $beritas = Berita::whereDate('tanggal_publish', '<=', now())
            ->orderBy('tanggal_publish', 'desc')
            ->limit($limit)
            ->get();

        $beritas = $beritas->map(function ($berita) {
            return [
                'id' => $berita->id,
                'judul' => $berita->judul,
                'slug' => $berita->slug,
                'excerpt' => $this->createExcerpt($berita->isi, 150),
                'kategori' => $berita->kategori,
                'penulis' => $berita->penulis,
                'tanggal_publish' => $berita->tanggal_publish,
                'tanggal_publish_formatted' => Carbon::parse($berita->tanggal_publish)->format('d M Y'),
                'gambar' => $berita->gambar ? asset('storage/' . $berita->gambar) : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Berita terbaru berhasil diambil',
            'data' => $beritas
        ]);
    }

    /**
     * Get berita by kategori
     */
    public function byKategori(string $kategori, Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $perPage = min($perPage, 50);

        $beritas = Berita::where('kategori', $kategori)
            ->whereDate('tanggal_publish', '<=', now())
            ->orderBy('tanggal_publish', 'desc')
            ->paginate($perPage);

        $beritas->getCollection()->transform(function ($berita) {
            return [
                'id' => $berita->id,
                'judul' => $berita->judul,
                'slug' => $berita->slug,
                'excerpt' => $this->createExcerpt($berita->isi, 200),
                'kategori' => $berita->kategori,
                'penulis' => $berita->penulis,
                'tanggal_publish' => $berita->tanggal_publish,
                'tanggal_publish_formatted' => Carbon::parse($berita->tanggal_publish)->format('d M Y'),
                'gambar' => $berita->gambar ? asset('storage/' . $berita->gambar) : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => "Berita kategori {$kategori} berhasil diambil",
            'data' => $beritas->items(),
            'meta' => [
                'current_page' => $beritas->currentPage(),
                'from' => $beritas->firstItem(),
                'last_page' => $beritas->lastPage(),
                'per_page' => $beritas->perPage(),
                'to' => $beritas->lastItem(),
                'total' => $beritas->total(),
            ]
        ]);
    }

    /**
     * Get available categories
     */
    public function categories(): JsonResponse
    {
        $categories = Berita::select('kategori')
            ->distinct()
            ->whereDate('tanggal_publish', '<=', now())
            ->orderBy('kategori')
            ->pluck('kategori');

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diambil',
            'data' => $categories
        ]);
    }

    /**
     * Create excerpt from content
     */
    private function createExcerpt(string $content, int $length = 200): string
    {
        // Strip HTML tags
        $text = strip_tags($content);
        
        // Truncate and add ellipsis if needed
        if (strlen($text) > $length) {
            $text = substr($text, 0, $length);
            $text = substr($text, 0, strrpos($text, ' ')) . '...';
        }
        
        return $text;
    }
}