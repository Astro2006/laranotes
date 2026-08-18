<?php

namespace App\Models;

use Database\Factories\NotesFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Notes extends Model
{
    /** @use HasFactory<NotesFactory> */
    use HasFactory, HasUuids;

    /** @var array<int, string> */
    protected $guarded = [];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    // --- Relationships ---

    /**
     * @return BelongsToMany<Tags, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tags::class, 'note_tag', 'note_id', 'tag_id');
    }

    // --- Query Scopes ---

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
