<?php

namespace App\Models;

use Database\Factories\NotesFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{
    /** @use HasFactory<NotesFactory> */
    use HasFactory;

    /** @var array<int, string> */
    protected $guarded = [];

    /**
     * Scope a query to only include notes whose title matches the given search term.
     *
     * @param  Builder<Notes>  $query
     * @return Builder<Notes>
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $term
            ? $query->where('title', 'like', '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%')
            : $query;
    }
}
