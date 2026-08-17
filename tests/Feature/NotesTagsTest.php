<?php

use App\Models\Notes;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('creating a note attaches tags parsed from a comma separated list', function () {
    $response = $this->post(route('notes.store'), [
        'title' => 'Grocery list',
        'content' => 'Milk, eggs, bread',
        'tags' => 'shopping, personal,  shopping ',
    ]);

    $note = Notes::firstOrFail();

    $response->assertRedirect(route('notes.show', $note));
    expect($note->tags->pluck('name')->all())->toBe(['shopping', 'personal']);
});

test('creating a note without tags attaches none', function () {
    $this->post(route('notes.store'), [
        'title' => 'Grocery list',
        'content' => 'Milk, eggs, bread',
    ]);

    $note = Notes::firstOrFail();

    expect($note->tags)->toBeEmpty();
});

test('updating a note syncs its tags', function () {
    $note = Notes::factory()->create();
    $note->tags()->attach(Tag::factory()->create(['name' => 'old-tag']));

    $response = $this->put(route('notes.update', $note), [
        'title' => $note->title,
        'content' => $note->content,
        'tags' => 'new-tag',
    ]);

    $response->assertRedirect(route('notes.show', $note));
    expect($note->fresh()->tags->pluck('name')->all())->toBe(['new-tag']);
});

test('clearing the tags field removes all tags from a note', function () {
    $note = Notes::factory()->create();
    $note->tags()->attach(Tag::factory()->create());

    $this->put(route('notes.update', $note), [
        'title' => $note->title,
        'content' => $note->content,
        'tags' => '',
    ]);

    expect($note->fresh()->tags)->toBeEmpty();
});

test('reusing a tag name does not create a duplicate tag', function () {
    Tag::factory()->create(['name' => 'shopping']);

    $this->post(route('notes.store'), [
        'title' => 'Note one',
        'content' => 'Content one',
        'tags' => 'Shopping',
    ]);

    expect(Tag::query()->count())->toBe(1);
});

test('note show page displays its tags', function () {
    $note = Notes::factory()->create();
    $note->tags()->attach(Tag::factory()->create(['name' => 'important']));

    $response = $this->get(route('notes.show', $note));

    $response->assertOk();
    $response->assertSeeText('important');
});
