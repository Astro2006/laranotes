<?php

namespace App\Models;

use Database\Factories\TagsFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Tags extends Model
{
    /** @use HasFactory<TagsFactory> */
    use HasFactory;

    /** @var array<int, string> */
    protected $guarded = [];

    /** @var array<int, string> */
    private const array BADGE_COLORS = [
        'red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal',
        'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose',
    ];

    // --- Relationships ---

    /**
     * @return BelongsToMany<Notes, $this>
     */
    public function notes(): BelongsToMany
    {
        return $this->belongsToMany(Notes::class, 'note_tag', 'tag_id', 'note_id');
    }

    // --- Accessors ---

    /**
     * A Flux badge color deterministically derived from the tag name, so the same tag always renders the same color.
     */
    protected function badgeColor(): Attribute
    {
        return Attribute::make(
            get: fn (): string => self::BADGE_COLORS[crc32($this->name) % count(self::BADGE_COLORS)],
        );
    }

    // --- Helpers ---

    /**
     * Find or create tags for the given comma-separated list of names, matching existing tags case-insensitively.
     *
     * @return Collection<int, Tags>
     */
    public static function fromNameList(?string $names): Collection
    {
        return collect(explode(',', (string) $names))
            ->map(fn (string $name): string => Str::of($name)->trim()->squish()->value())
            ->filter()
            ->unique(fn (string $name): string => Str::lower($name))
            ->map(fn (string $name): Tags => Tags::query()->whereRaw('lower(name) = ?', [Str::lower($name)])->first()
                ?? Tags::query()->create(['name' => $name]))
            ->values();
    }
}
