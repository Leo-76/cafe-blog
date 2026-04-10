<?php

namespace VotrePseudo\CafeBlog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'cafe_blog_comments';

    protected $fillable = [
        'post_id', 'user_id', 'guest_name', 'guest_email',
        'body', 'approved',
    ];

    protected $casts = ['approved' => 'boolean'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(config('cafe-blog.user_model'), 'user_id');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approved', true);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('approved', false);
    }

    public function displayName(): string
    {
        return $this->author?->name ?? $this->guest_name ?? 'Anonyme';
    }
}
