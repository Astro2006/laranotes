<?php

use App\Models\Notes;
use App\Models\Tags;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('the lw notes index page renders', function () {
    $note = Notes::factory()->create();

    $response = $this->get(route('lw.notes.index'));

    $response->assertOk();
    $response->assertSeeText($note->title);
});

test('the lw notes create page renders the note form', function () {
    $response = $this->get(route('lw.notes.create'));

    $response->assertOk();
    $response->assertSeeText('New note');
});

test('creating a note through the lw note form dispatches a success toast and redirects', function () {
    Livewire::test('lw-note-form')
        ->set('title', 'My note')
        ->set('content', 'Some content')
        ->call('save')
        ->assertDispatched('toast-show')
        ->assertRedirect();

    $note = Notes::sole();

    expect($note->title)->toBe('My note');
});

test('the lw note show page uses the uuid instead of the incrementing id', function () {
    $note = Notes::factory()->create();

    $url = route('lw.notes.show', $note);

    expect($url)
        ->toContain($note->uuid)
        ->not->toContain("/lw/notes/{$note->id}");
});

test('the lw notes index page includes a delete confirmation modal for each note', function () {
    $note = Notes::factory()->create();

    $response = $this->get(route('lw.notes.index'));

    $response->assertOk();
    $response->assertSeeText('Delete note?');
});

test('deleting a note from the lw index page dispatches a toast', function () {
    $note = Notes::factory()->create();

    Livewire::test('pages::lw.notes.index')
        ->call('delete', $note->id)
        ->assertDispatched('toast-show');

    $this->assertDatabaseMissing('notes', ['id' => $note->id]);
});

test('the lw note show page includes a delete confirmation modal', function () {
    $note = Notes::factory()->create();

    $response = $this->get(route('lw.notes.show', $note));

    $response->assertOk();
    $response->assertSeeText('Delete note?');
});

test('deleting a note through the lw show page dispatches a toast and redirects to the index', function () {
    $note = Notes::factory()->create();

    Livewire::test('pages::lw.notes.show', ['note' => $note])
        ->call('delete')
        ->assertDispatched('toast-show')
        ->assertRedirect(route('lw.notes.index'));

    $this->assertDatabaseMissing('notes', ['id' => $note->id]);
});

test('saving edits through the lw note form dispatches a success toast', function () {
    $note = Notes::factory()->create();

    Livewire::test('lw-note-form', ['note' => $note])
        ->set('title', 'Updated title')
        ->call('save')
        ->assertDispatched('toast-show');

    expect($note->refresh()->title)->toBe('Updated title');
});

test('the lw note form lists existing tags as pillbox options', function () {
    Tags::factory()->create(['name' => 'urgent']);

    Livewire::test('lw-note-form')
        ->assertSee('urgent');
});

test('creating a note through the lw note form persists it with the selected tags', function () {
    $tag = Tags::factory()->create(['name' => 'work']);

    Livewire::test('lw-note-form')
        ->set('title', 'My note')
        ->set('content', 'Some content')
        ->set('selectedTagIds', [$tag->id])
        ->call('save');

    $note = Notes::sole();

    expect($note->tags->pluck('name')->all())->toBe(['work']);
});

test('the lw note show page displays tag badges', function () {
    $note = Notes::factory()->create();
    $note->tags()->attach(Tags::factory()->create(['name' => 'urgent'])->id);

    $response = $this->get(route('lw.notes.show', $note));

    $response->assertOk();
    $response->assertSeeText('urgent');
});
