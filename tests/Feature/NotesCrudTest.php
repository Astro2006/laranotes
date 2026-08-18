<?php

use App\Models\Notes;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('create page renders the note form', function () {
    $response = $this->get(route('notes.create'));

    $response->assertOk();
    $response->assertSeeText('New note');
    $response->assertSeeText('Title');
    $response->assertSeeText('Content');
});

test('storing a note creates it and redirects to the show page', function () {
    $note = Notes::factory()->make();

    $response = $this->post(route('notes.store'), [
        'title' => $note->title,
        'content' => $note->content,
    ]);

    $created = Notes::sole();

    $response->assertRedirect(route('notes.show', $created));
    $this->assertDatabaseHas('notes', [
        'title' => $note->title,
        'content' => $note->content,
    ]);
});

test('storing a note requires a title and content', function () {
    $response = $this->post(route('notes.store'), [
        'title' => '',
        'content' => '',
    ]);

    $response->assertSessionHasErrors(['title', 'content']);
    $this->assertDatabaseCount('notes', 0);
});

test('show page displays the note', function () {
    $note = Notes::factory()->create();

    $response = $this->get(route('notes.show', $note));

    $response->assertOk();
    $response->assertSeeText($note->title);
    $response->assertSeeText($note->content);
});

test('edit page renders the note form', function () {
    $note = Notes::factory()->create();

    $response = $this->get(route('notes.edit', $note));

    $response->assertOk();
    $response->assertSeeText('Edit note');
});

test('updating a note persists changes and redirects to the show page', function () {
    $note = Notes::factory()->create();

    $response = $this->put(route('notes.update', $note), [
        'title' => 'Updated title',
        'content' => 'Updated content',
    ]);

    $response->assertRedirect(route('notes.show', $note));
    $this->assertDatabaseHas('notes', [
        'id' => $note->id,
        'title' => 'Updated title',
        'content' => 'Updated content',
    ]);
});

test('updating a note requires a non-empty title', function () {
    $note = Notes::factory()->create();

    $response = $this->put(route('notes.update', $note), [
        'title' => '',
    ]);

    $response->assertSessionHasErrors('title');
});

test('deleting a note removes it and redirects to the index page', function () {
    $note = Notes::factory()->create();

    $response = $this->delete(route('notes.destroy', $note));

    $response->assertRedirect(route('notes.index'));
    $this->assertDatabaseMissing('notes', ['id' => $note->id]);
});

test('note urls use the uuid instead of the incrementing id', function () {
    $note = Notes::factory()->create();

    $url = route('notes.show', $note);

    expect($url)
        ->toContain($note->uuid)
        ->not->toContain("/notes/{$note->id}");
});

test('visiting a note by its raw incrementing id returns not found', function () {
    $note = Notes::factory()->create();

    $response = $this->get("/notes/{$note->id}");

    $response->assertNotFound();
});
