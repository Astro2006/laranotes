@props([
    'action',
    'method' => 'POST',
    'tag' => null,
    'submitLabel' => 'Save tag',
])

<form method="POST" action="{{ $action }}" class="mt-6 space-y-6">
    @csrf

    @unless (Str::upper($method) === 'POST')
        @method($method)
    @endunless

    <flux:field>
        <flux:label>Name</flux:label>
        <flux:input name="name" value="{{ old('name', $tag?->name) }}" required maxlength="255" />
        <flux:error name="name" />
    </flux:field>

    <div class="flex items-center gap-3">
        <flux:button type="submit" variant="primary">{{ $submitLabel }}</flux:button>
        <flux:button :href="route('tags.index')">Cancel</flux:button>
    </div>
</form>
