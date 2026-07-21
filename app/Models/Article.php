<?php

namespace App\Models;

use App\Helpers\StorageHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'host_id',
        'author_id',
        'title',
        'slug',
        'category',
        'tags',
        'excerpt',
        'content',
        'cover_image',
        'reading_time_minutes',
        'views_count',
        'cta_wa_count',
        'cta_fb_count',
        'cta_ig_count',
        'cta_copy_count',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'reading_time_minutes' => 'integer',
        'views_count' => 'integer',
        'cta_wa_count' => 'integer',
        'cta_fb_count' => 'integer',
        'cta_ig_count' => 'integer',
        'cta_copy_count' => 'integer',
    ];

    protected $appends = [
        'cover_image_url',
        'total_cta_count',
    ];

    public function getTotalCtaCountAttribute(): int
    {
        return (int) ($this->cta_wa_count + $this->cta_fb_count + $this->cta_ig_count + $this->cta_copy_count);
    }

    /**
     * Posko Host User (Group)
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    /**
     * Member Author User
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get accessible cover image URL
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        return StorageHelper::getUrl($this->cover_image);
    }

    /**
     * Scope for published articles
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(function (Builder $q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Scope for filtering by category
     */
    public function scopeByCategory(Builder $query, ?string $category): Builder
    {
        if (! $category || $category === 'Semua') {
            return $query;
        }

        return $query->where('category', $category);
    }

    /**
     * Scope for search
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('excerpt', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%");
        });
    }
}
