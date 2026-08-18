<?php

use App\Models\Notes;
use App\Models\Tags;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('storing a note with tags creates and attaches the tags', function () {
    $note = Notes::factory()->make();

    $response = $this->post(route('notes.store'), [
        'title' => $note->title,
        'content' => $note->content,
        'tags' => 'work, ideas',
    ]);

    $created = Notes::sole();

    $response->assertRedirect(route('notes.show', $created));
    expect($created->tags->pluck('name')->all())->toEqualCanonicalizing(['work', 'ideas']);
});

test('storing a note reuses an existing tag instead of duplicating it', function () {
    $existing = Tags::factory()->create(['name' => 'work']);

    $this->post(route('notes.store'), [
        'title' => 'Note title',
        'content' => 'Note content',
        'tags' => 'Work',
    ]);

    $created = Notes::sole();

    expect(Tags::count())->toBe(1)
        ->and($created->tags->first()->is($existing))->toBeTrue();
});

test('storing a note without tags creates no tags', function () {
    $this->post(route('notes.store'), [
        'title' => 'Note title',
        'content' => 'Note content',
    ]);

    $created = Notes::sole();

    expect($created->tags)->toBeEmpty();
});

test('updating a note replaces its tags', function () {
    $note = Notes::factory()->create();
    $note->tags()->attach(Tags::factory()->create(['name' => 'old'])->id);

    $this->put(route('notes.update', $note), [
        'title' => $note->title,
        'content' => $note->content,
        'tags' => 'new',
    ]);

    expect($note->refresh()->tags->pluck('name')->all())->toBe(['new']);
});

test('clearing the tags field on update removes all tags', function () {
    $note = Notes::factory()->create();
    $note->tags()->attach(Tags::factory()->create()->id);

    $this->put(route('notes.update', $note), [
        'title' => $note->title,
        'content' => $note->content,
        'tags' => '',
    ]);

    expect($note->refresh()->tags)->toBeEmpty();
});

test('the notes index page displays tag badges', function () {
    $note = Notes::factory()->create();
    $note->tags()->attach(Tags::factory()->create(['name' => 'urgent'])->id);

    $response = $this->get(route('notes.index'));

    $response->assertOk();
    $response->assertSeeText('urgent');
});

test('the note show page displays tag badges', function () {
    $note = Notes::factory()->create();
    $note->tags()->attach(Tags::factory()->create(['name' => 'urgent'])->id);

    $response = $this->get(route('notes.show', $note));

    $response->assertOk();
    $response->assertSeeText('urgent');
});

test('the note form lists existing tags as pillbox options', function () {
    Tags::factory()->create(['name' => 'urgent']);

    Livewire::test('note-form')
        ->assertSee('urgent');
});

test('creating a note through the note form persists it with the selected tags', function () {
    $tag = Tags::factory()->create(['name' => 'work']);

    Livewire::test('note-form')
        ->set('title', 'My note')
        ->set('content', 'Some content')
        ->set('selectedTagIds', [$tag->id])
        ->call('save');

    $note = Notes::sole();

    expect($note->title)->toBe('My note')
        ->and($note->tags->pluck('name')->all())->toBe(['work']);
});

test('typing a new tag name and creating it adds it to the selection', function () {
    Livewire::test('note-form')
        ->set('tagSearch', 'brand new')
        ->call('createTag')
        ->assertSet('tagSearch', '');

    $tag = Tags::sole();

    expect($tag->name)->toBe('brand new');

    Livewire::test('note-form')
        ->set('title', 'My note')
        ->set('content', 'Some content')
        ->set('tagSearch', 'brand new')
        ->call('createTag')
        ->call('save');

    expect(Notes::sole()->tags->pluck('name')->all())->toBe(['brand new'])
        ->and(Tags::count())->toBe(1);
});

test('editing a note through the note form pre-fills its title, content, and tags', function () {
    $note = Notes::factory()->create();
    $tag = Tags::factory()->create(['name' => 'urgent']);
    $note->tags()->attach($tag);

    Livewire::test('note-form', ['note' => $note])
        ->assertSet('title', $note->title)
        ->assertSet('content', $note->content)
        ->assertSet('selectedTagIds', [$tag->id]);
});

test('saving edits through the note form replaces the note tags', function () {
    $note = Notes::factory()->create();
    $old = Tags::factory()->create(['name' => 'old']);
    $new = Tags::factory()->create(['name' => 'new']);
    $note->tags()->attach($old);

    Livewire::test('note-form', ['note' => $note])
        ->set('selectedTagIds', [$new->id])
        ->call('save');

    expect($note->refresh()->tags->pluck('name')->all())->toBe(['new']);
});
