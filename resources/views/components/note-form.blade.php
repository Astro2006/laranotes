@props([
    'action',
    'method' => 'POST',
    'note' => null,
    'submitLabel' => 'Save note',
])

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

    <div class="flex items-center gap-3">
        <flux:button type="submit" variant="primary">{{ $submitLabel }}</flux:button>
        <flux:button :href="route('notes.index')">Cancel</flux:button>
    </div>
</form>
