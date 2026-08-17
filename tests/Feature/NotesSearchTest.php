<?php

use App\Models\Notes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('notes index lists all notes without a search term', function () {
    Notes::factory()->count(3)->create();

    Livewire::test('pages::notes.index')
        ->assertSee('3 notes');
});

test('notes can be searched by title', function () {
    Notes::factory()->create(['title' => 'Grocery list']);
    Notes::factory()->create(['title' => 'Meeting notes']);
    Notes::factory()->create(['title' => 'Weekend groceries']);

    Livewire::test('pages::notes.index')
        ->set('search', 'grocer')
        ->assertSee('Grocery list')
        ->assertSee('Weekend groceries')
        ->assertDontSee('Meeting notes');
});

test('notes can be searched by content', function () {
    Notes::factory()->create(['title' => 'Grocery list', 'content' => 'Remember to buy xylophones and kumquats']);
    Notes::factory()->create(['title' => 'Meeting notes', 'content' => 'Discuss the quarterly roadmap']);

    Livewire::test('pages::notes.index')
        ->set('search', 'xylophones')
        ->assertSee('Grocery list')
        ->assertDontSee('Meeting notes');
});

test('notes search is case insensitive', function () {
    Notes::factory()->create(['title' => 'Grocery list']);

    Livewire::test('pages::notes.index')
        ->set('search', 'GROCERY')
        ->assertSee('Grocery list');
});

test('notes search shows an empty state when nothing matches', function () {
    Notes::factory()->create(['title' => 'Grocery list']);

    Livewire::test('pages::notes.index')
        ->set('search', 'nonexistent')
        ->assertSee('No notes found');
});
