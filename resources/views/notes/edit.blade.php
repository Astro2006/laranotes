<x-layout :title="__('Edit note')">
    <main class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 sm:py-14">
        <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('notes.show', $note)">
            {{ __('Back to note') }}
        </flux:button>

        <flux:heading size="xl" class="mt-6">{{ __('Edit note') }}</flux:heading>

        <livewire:note-form :note="$note" />
    </main>
</x-layout>
