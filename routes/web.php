<?php

use App\Http\Controllers\NotesController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/notes');

Route::resource('notes', NotesController::class);
