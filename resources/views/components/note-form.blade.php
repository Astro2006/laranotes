@props([
    'action',
    'method' => 'POST',
    'note' => null,
    'submitLabel' => 'Save note',
])

@php
    $inputClasses = 'mt-2 block w-full rounded-md border bg-white px-3 py-2 text-sm text-gray-900 shadow-xs focus:outline-2 focus:outline-offset-2 focus:outline-indigo-600 dark:bg-zinc-900 dark:text-zinc-100';
@endphp

<form method="POST" action="{{ $action }}" class="mt-6 space-y-6">
    @csrf

    @unless (Str::upper($method) === 'POST')
        @method($method)
    @endunless

    <div>
        <label for="title" class="block text-sm font-medium text-gray-900 dark:text-zinc-100">
            Title
        </label>

        <input
            id="title"
            name="title"
            type="text"
            value="{{ old('title', $note?->title) }}"
            required
            maxlength="255"
            @class([
                $inputClasses,
                'border-gray-300 dark:border-zinc-700' => ! $errors->has('title'),
                'border-red-500 dark:border-red-500' => $errors->has('title'),
            ])
        >

        @error('title')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="content" class="block text-sm font-medium text-gray-900 dark:text-zinc-100">
            Content
        </label>

        <textarea
            id="content"
            name="content"
            rows="10"
            required
            @class([
                $inputClasses,
                'border-gray-300 dark:border-zinc-700' => ! $errors->has('content'),
                'border-red-500 dark:border-red-500' => $errors->has('content'),
            ])
        >{{ old('content', $note?->content) }}</textarea>

        @error('content')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-3">
        <x-button>{{ $submitLabel }}</x-button>
        <x-button variant="secondary" :href="route('notes.index')">Cancel</x-button>
    </div>
</form>
