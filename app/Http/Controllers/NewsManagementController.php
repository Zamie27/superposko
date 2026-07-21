<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class NewsManagementController extends Controller
{
    /**
     * Display list of posko articles for management
     */
    public function index(Request $request): Response
    {
        $user = auth()->user();
        $hostId = $user->host_id ?? $user->id;

        $articles = Article::where('host_id', $hostId)
            ->with(['author:id,name'])
            ->latest()
            ->get()
            ->map(fn ($art) => [
                'id' => $art->id,
                'title' => $art->title,
                'slug' => $art->slug,
                'category' => $art->category,
                'tags' => $art->tags ?? [],
                'excerpt' => $art->excerpt,
                'cover_image_url' => $art->cover_image_url,
                'views_count' => $art->views_count,
                'cta_wa_count' => $art->cta_wa_count ?? 0,
                'cta_fb_count' => $art->cta_fb_count ?? 0,
                'cta_ig_count' => $art->cta_ig_count ?? 0,
                'cta_copy_count' => $art->cta_copy_count ?? 0,
                'total_cta_count' => $art->total_cta_count ?? 0,
                'reading_time_minutes' => $art->reading_time_minutes,
                'is_published' => $art->is_published,
                'published_at' => $art->published_at ? $art->published_at->format('Y-m-d H:i') : null,
                'created_at' => $art->created_at->translatedFormat('d M Y, H:i'),
                'author_name' => $art->author?->name ?? 'Anggota',
            ]);

        return Inertia::render('news/Management', [
            'articles' => $articles,
        ]);
    }

    /**
     * Show form to create new article
     */
    public function create(): Response
    {
        return Inertia::render('news/Editor', [
            'article' => null,
            'categories' => [
                'Kegiatan Posko',
                'Edukasi & UMKM',
                'Pemberdayaan Masyarakat',
                'Teknologi',
                'Kesehatan & Lingkungan',
                'Berita Desa',
            ],
        ]);
    }

    /**
     * Store a newly created article
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $hostId = $user->host_id ?? $user->id;
        $hostUser = User::find($hostId);

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'is_published' => ['boolean'],
            'cover_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        ]);

        $groupRaw = $hostUser?->group_number ?: "Kelompok {$hostId}";
        if (is_numeric($groupRaw)) {
            $groupRaw = "Kelompok {$groupRaw}";
        }
        $groupSlug = Str::slug($groupRaw, '_');

        // Generate unique slug
        $baseSlug = Str::slug($request->title);
        $slug = $baseSlug;
        $counter = 1;
        while (Article::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        // Calculate reading time
        $wordCount = str_word_count(strip_tags($request->content));
        $readingTime = max(1, (int) ceil($wordCount / 200));

        // Upload cover image to MinIO if present
        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $fileName = time().'_'.Str::random(8).'.webp';
            $coverPath = "client/{$groupSlug}/image/news/{$fileName}";

            $disk = env('FILESYSTEM_DISK', 's3');
            try {
                Storage::disk($disk)->put($coverPath, file_get_contents($file->getRealPath()));
            } catch (\Throwable $e) {
                // S3 fallback
            }
        }

        Article::create([
            'host_id' => $hostId,
            'author_id' => $user->id,
            'title' => $request->title,
            'slug' => $slug,
            'category' => $request->category,
            'tags' => $request->tags ?? [],
            'excerpt' => $request->excerpt ?: Str::limit(strip_tags($request->content), 160),
            'content' => $request->content,
            'cover_image' => $coverPath,
            'reading_time_minutes' => $readingTime,
            'is_published' => $request->boolean('is_published', true),
            'published_at' => $request->boolean('is_published', true) ? now() : null,
        ]);

        return redirect()->route('news-management.index')->with('success', 'Artikel berita posko berhasil dibuat.');
    }

    /**
     * Show form to edit an existing article
     */
    public function edit(Article $article): Response
    {
        $user = auth()->user();
        $hostId = $user->host_id ?? $user->id;

        if ($article->host_id !== $hostId) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit artikel posko ini.');
        }

        return Inertia::render('news/Editor', [
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'category' => $article->category,
                'tags' => $article->tags ?? [],
                'excerpt' => $article->excerpt,
                'content' => $article->content,
                'cover_image_url' => $article->cover_image_url,
                'is_published' => $article->is_published,
            ],
            'categories' => [
                'Kegiatan Posko',
                'Edukasi & UMKM',
                'Pemberdayaan Masyarakat',
                'Teknologi',
                'Kesehatan & Lingkungan',
                'Berita Desa',
            ],
        ]);
    }

    /**
     * Update an existing article
     */
    public function update(Request $request, Article $article): RedirectResponse
    {
        $user = auth()->user();
        $hostId = $user->host_id ?? $user->id;
        $hostUser = User::find($hostId);

        if ($article->host_id !== $hostId) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit artikel posko ini.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'is_published' => ['boolean'],
            'cover_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        ]);

        $groupRaw = $hostUser?->group_number ?: "Kelompok {$hostId}";
        if (is_numeric($groupRaw)) {
            $groupRaw = "Kelompok {$groupRaw}";
        }
        $groupSlug = Str::slug($groupRaw, '_');

        // Update slug if title changed
        $slug = $article->slug;
        if ($request->title !== $article->title) {
            $baseSlug = Str::slug($request->title);
            $slug = $baseSlug;
            $counter = 1;
            while (Article::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }
        }

        // Calculate reading time
        $wordCount = str_word_count(strip_tags($request->content));
        $readingTime = max(1, (int) ceil($wordCount / 200));

        // Upload cover image to MinIO if present
        $coverPath = $article->cover_image;
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $fileName = time().'_'.Str::random(8).'.webp';
            $coverPath = "client/{$groupSlug}/image/news/{$fileName}";

            $disk = env('FILESYSTEM_DISK', 's3');
            try {
                Storage::disk($disk)->put($coverPath, file_get_contents($file->getRealPath()));
            } catch (\Throwable $e) {
                // S3 fallback
            }
        }

        $article->update([
            'title' => $request->title,
            'slug' => $slug,
            'category' => $request->category,
            'tags' => $request->tags ?? [],
            'excerpt' => $request->excerpt ?: Str::limit(strip_tags($request->content), 160),
            'content' => $request->content,
            'cover_image' => $coverPath,
            'reading_time_minutes' => $readingTime,
            'is_published' => $request->boolean('is_published', true),
            'published_at' => $request->boolean('is_published', true) ? ($article->published_at ?: now()) : null,
        ]);

        return redirect()->route('news-management.index')->with('success', 'Artikel berita posko berhasil diperbarui.');
    }

    /**
     * Delete an article
     */
    public function destroy(Article $article): RedirectResponse
    {
        $user = auth()->user();
        $hostId = $user->host_id ?? $user->id;

        if ($article->host_id !== $hostId) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus artikel posko ini.');
        }

        if ($article->cover_image) {
            $disk = env('FILESYSTEM_DISK', 's3');
            try {
                Storage::disk($disk)->delete($article->cover_image);
            } catch (\Throwable $e) {
                // Ignore delete error
            }
        }

        $article->delete();

        return redirect()->route('news-management.index')->with('success', 'Artikel berita posko berhasil dihapus.');
    }

    /**
     * Upload an inline image for article body content
     */
    public function uploadImage(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:10240'],
        ]);

        $user = auth()->user();
        $hostId = $user->host_id ?? $user->id;
        $hostUser = User::find($hostId);

        $groupRaw = $hostUser?->group_number ?: "Kelompok {$hostId}";
        if (is_numeric($groupRaw)) {
            $groupRaw = "Kelompok {$groupRaw}";
        }
        $groupSlug = Str::slug($groupRaw, '_');

        $file = $request->file('image');
        $fileName = time().'_'.Str::random(8).'.webp';
        $imagePath = "client/{$groupSlug}/image/news_inline/{$fileName}";

        $disk = env('FILESYSTEM_DISK', 's3');
        Storage::disk($disk)->put($imagePath, file_get_contents($file->getRealPath()));

        $url = \App\Helpers\StorageHelper::getUrl($imagePath);

        return response()->json([
            'url' => $url,
            'path' => $imagePath,
        ]);
    }
}
