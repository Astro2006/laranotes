<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/notes');

Route::livewire('/notes', 'pages::notes.index')->name('notes.index');
Route::livewire('/notes/create', 'pages::notes.create')->name('notes.create');
Route::livewire('/notes/{note}', 'pages::notes.show')->name('notes.show');
Route::livewire('/notes/{note}/edit', 'pages::notes.edit')->name('notes.edit');
