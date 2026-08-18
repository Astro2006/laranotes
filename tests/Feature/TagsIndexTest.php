<?php

use App\Models\Tags;
use App\NativeComponents\TagsIndex;
use Codebar\NativeCrudFormV2\NativeCrudFormV2;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Native\Mobile\Events\NativeForm\ValueChanged;
use Native\Mobile\Testing\Native;

uses(RefreshDatabase::class);

it('lists existing tags', function () {
    $tag = Tags::factory()->create(['name' => 'Urgent']);

    $component = Native::test(TagsIndex::class)
        ->assertSee('Urgent');

    expect($component->get('tags'))->toHaveCount(1)
        ->and($component->get('tags')[0]['uuid'])->toBe($tag->uuid);
});

it('shows an empty state when there are no tags', function () {
    Native::test(TagsIndex::class)
        ->assertSee('No tags');
});

it('creates a tag when the checkmark is tapped on a new sheet', function () {
    $component = Native::test(TagsIndex::class)
        ->call('create');

    $handle = 'crud:tags:new';

    $component->emitNative(ValueChanged::class, [
        'rowId' => 'name', 'type' => 'text_field', 'value' => 'Personal', 'id' => $handle,
    ]);
    $component->emitNative(ValueChanged::class, [
        'rowId' => 'save', 'type' => 'button', 'value' => true, 'id' => $handle,
    ])->assertSee('Personal');

    $this->assertDatabaseHas('tags', ['name' => 'Personal']);
});

it('does not save an incomplete tag', function () {
    $component = Native::test(TagsIndex::class)
        ->call('create');

    $handle = 'crud:tags:new';

    $component->emitNative(ValueChanged::class, [
        'rowId' => 'save', 'type' => 'button', 'value' => true, 'id' => $handle,
    ]);

    $this->assertDatabaseCount('tags', 0);
});

it('persists an edit and dismisses the sheet when the checkmark is tapped', function () {
    $tag = Tags::factory()->create(['name' => 'Original']);
    $handle = "crud:tags:{$tag->id}";

    $component = Native::test(TagsIndex::class)
        ->call('edit', $tag->uuid);

    $component->emitNative(ValueChanged::class, [
        'rowId' => 'name', 'type' => 'text_field', 'value' => 'Renamed', 'id' => $handle,
    ]);

    $component->emitNative(ValueChanged::class, [
        'rowId' => 'save', 'type' => 'button', 'value' => true, 'id' => $handle,
    ])
        ->assertSee('Renamed')
        ->assertNativeCalled('NativeForm.Dismiss');

    $this->assertDatabaseHas('tags', ['uuid' => $tag->uuid, 'name' => 'Renamed']);
});

it('deletes a tag', function () {
    $tag = Tags::factory()->create();

    Native::test(TagsIndex::class)
        ->call('delete', $tag->uuid);

    $this->assertDatabaseMissing('tags', ['uuid' => $tag->uuid]);
});

it('deletes a tag from the sheet\'s delete menu item and dismisses it', function () {
    $tag = Tags::factory()->create();
    $handle = "crud:tags:{$tag->id}";

    Native::test(TagsIndex::class)
        ->call('edit', $tag->uuid)
        ->emitNative(ValueChanged::class, [
            'rowId' => NativeCrudFormV2::DELETE_ROW_ID,
            'type' => 'button',
            'value' => true,
            'id' => $handle,
        ])
        ->assertNativeCalled('NativeForm.Dismiss');

    $this->assertDatabaseMissing('tags', ['uuid' => $tag->uuid]);
});
