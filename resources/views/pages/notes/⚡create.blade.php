<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('New note')] class extends Component
{
    //
};
?>

<main class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 sm:py-14">
    <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('notes.index')" wire:navigate>
        All notes
    </flux:button>

    <flux:heading size="xl" class="mt-6">New note</flux:heading>

    <livewire:note-form />
</main>
