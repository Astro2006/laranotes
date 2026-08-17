<?php

use App\Http\Controllers\NotesController;
use App\Http\Controllers\TagsController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/notes');

Route::resource('notes', NotesController::class);
Route::resource('tags', TagsController::class)->except('show');
