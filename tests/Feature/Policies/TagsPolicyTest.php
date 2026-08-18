<?php

use App\Models\Tags;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('an authenticated user can perform every ability on tags', function () {
    $user = User::factory()->create();
    $tag = Tags::factory()->create();

    expect($user->can('viewAny', Tags::class))->toBeTrue()
        ->and($user->can('view', $tag))->toBeTrue()
        ->and($user->can('create', Tags::class))->toBeTrue()
        ->and($user->can('update', $tag))->toBeTrue()
        ->and($user->can('delete', $tag))->toBeTrue();
});
