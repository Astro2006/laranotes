<?php

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('index page lists existing tags', function () {
    $tag = Tag::factory()->create(['name' => 'Work']);

    $response = $this->get(route('tags.index'));

    $response->assertOk();
    $response->assertSeeText($tag->name);
});

test('create page renders the tag form', function () {
    $response = $this->get(route('tags.create'));

    $response->assertOk();
    $response->assertSeeText('New tag');
    $response->assertSeeText('Name');
});

test('storing a tag creates it and redirects to the index page', function () {
    $response = $this->post(route('tags.store'), [
        'name' => 'Work',
    ]);

    $response->assertRedirect(route('tags.index'));
    $this->assertDatabaseHas('tags', ['name' => 'Work']);
});

test('storing a tag requires a name', function () {
    $response = $this->post(route('tags.store'), [
        'name' => '',
    ]);

    $response->assertSessionHasErrors('name');
    $this->assertDatabaseCount('tags', 0);
});

test('storing a tag requires a unique name', function () {
    Tag::factory()->create(['name' => 'Work']);

    $response = $this->post(route('tags.store'), [
        'name' => 'Work',
    ]);

    $response->assertSessionHasErrors('name');
    $this->assertDatabaseCount('tags', 1);
});

test('edit page renders the form pre-filled with the tag', function () {
    $tag = Tag::factory()->create();

    $response = $this->get(route('tags.edit', $tag));

    $response->assertOk();
    $response->assertSee($tag->name);
});

test('updating a tag persists changes and redirects to the index page', function () {
    $tag = Tag::factory()->create();

    $response = $this->put(route('tags.update', $tag), [
        'name' => 'Renamed',
    ]);

    $response->assertRedirect(route('tags.index'));
    $this->assertDatabaseHas('tags', ['id' => $tag->id, 'name' => 'Renamed']);
});

test('updating a tag requires a unique name', function () {
    Tag::factory()->create(['name' => 'Taken']);
    $tag = Tag::factory()->create(['name' => 'Original']);

    $response = $this->put(route('tags.update', $tag), [
        'name' => 'Taken',
    ]);

    $response->assertSessionHasErrors('name');
    $this->assertDatabaseHas('tags', ['id' => $tag->id, 'name' => 'Original']);
});

test('deleting a tag removes it and redirects to the index page', function () {
    $tag = Tag::factory()->create();

    $response = $this->delete(route('tags.destroy', $tag));

    $response->assertRedirect(route('tags.index'));
    $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
});
