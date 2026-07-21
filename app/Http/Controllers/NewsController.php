<?php

namespace App\Http\Controllers;

use App\Helpers\StorageHelper;
use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NewsController extends Controller
{
    /**
     * Public news listing page (/berita)
     */
    public function index(Request $request): Response
    {
        $search = $request->query('q');
        $category = $request->query('category');
        $tag = $request->query('tag');

        $query = Article::published()
            ->with(['host:id,name,group_number,posko_logo_url,university', 'author:id,name'])
            ->latest('published_at');

        if ($search) {
            $query->search($search);
        }

        if ($category && $category !== 'Semua') {
            $query->byCategory($category);
        }

        if ($tag) {
            $query->whereJsonContains('tags', $tag);
        }

        $allArticles = (clone $query)->get();

        // Featured article (first one)
        $featuredArticle = $allArticles->first();

        // Grid articles (remaining)
        $gridArticles = $allArticles->slice(1)->values();

        // Format articles output
        $transformArticle = function ($article) {
            if (! $article) {
                return null;
            }

            $rawGroup = $article->host?->group_number ?: $article->host_id;
            $groupName = is_numeric($rawGroup) ? "Kelompok {$rawGroup}" : $rawGroup;
            $university = $article->host?->university;
            $authorDisplayName = $university
                ? "{$university}, {$groupName} - {$article->author?->name}"
                : "{$groupName} - {$article->author?->name}";
            $poskoLogoUrl = StorageHelper::getUrl($article->host?->posko_logo_url) ?: '/logo_superposko.png';

            return [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'category' => $article->category,
                'tags' => $article->tags ?? [],
                'excerpt' => $article->excerpt,
                'cover_image_url' => $article->cover_image_url,
                'reading_time_minutes' => $article->reading_time_minutes,
                'views_count' => $article->views_count,
                'published_at' => $article->published_at ? $article->published_at->translatedFormat('d F Y') : $article->created_at->translatedFormat('d F Y'),
                'author_display_name' => $authorDisplayName,
                'posko_logo_url' => $poskoLogoUrl,
            ];
        };

        $featuredFormatted = $featuredArticle ? $transformArticle($featuredArticle) : null;
        $gridFormatted = $gridArticles->map($transformArticle);

        // Sidebar Widgets Data
        $categoriesWithCounts = Article::published()
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get()
            ->map(fn ($item) => [
                'name' => $item->category,
                'count' => $item->total,
            ]);

        $recentArticles = Article::published()
            ->latest('published_at')
            ->take(5)
            ->get(['id', 'title', 'slug', 'cover_image', 'published_at', 'created_at'])
            ->map(fn ($art) => [
                'id' => $art->id,
                'title' => $art->title,
                'slug' => $art->slug,
                'cover_image_url' => $art->cover_image_url,
                'published_at' => $art->published_at ? $art->published_at->translatedFormat('d M Y') : $art->created_at->translatedFormat('d M Y'),
            ]);

        // Popular Tags Cloud
        $allTags = Article::published()->pluck('tags')->filter()->flatten();
        $popularTags = $allTags->countBy()
            ->sortDesc()
            ->take(10)
            ->map(fn ($count, $tagName) => [
                'name' => $tagName,
                'count' => $count,
            ])
            ->values();

        return Inertia::render('news/Index', [
            'featuredArticle' => $featuredFormatted,
            'articles' => $gridFormatted,
            'categories' => $categoriesWithCounts,
            'recentArticles' => $recentArticles,
            'popularTags' => $popularTags,
            'filters' => [
                'q' => $search,
                'category' => $category,
                'tag' => $tag,
            ],
        ]);
    }

    /**
     * Public news detail page (/berita/{slug})
     */
    public function show(string $slug): Response
    {
        $article = Article::published()
            ->with(['host:id,name,group_number,posko_logo_url,university', 'author:id,name'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views count
        $article->increment('views_count');

        // Parse Table of Contents & inject IDs into content headings
        $htmlContent = $article->content;
        $tableOfContents = [];

        if (preg_match_all('/<h([2-3])([^>]*)>(.*?)<\/h[2-3]>/i', $htmlContent, $matches, PREG_SET_ORDER)) {
            $index = 1;
            foreach ($matches as $match) {
                $level = (int) $match[1];
                $headingText = strip_tags($match[3]);
                $headingId = 'section-'.\Illuminate\Support\Str::slug($headingText)."-{$index}";

                // Inject id into heading tag if not present
                $oldHeadingTag = $match[0];
                $newHeadingTag = "<h{$level} id=\"{$headingId}\" {$match[2]}>{$match[3]}</h{$level}>";
                $htmlContent = str_replace($oldHeadingTag, $newHeadingTag, $htmlContent);

                $tableOfContents[] = [
                    'id' => $headingId,
                    'text' => $headingText,
                    'level' => $level,
                ];

                $index++;
            }
        }

        $rawGroup = $article->host?->group_number ?: $article->host_id;
        $groupName = is_numeric($rawGroup) ? "Kelompok {$rawGroup}" : $rawGroup;
        $university = $article->host?->university;
        $authorDisplayName = $university
            ? "{$university}, {$groupName} - {$article->author?->name}"
            : "{$groupName} - {$article->author?->name}";
        $poskoLogoUrl = StorageHelper::getUrl($article->host?->posko_logo_url) ?: '/logo_superposko.png';

        // Related articles
        $relatedArticles = Article::published()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->latest('published_at')
            ->take(3)
            ->get()
            ->map(fn ($art) => [
                'id' => $art->id,
                'title' => $art->title,
                'slug' => $art->slug,
                'category' => $art->category,
                'cover_image_url' => $art->cover_image_url,
                'reading_time_minutes' => $art->reading_time_minutes,
                'published_at' => $art->published_at ? $art->published_at->translatedFormat('d F Y') : $art->created_at->translatedFormat('d F Y'),
            ]);

        return Inertia::render('news/Show', [
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'category' => $article->category,
                'tags' => $article->tags ?? [],
                'excerpt' => $article->excerpt,
                'content' => $htmlContent,
                'cover_image_url' => $article->cover_image_url,
                'reading_time_minutes' => $article->reading_time_minutes,
                'views_count' => $article->views_count,
                'published_at' => $article->published_at ? $article->published_at->translatedFormat('d F Y') : $article->created_at->translatedFormat('d F Y'),
                'author_display_name' => $authorDisplayName,
                'posko_logo_url' => $poskoLogoUrl,
            ],
            'tableOfContents' => $tableOfContents,
            'relatedArticles' => $relatedArticles,
        ]);
    }
}
