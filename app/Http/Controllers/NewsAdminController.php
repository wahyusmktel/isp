<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsAdminController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', '');
        $search = $request->get('search', '');

        $query = News::withCount('comments')->latest();

        if ($status) $query->where('status', $status);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        $news = $query->paginate(15)->withQueryString();

        $stats = [
            'total'     => News::count(),
            'published' => News::where('status', 'published')->count(),
            'draft'     => News::where('status', 'draft')->count(),
            'month'     => News::whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count(),
        ];

        return view('news.admin', compact('news', 'stats', 'status', 'search'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'category'         => 'required|in:pengumuman,gangguan,promo,tips,umum',
            'status'           => 'required|in:draft,published',
            'author'           => 'required|string|max:100',
            'excerpt'          => 'nullable|string|max:500',
            'body'             => 'required|string',
            'cover_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'published_at'     => 'nullable|date',
        ]);

        $validated['slug'] = News::generateSlug($validated['title']);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $this->uploadImage($request->file('cover_image'));
        }

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $item = News::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Berita \"{$item->title}\" berhasil dipublikasikan.",
        ]);
    }

    public function update(Request $request, News $news): JsonResponse
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'category'         => 'required|in:pengumuman,gangguan,promo,tips,umum',
            'status'           => 'required|in:draft,published',
            'author'           => 'required|string|max:100',
            'excerpt'          => 'nullable|string|max:500',
            'body'             => 'required|string',
            'cover_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'published_at'     => 'nullable|date',
        ]);

        if ($validated['title'] !== $news->title) {
            $validated['slug'] = News::generateSlug($validated['title'], $news->id);
        }

        if ($request->hasFile('cover_image')) {
            $this->deleteImage($news->cover_image);
            $validated['cover_image'] = $this->uploadImage($request->file('cover_image'));
        }

        if ($validated['status'] === 'published' && !$news->published_at && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $news->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Berita \"{$news->title}\" berhasil diperbarui.",
        ]);
    }

    public function destroy(News $news): JsonResponse
    {
        $this->deleteImage($news->cover_image);
        $title = $news->title;
        $news->delete();

        return response()->json([
            'success' => true,
            'message' => "Berita \"{$title}\" berhasil dihapus.",
        ]);
    }

    public function destroyComment(NewsComment $comment): JsonResponse
    {
        $comment->delete();
        return response()->json(['success' => true, 'message' => 'Komentar dihapus.']);
    }

    private function uploadImage($file): string
    {
        $dir = public_path('uploads/news');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);
        return $filename;
    }

    private function deleteImage(?string $filename): void
    {
        if ($filename && !str_starts_with($filename, 'http')) {
            $path = public_path('uploads/news/' . $filename);
            if (file_exists($path)) unlink($path);
        }
    }
}
