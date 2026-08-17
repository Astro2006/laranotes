<?php

use App\Models\Notes;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Edit note')] class extends Component
{
    public Notes $note;
};
?>

<main class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 sm:py-14">
    <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('notes.show', $note)" wire:navigate>
        Back to note
    </flux:button>

    <flux:heading size="xl" class="mt-6">Edit note</flux:heading>

    <livewire:note-form :note="$note" :key="$note->id" />
</main>
