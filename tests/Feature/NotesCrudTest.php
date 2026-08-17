<?php

use App\Models\Notes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('create page renders the note form', function () {
    Livewire::test('pages::notes.create')
        ->assertSee('New note')
        ->assertSee('Title')
        ->assertSee('Content');
});

test('saving a new note creates it and redirects to the show page', function () {
    Livewire::test('note-form')
        ->set('title', 'Grocery list')
        ->set('content', '<p>Buy milk and eggs</p>')
        ->call('save')
        ->assertRedirect(route('notes.show', Notes::sole()));

    $this->assertDatabaseHas('notes', [
        'title' => 'Grocery list',
        'content' => '<p>Buy milk and eggs</p>',
    ]);
});

test('saving a note strips disallowed html from content', function () {
    Livewire::test('note-form')
        ->set('title', 'Grocery list')
        ->set('content', '<script>alert(1)</script><p onclick="evil()">Buy milk</p>')
        ->call('save');

    $note = Notes::sole();

    expect($note->content)
        ->not->toContain('<script')
        ->not->toContain('onclick')
        ->toContain('<p>Buy milk</p>');
});

test('a note requires a title and content', function () {
    Livewire::test('note-form')
        ->set('title', '')
        ->set('content', '')
        ->call('save')
        ->assertHasErrors(['title', 'content']);

    $this->assertDatabaseCount('notes', 0);
});

test('a note requires non-empty content even when the editor only sent empty markup', function () {
    Livewire::test('note-form')
        ->set('title', 'Empty note')
        ->set('content', '<p></p>')
        ->call('save')
        ->assertHasErrors('content');
});

test('show page displays the note', function () {
    $note = Notes::factory()->create(['content' => '<p>Hello world</p>']);

    Livewire::test('pages::notes.show', ['note' => $note])
        ->assertSee($note->title)
        ->assertSee('Hello world');
});

test('edit form is pre-filled with the note', function () {
    $note = Notes::factory()->create();

    Livewire::test('note-form', ['note' => $note])
        ->assertSet('title', $note->title)
        ->assertSet('content', $note->content);
});

test('updating a note persists changes and redirects to the show page', function () {
    $note = Notes::factory()->create();

    Livewire::test('note-form', ['note' => $note])
        ->set('title', 'Updated title')
        ->set('content', '<p>Updated content</p>')
        ->call('save')
        ->assertRedirect(route('notes.show', $note));

    $this->assertDatabaseHas('notes', [
        'id' => $note->id,
        'title' => 'Updated title',
        'content' => '<p>Updated content</p>',
    ]);
});

test('updating a note requires a non-empty title', function () {
    $note = Notes::factory()->create();

    Livewire::test('note-form', ['note' => $note])
        ->set('title', '')
        ->call('save')
        ->assertHasErrors('title');
});

test('deleting a note removes it and redirects to the index page', function () {
    $note = Notes::factory()->create();

    Livewire::test('pages::notes.show', ['note' => $note])
        ->call('delete')
        ->assertRedirect(route('notes.index'));

    $this->assertDatabaseMissing('notes', ['id' => $note->id]);
});
