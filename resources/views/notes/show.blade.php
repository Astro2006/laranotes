<x-layout :title="$note->title">
    <main class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 sm:py-14">
        <a
            href="{{ route('notes.index') }}"
            class="text-sm text-gray-500 hover:text-gray-700 dark:text-zinc-400 dark:hover:text-zinc-200"
        >
            &larr; All notes
        </a>

        <article class="mt-6 rounded-md border border-gray-200 bg-white p-6 shadow-xs sm:p-8 dark:border-zinc-800 dark:bg-zinc-900">
            <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-zinc-100">
                {{ $note->title }}
            </h1>

            <p class="mt-1 text-xs text-gray-400 dark:text-zinc-500">
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
            </p>

            <div class="mt-6 whitespace-pre-line text-sm leading-relaxed text-gray-700 dark:text-zinc-300">
                {{ $note->content }}
            </div>
        </article>

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <x-button :href="route('notes.edit', $note)">Edit note</x-button>

            <form method="POST" action="{{ route('notes.destroy', $note) }}">
                @csrf
                @method('DELETE')

                <x-button variant="danger">Delete note</x-button>
            </form>
        </div>
    </main>
</x-layout>
