<?php

namespace App\Actions\Notes;

use App\Helpers\NoteContentHelper;
use App\Models\Notes;

class UpdateNote
{
    public function execute(Notes $note, string $title, string $content): Notes
    {
        $note->update([
            'title' => $title,
            'content' => NoteContentHelper::sanitize($content),
        ]);

        return $note;
    }
}
