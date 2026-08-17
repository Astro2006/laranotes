<?php

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    /** @var array<int, string> */
    protected $guarded = [];

    // --- Relationships ---

    /**
     * @return BelongsToMany<Notes, $this>
     */
    public function notes(): BelongsToMany
    {
        return $this->belongsToMany(Notes::class, 'note_tag', 'tag_id', 'note_id');
    }

    // --- Helpers ---

    /**
     * Find or create tags for the given comma-separated list of names.
     *
     * @return Collection<int, Tag>
     */
    public static function fromNameList(?string $names): Collection
    {
        return collect(explode(',', (string) $names))
            ->map(fn (string $name): string => Str::of($name)->trim()->squish()->value())
            ->filter()
            ->unique(fn (string $name): string => Str::lower($name))
            ->map(fn (string $name): self => static::query()->whereRaw('lower(name) = ?', [Str::lower($name)])->first()
                ?? static::query()->create(['name' => $name]))
            ->values();
    }
}
