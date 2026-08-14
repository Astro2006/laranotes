<?php

namespace App\Models;

use Database\Factories\NotesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{
    /** @use HasFactory<NotesFactory> */
    use HasFactory;

    /** @var array<int, string> */
    protected $guarded = [];
}
