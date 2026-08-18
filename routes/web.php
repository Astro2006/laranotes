<?php

use App\Http\Controllers\NotesController;
use App\Http\Controllers\SetLocaleController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/notes');

Route::get('/locale/{locale}', SetLocaleController::class)->name('locale.set');

Route::resource('notes', NotesController::class);

Route::prefix('lw')->name('lw.')->group(function (): void {
    Route::livewire('/notes', 'pages::lw.notes.index')->name('notes.index');
    Route::livewire('/notes/create', 'pages::lw.notes.create')->name('notes.create');
    Route::livewire('/notes/{note}', 'pages::lw.notes.show')->name('notes.show');
    Route::livewire('/notes/{note}/edit', 'pages::lw.notes.edit')->name('notes.edit');
});
