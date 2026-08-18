<?php

use App\Models\Notes;
use App\NativeComponents\NotesIndex;
use Codebar\NativeCrudFormV2\NativeCrudFormV2;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Native\Mobile\Events\NativeForm\ValueChanged;
use Native\Mobile\Testing\Native;

uses(RefreshDatabase::class);

it('lists existing notes', function () {
    $note = Notes::factory()->create(['title' => 'Grocery list']);

    $component = Native::test(NotesIndex::class)
        ->assertSee('Grocery list');

    expect($component->get('notes'))->toHaveCount(1)
        ->and($component->get('notes')[0]['uuid'])->toBe($note->uuid);
});

it('shows an empty state when there are no notes', function () {
    Native::test(NotesIndex::class)
        ->assertSee('No notes');
});

it('persists an edit and dismisses the sheet when the checkmark is tapped', function () {
    $note = Notes::factory()->create(['title' => 'Original title', 'content' => 'Original content']);
    $handle = "crud:notes:{$note->id}";

    $component = Native::test(NotesIndex::class)
        ->call('edit', $note->uuid);

    $component->emitNative(ValueChanged::class, [
        'rowId' => 'title',
        'type' => 'text_field',
        'value' => 'Updated title',
        'id' => $handle,
    ]);

    $component->emitNative(ValueChanged::class, [
        'rowId' => 'save',
        'type' => 'button',
        'value' => true,
        'id' => $handle,
    ])
        ->assertSee('Updated title')
        ->assertNativeCalled('NativeForm.Dismiss');

    $this->assertDatabaseHas('notes', [
        'uuid' => $note->uuid,
        'title' => 'Updated title',
    ]);
});

it('creates a note when the checkmark is tapped on a new sheet', function () {
    $component = Native::test(NotesIndex::class)
        ->call('create');

    $handle = 'crud:notes:new';

    $component->emitNative(ValueChanged::class, [
        'rowId' => 'title', 'type' => 'text_field', 'value' => 'New title', 'id' => $handle,
    ]);
    $component->emitNative(ValueChanged::class, [
        'rowId' => 'content', 'type' => 'text_field', 'value' => 'New content', 'id' => $handle,
    ]);
    $component->emitNative(ValueChanged::class, [
        'rowId' => 'save', 'type' => 'button', 'value' => true, 'id' => $handle,
    ])->assertSee('New title');

    $this->assertDatabaseHas('notes', [
        'title' => 'New title',
        'content' => 'New content',
    ]);
});

it('does not save an incomplete note', function () {
    $component = Native::test(NotesIndex::class)
        ->call('create');

    $handle = 'crud:notes:new';

    $component->emitNative(ValueChanged::class, [
        'rowId' => 'title', 'type' => 'text_field', 'value' => 'Only a title', 'id' => $handle,
    ]);
    $component->emitNative(ValueChanged::class, [
        'rowId' => 'save', 'type' => 'button', 'value' => true, 'id' => $handle,
    ]);

    $this->assertDatabaseCount('notes', 0);
});

it('deletes a note', function () {
    $note = Notes::factory()->create();

    Native::test(NotesIndex::class)
        ->call('delete', $note->uuid);

    $this->assertDatabaseMissing('notes', ['uuid' => $note->uuid]);
});

it('deletes a note from the sheet\'s delete menu item and dismisses it', function () {
    $note = Notes::factory()->create();
    $handle = "crud:notes:{$note->id}";

    Native::test(NotesIndex::class)
        ->call('edit', $note->uuid)
        ->emitNative(ValueChanged::class, [
            'rowId' => NativeCrudFormV2::DELETE_ROW_ID,
            'type' => 'button',
            'value' => true,
            'id' => $handle,
        ])
        ->assertNativeCalled('NativeForm.Dismiss');

    $this->assertDatabaseMissing('notes', ['uuid' => $note->uuid]);
});
