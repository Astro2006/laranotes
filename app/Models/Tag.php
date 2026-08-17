<?php

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    /** @var array<int, string> */
    protected $guarded = [];

    /**
     * @return BelongsToMany<Notes, $this>
     */
    public function notes(): BelongsToMany
    {
        return $this->belongsToMany(Notes::class, 'note_tag', 'tag_id', 'note_id');
    }
}
