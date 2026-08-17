@props([
    'action',
    'method' => 'POST',
    'note' => null,
    'tags' => [],
    'submitLabel' => 'Save note',
])

@php
    $selectedTags = old('tags', $note?->tags->pluck('id')->all() ?? []);
@endphp

<form method="POST" action="{{ $action }}" class="mt-6 space-y-6">
    @csrf

    @unless (Str::upper($method) === 'POST')
        @method($method)
    @endunless

    <flux:field>
        <flux:label>Title</flux:label>
        <flux:input name="title" value="{{ old('title', $note?->title) }}" required maxlength="255" />
        <flux:error name="title" />
    </flux:field>

    <flux:field>
        <flux:label>Content</flux:label>
        <flux:textarea name="content" rows="10" required>{{ old('content', $note?->content) }}</flux:textarea>
        <flux:error name="content" />
    </flux:field>

    <flux:field>
        <flux:label>Tags</flux:label>

        @if ($tags->isEmpty())
            <flux:text variant="subtle">
                No tags yet. <flux:link :href="route('tags.create')">Create one</flux:link> first.
            </flux:text>
        @else
            <div class="flex flex-wrap gap-3">
                @foreach ($tags as $tag)
                    <flux:checkbox
                        name="tags[]"
                        value="{{ $tag->id }}"
                        label="{{ $tag->name }}"
                        :checked="in_array($tag->id, $selectedTags)"
                    />
                @endforeach
            </div>
        @endif

        <flux:error name="tags" />
    </flux:field>

    <div class="flex items-center gap-3">
        <flux:button type="submit" variant="primary">{{ $submitLabel }}</flux:button>
        <flux:button :href="route('notes.index')">Cancel</flux:button>
    </div>
</form>
