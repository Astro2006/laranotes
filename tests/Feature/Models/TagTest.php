<?php

use App\Models\Notes;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('supports tag CRUD operations', function () {
    $tag = Tag::factory()->create(['name' => 'Work']);

    expect($tag->exists)->toBeTrue();
    $this->assertDatabaseHas('tags', ['id' => $tag->id, 'name' => 'Work']);

    $fresh = Tag::query()->findOrFail($tag->id);
    expect($fresh->name)->toBe('Work');

    $fresh->update(['name' => 'Personal']);
    $this->assertDatabaseHas('tags', ['id' => $fresh->id, 'name' => 'Personal']);

    $fresh->delete();
    $this->assertDatabaseMissing('tags', ['id' => $fresh->id]);
});

it('defines and resolves the notes relation', function () {
    $tag = Tag::factory()->create();

    expect($tag->notes())->toBeInstanceOf(BelongsToMany::class);

    $notes = Notes::factory()->count(2)->create();
    $tag->notes()->attach($notes);

    expect($tag->notes)->toHaveCount(2)
        ->and($tag->notes->first())->toBeInstanceOf(Notes::class);
});
