@props([
    'name' => 'tags',
    'id' => 'tags',
    'selected' => [],
    'available' => [],
    'invalid' => false,
])

<div
    data-tag-input
    data-available="{{ json_encode(collect($available)->values()->all(), JSON_THROW_ON_ERROR) }}"
    class="relative"
>
    <div
        data-tag-chips
        @class([
            'mt-2 flex flex-wrap items-center gap-1.5 rounded-md border bg-white px-2 py-1.5 shadow-xs focus-within:outline-2 focus-within:outline-offset-2 focus-within:outline-indigo-600 dark:bg-zinc-900',
            'border-gray-300 dark:border-zinc-700' => ! $invalid,
            'border-red-500 dark:border-red-500' => $invalid,
        ])
    >
        @foreach ($selected as $tagName)
            <span
                data-tag-chip
                data-value="{{ $tagName }}"
                class="inline-flex items-center gap-1 rounded-full bg-indigo-50 py-0.5 pr-1 pl-2 text-xs font-medium text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
            >
                <span data-tag-chip-label>{{ $tagName }}</span>
                <button
                    type="button"
                    data-tag-chip-remove
                    aria-label="Remove tag {{ $tagName }}"
                    class="rounded-full p-0.5 hover:bg-indigo-100 dark:hover:bg-indigo-500/20"
                >
                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="size-3">
                        <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                    </svg>
                </button>
            </span>
        @endforeach

        <input
            id="{{ $id }}-search"
            type="text"
            autocomplete="off"
            role="combobox"
            aria-expanded="false"
            aria-controls="{{ $id }}-dropdown"
            data-tag-search
            placeholder="{{ empty($selected) ? 'Add tags…' : '' }}"
            class="min-w-[8ch] flex-1 border-0 bg-transparent p-1 text-sm text-gray-900 focus:ring-0 focus:outline-none dark:text-zinc-100"
        >
    </div>

    <ul
        id="{{ $id }}-dropdown"
        data-tag-dropdown
        role="listbox"
        class="absolute z-10 mt-1 hidden max-h-56 w-full overflow-auto rounded-md border border-gray-200 bg-white py-1 text-sm shadow-lg dark:border-zinc-700 dark:bg-zinc-900"
    ></ul>

    <input type="hidden" name="{{ $name }}" id="{{ $id }}" data-tag-value value="{{ implode(',', $selected) }}">
</div>
