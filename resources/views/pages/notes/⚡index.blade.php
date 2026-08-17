<?php

use App\Models\Notes;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Notes')] class extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'notes' => Notes::search($this->search ?: null)->latest()->paginate(15),
        ];
    }
};
?>

<main class="mx-auto w-full max-w-7xl px-4 py-10 sm:px-6 sm:py-14">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <flux:heading size="lg">
            @if ($search)
                {{ trans_choice('{0}No notes found|{1}1 note found|[2,*]:count notes found', $notes->total(), ['count' => $notes->total()]) }}
                for &ldquo;{{ $search }}&rdquo;
            @else
                {{ trans_choice('{0}No notes|{1}1 note|[2,*]:count notes', $notes->total(), ['count' => $notes->total()]) }}
            @endif
        </flux:heading>

        <flux:button variant="primary" icon="plus" :href="route('notes.create')" wire:navigate>New note</flux:button>
    </div>

    <div class="mt-4 flex items-center gap-2 sm:max-w-xs">
        <flux:input
            wire:model.live.debounce.400ms="search"
            type="search"
            icon="magnifying-glass"
            placeholder="Search notes by title or content&hellip;"
            aria-label="Search notes by title or content"
        />

        @if ($search)
            <flux:button variant="ghost" size="sm" wire:click="$set('search', '')">Clear</flux:button>
        @endif
    </div>

    @if ($notes->isEmpty())
        <flux:card class="mt-3 border-dashed px-6 py-16 text-center">
            <flux:icon.document-text class="mx-auto size-12 text-gray-400 dark:text-zinc-600" />

            @if ($search)
                <flux:heading size="sm" class="mt-4">No notes found</flux:heading>
                <flux:text class="mt-1">No notes match &ldquo;{{ $search }}&rdquo;. Try a different search term.</flux:text>
                <div class="mt-6">
                    <flux:button wire:click="$set('search', '')">Clear search</flux:button>
                </div>
            @else
                <flux:heading size="sm" class="mt-4">No notes</flux:heading>
                <flux:text class="mt-1">Get started by creating a new note.</flux:text>
                <div class="mt-6">
                    <flux:button variant="primary" icon="plus" :href="route('notes.create')" wire:navigate>New note</flux:button>
                </div>
            @endif
        </flux:card>
    @else
        <ul role="list" class="mt-3 grid grid-cols-1 gap-4 md:hidden">
            @foreach ($notes as $note)
                <livewire:note-card :note="$note" :key="'mobile-'.$note->id" />
            @endforeach
        </ul>

        <div class="mt-3 hidden md:block">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Title</flux:table.column>
                    <flux:table.column>Content</flux:table.column>
                    <flux:table.column>Created</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($notes as $note)
                        <flux:table.row :key="$note->id">
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="{{ $note->title }}" color="auto" color:seed="{{ $note->id }}" />

                                    <a
                                        href="{{ route('notes.show', $note) }}"
                                        wire:navigate
                                        class="font-medium text-gray-900 hover:text-gray-600 dark:text-zinc-100 dark:hover:text-zinc-300"
                                    >
                                        {{ $note->title }}
                                    </a>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="max-w-xs truncate">{{ Str::of($note->content)->stripTags()->squish() }}</flux:table.cell>

                            <flux:table.cell class="whitespace-nowrap">
                                <time datetime="{{ $note->created_at->toIso8601String() }}">
                                    {{ $note->created_at->diffForHumans(short: true) }}
                                </time>
                            </flux:table.cell>

                            <flux:table.cell align="end">
                                <flux:button variant="ghost" size="sm" icon="pencil" :href="route('notes.edit', $note)" wire:navigate>
                                    Edit<span class="sr-only">, {{ $note->title }}</span>
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>

        @if ($notes->hasPages())
            <div class="mt-8">
                {{ $notes->links() }}
            </div>
        @endif
    @endif
</main>
