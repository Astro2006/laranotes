<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('New note (Livewire)')] class extends Component
{
    //
};
?>

<main class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 sm:py-14">
    <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('lw.notes.index')" wire:navigate>
        {{ __('All notes') }}
    </flux:button>

    <flux:heading size="xl" class="mt-6">{{ __('New note') }}</flux:heading>

    <livewire:lw-note-form />
</main>
