<?php

use App\Http\Controllers\NotesController;
use App\NativeComponents\NotesIndex;
use App\NativeComponents\TagsIndex;
use App\NativeLayouts\MainTabsLayout;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/notes');

Route::nativeGroup(MainTabsLayout::class, function (): void {
    Route::native('/notes-native', NotesIndex::class);
    Route::native('/tags-native', TagsIndex::class);
});

Route::get('notes/native/create', [NotesController::class, 'presentNativeCreate'])->name('notes.native.create');
Route::get('notes/native/{note}/edit', [NotesController::class, 'presentNativeEdit'])->name('notes.native.edit');
Route::resource('notes', NotesController::class);

Route::prefix('lw')->name('lw.')->group(function (): void {
    Route::livewire('/notes', 'pages::lw.notes.index')->name('notes.index');
    Route::livewire('/notes/create', 'pages::lw.notes.create')->name('notes.create');
    Route::livewire('/notes/{note}', 'pages::lw.notes.show')->name('notes.show');
    Route::livewire('/notes/{note}/edit', 'pages::lw.notes.edit')->name('notes.edit');
});
