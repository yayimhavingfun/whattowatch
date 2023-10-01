<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    const DEFAULT_AUTHOR = 'Гость';

    protected $visible = [
        'id',
        'text',
        'rating',
        'parent_id',
        'created_at',
        'author',
    ];

    protected $fillable = [
        'text',
        'rating',
        'parent_id',
        'user_id',
    ];

    protected $appends = [
        'author',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

    public function getAuthorAttribute()
    {
        return $this->user->name ?? 'Гость';
    }
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
