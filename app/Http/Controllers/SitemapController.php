<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect([
            [
                'loc' => $this->absoluteUrl('/'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ],
            [
                'loc' => $this->absoluteUrl('/berita'),
                'lastmod' => $this->latestNewsDate(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'loc' => $this->absoluteUrl('/terms'),
                'lastmod' => now()->subMonth()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.4',
            ],
        ]);

        $newsUrls = News::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->get(['slug', 'updated_at', 'published_at'])
            ->map(fn (News $news) => [
                'loc' => $this->absoluteUrl('/berita/' . $news->slug),
                'lastmod' => ($news->updated_at ?? $news->published_at)->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ]);

        $xml = view('sitemap', [
            'urls' => $urls->merge($newsUrls),
        ])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $content = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /dashboard',
            'Disallow: /news',
            'Disallow: /customers',
            'Disallow: /invoices',
            'Disallow: /financial',
            'Disallow: /settings',
            'Disallow: /users',
            '',
            'Sitemap: ' . $this->absoluteUrl('/sitemap.xml'),
            '',
        ]);

        return response($content, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    private function latestNewsDate(): string
    {
        $latest = News::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->latest('updated_at')
            ->first(['updated_at']);

        return $latest ? $latest->updated_at->toAtomString() : now()->toAtomString();
    }

    private function absoluteUrl(string $path): string
    {
        $baseUrl = rtrim(config('app.url'), '/');

        if ($baseUrl === 'http://localhost' || $baseUrl === 'https://localhost') {
            $baseUrl = 'https://tim-7.net';
        }

        return $baseUrl . '/' . ltrim($path, '/');
    }
}
