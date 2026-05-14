<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('kategori', '');
        $page     = (int) $request->get('page', 1);

        $base = News::where('status', 'published');
        if ($category) {
            $base->where('category', $category);
        }

        // Featured: first published article, only on page 1 without category filter
        $featured = null;
        if (!$category && $page === 1) {
            $featured = (clone $base)->latest('published_at')->first();
        }

        // Grid: all except featured
        $gridQuery = (clone $base)->latest('published_at');
        if ($featured) {
            $gridQuery->where('id', '!=', $featured->id);
        }

        $news = $gridQuery->paginate(9)->withQueryString();

        $categories = [
            'pengumuman' => 'Pengumuman',
            'gangguan'   => 'Gangguan',
            'promo'      => 'Promo',
            'tips'       => 'Tips & Trik',
            'umum'       => 'Umum',
        ];

        return view('news.index', compact('news', 'featured', 'category', 'categories'));
    }

    public function show(string $slug)
    {
        $news = News::where('slug', $slug)
                    ->where('status', 'published')
                    ->firstOrFail();

        $news->increment('view_count');

        $comments = $news->comments()->get();

        $related = News::where('status', 'published')
                       ->where('id', '!=', $news->id)
                       ->where('category', $news->category)
                       ->latest('published_at')
                       ->take(3)
                       ->get();

        if ($related->count() < 3) {
            $ids = $related->pluck('id')->push($news->id);
            $extra = News::where('status', 'published')
                         ->whereNotIn('id', $ids)
                         ->latest('published_at')
                         ->take(3 - $related->count())
                         ->get();
            $related = $related->concat($extra);
        }

        return view('news.show', compact('news', 'comments', 'related'));
    }
}
