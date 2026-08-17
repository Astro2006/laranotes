<?php

namespace App\Models;

use Database\Factories\NotesFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Notes extends Model
{
    /** @use HasFactory<NotesFactory> */
    use HasFactory;

    /** @var array<int, string> */
    protected $guarded = [];

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'note_tag', 'note_id', 'tag_id');
    }

    /**
     * Scope a query to only include notes whose title or content matches the given search term.
     *
     * @param  Builder<Notes>  $query
     * @return Builder<Notes>
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        $escaped = str_replace(['%', '_'], ['\%', '\_'], $term);

        return $query->where(function (Builder $query) use ($escaped): void {
            $query->where('title', 'like', "%{$escaped}%")
                ->orWhere('content', 'like', "%{$escaped}%");
        });
    }
}
