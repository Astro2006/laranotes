<?php

use App\Models\Notes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('an authenticated user can perform every ability on notes', function () {
    $user = User::factory()->create();
    $note = Notes::factory()->create();

    expect($user->can('viewAny', Notes::class))->toBeTrue()
        ->and($user->can('view', $note))->toBeTrue()
        ->and($user->can('create', Notes::class))->toBeTrue()
        ->and($user->can('update', $note))->toBeTrue()
        ->and($user->can('delete', $note))->toBeTrue();
});
