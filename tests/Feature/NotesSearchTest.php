<?php

use App\Models\Notes;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('notes index lists all notes without a search term', function () {
    Notes::factory()->count(3)->create();

    $response = $this->get(route('notes.index'));

    $response->assertOk();
    $response->assertViewHas('notes', fn ($notes) => $notes->total() === 3);
});

test('notes can be searched by title', function () {
    Notes::factory()->create(['title' => 'Grocery list']);
    Notes::factory()->create(['title' => 'Meeting notes']);
    Notes::factory()->create(['title' => 'Weekend groceries']);

    $response = $this->get(route('notes.index', ['search' => 'grocer']));

    $response->assertOk();
    $response->assertViewHas('notes', fn ($notes) => $notes->total() === 2);
    $response->assertSeeText('Grocery list');
    $response->assertSeeText('Weekend groceries');
    $response->assertDontSeeText('Meeting notes');
});

test('notes search is case insensitive', function () {
    Notes::factory()->create(['title' => 'Grocery list']);

    $response = $this->get(route('notes.index', ['search' => 'GROCERY']));

    $response->assertOk();
    $response->assertSeeText('Grocery list');
});

test('notes search shows an empty state when nothing matches', function () {
    Notes::factory()->create(['title' => 'Grocery list']);

    $response = $this->get(route('notes.index', ['search' => 'nonexistent']));

    $response->assertOk();
    $response->assertSeeText('No notes found');
});
