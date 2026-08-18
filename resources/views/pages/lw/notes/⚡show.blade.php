<?php

use App\Models\Notes;
use Flux\Flux;
use Livewire\Component;

new class extends Component
{
    public Notes $note;

    public function delete(): void
    {
        $this->note->delete();

        Flux::toast(text: 'Note deleted.', variant: 'danger');

        $this->redirect(route('lw.notes.index'), navigate: true);
    }

    public function render()
    {
        return $this->view()->title($this->note->title);
    }
};
?>

<main class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 sm:py-14">
    <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('lw.notes.index')" wire:navigate>
        All notes
    </flux:button>

    <flux:card class="mt-6 p-6 sm:p-8">
        <flux:heading size="xl">{{ $note->title }}</flux:heading>

        @if ($note->tags->isNotEmpty())
            <div class="mt-3 flex flex-wrap gap-1">
                @foreach ($note->tags as $tag)
                    <x-tag-badge :tag="$tag" />
                @endforeach
            </div>
        @endif

        <flux:text variant="subtle" class="mt-1 text-xs">
            Created
            <time datetime="{{ $note->created_at->toIso8601String() }}">
                {{ $note->created_at->isoFormat('LLL') }}
            </time>

            @if ($note->updated_at->ne($note->created_at))
                &middot; edited
                <time datetime="{{ $note->updated_at->toIso8601String() }}">
                    {{ $note->updated_at->diffForHumans() }}
                </time>
            @endif
        </flux:text>

        <div class="mt-6 text-sm leading-relaxed text-gray-700 dark:text-zinc-300
            [&_h1]:mt-4 [&_h1]:text-lg [&_h1]:font-semibold [&_h1]:first:mt-0
            [&_h2]:mt-4 [&_h2]:text-base [&_h2]:font-semibold [&_h2]:first:mt-0
            [&_h3]:mt-4 [&_h3]:text-sm [&_h3]:font-semibold [&_h3]:first:mt-0
            [&_p]:mt-3 [&_p]:first:mt-0
            [&_ul]:mt-3 [&_ul]:list-disc [&_ul]:pl-5
            [&_ol]:mt-3 [&_ol]:list-decimal [&_ol]:pl-5
            [&_blockquote]:mt-3 [&_blockquote]:border-l-2 [&_blockquote]:border-gray-300 [&_blockquote]:pl-4 [&_blockquote]:italic [&_blockquote]:dark:border-zinc-700
            [&_a]:underline [&_a]:text-indigo-600 [&_a]:dark:text-indigo-400"
        >
            {!! $note->content !!}
        </div>
    </flux:card>

    <div class="mt-6 flex flex-wrap items-center gap-3">
        <flux:button variant="primary" icon="pencil" :href="route('lw.notes.edit', $note)" wire:navigate>Edit note</flux:button>

        <flux:modal.trigger name="delete-note">
            <flux:button variant="danger" icon="trash">Delete note</flux:button>
        </flux:modal.trigger>
    </div>

    <flux:modal name="delete-note" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete note?</flux:heading>

                <flux:text class="mt-2">
                    You're about to delete "{{ $note->title }}".<br>
                    This action cannot be reversed.
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <flux:button type="button" variant="danger" wire:click="delete">Delete note</flux:button>
            </div>
        </div>
    </flux:modal>
</main>
