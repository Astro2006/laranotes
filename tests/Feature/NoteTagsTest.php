<?php

use App\Models\Notes;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('storing a note attaches the selected tags', function () {
    $note = Notes::factory()->make();
    $tags = Tag::factory()->count(2)->create();

    $response = $this->post(route('notes.store'), [
        'title' => $note->title,
        'content' => $note->content,
        'tags' => $tags->pluck('id')->all(),
    ]);

    $created = Notes::sole();

    $response->assertRedirect(route('notes.show', $created));
    expect($created->tags->pluck('id')->sort()->values()->all())
        ->toBe($tags->pluck('id')->sort()->values()->all());
});

test('storing a note without tags attaches none', function () {
    $note = Notes::factory()->make();

    $this->post(route('notes.store'), [
        'title' => $note->title,
        'content' => $note->content,
    ]);

    expect(Notes::sole()->tags)->toBeEmpty();
});

test('storing a note rejects unknown tag ids', function () {
    $note = Notes::factory()->make();

    $response = $this->post(route('notes.store'), [
        'title' => $note->title,
        'content' => $note->content,
        'tags' => [9999],
    ]);

    $response->assertSessionHasErrors('tags.0');
    $this->assertDatabaseCount('notes', 0);
});

test('updating a note syncs its tags, replacing previous ones', function () {
    $note = Notes::factory()->create();
    $oldTag = Tag::factory()->create();
    $newTag = Tag::factory()->create();
    $note->tags()->attach($oldTag);

    $response = $this->put(route('notes.update', $note), [
        'title' => $note->title,
        'content' => $note->content,
        'tags' => [$newTag->id],
    ]);

    $response->assertRedirect(route('notes.show', $note));
    expect($note->fresh()->tags->pluck('id')->all())->toBe([$newTag->id]);
});

test('updating a note without tags detaches all of them', function () {
    $note = Notes::factory()->create();
    $note->tags()->attach(Tag::factory()->create());

    $this->put(route('notes.update', $note), [
        'title' => $note->title,
        'content' => $note->content,
    ]);

    expect($note->fresh()->tags)->toBeEmpty();
});

test('deleting a tag detaches it from notes without deleting the notes', function () {
    $note = Notes::factory()->create();
    $tag = Tag::factory()->create();
    $note->tags()->attach($tag);

    $this->delete(route('tags.destroy', $tag));

    $this->assertDatabaseMissing('note_tag', ['note_id' => $note->id, 'tag_id' => $tag->id]);
    $this->assertDatabaseHas('notes', ['id' => $note->id]);
});

test('deleting a note detaches its tags without deleting the tags', function () {
    $note = Notes::factory()->create();
    $tag = Tag::factory()->create();
    $note->tags()->attach($tag);

    $this->delete(route('notes.destroy', $note));

    $this->assertDatabaseMissing('note_tag', ['note_id' => $note->id, 'tag_id' => $tag->id]);
    $this->assertDatabaseHas('tags', ['id' => $tag->id]);
});

test('show page displays attached tags', function () {
    $note = Notes::factory()->create();
    $tag = Tag::factory()->create(['name' => 'Important']);
    $note->tags()->attach($tag);

    $response = $this->get(route('notes.show', $note));

    $response->assertOk();
    $response->assertSeeText('Important');
});
