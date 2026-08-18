<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('switching locale persists it in the session and redirects back', function () {
    $response = $this->get(route('locale.set', 'de'));

    $response->assertRedirect();
    expect(session('locale'))->toBe('de');
});

test('an invalid locale segment is rejected with a 404', function () {
    $this->get('/locale/fr')->assertNotFound();
});

test('visiting notes after switching to german renders german strings', function () {
    $this->get(route('locale.set', 'de'));

    $response = $this->get(route('notes.index'));

    $response->assertOk();
    $response->assertSeeText('Neue Notiz');
});

test('the language switcher is present on the plain notes index page', function () {
    $this->get(route('notes.index'))
        ->assertSee(route('locale.set', 'de'), false)
        ->assertSee(route('locale.set', 'en'), false);
});

test('the language switcher is present on the livewire notes index page', function () {
    $this->get(route('lw.notes.index'))
        ->assertSee(route('locale.set', 'de'), false)
        ->assertSee(route('locale.set', 'en'), false);
});
