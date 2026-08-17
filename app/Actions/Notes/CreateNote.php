<?php

namespace App\Actions\Notes;

use App\Helpers\NoteContentHelper;
use App\Models\Notes;

class CreateNote
{
    public function execute(string $title, string $content): Notes
    {
        return Notes::create([
            'title' => $title,
            'content' => NoteContentHelper::sanitize($content),
        ]);
    }
}
