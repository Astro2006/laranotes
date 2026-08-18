<form wire:submit="save" class="mt-6 space-y-6">
    <flux:field>
        <flux:label>{{ __('Title') }}</flux:label>
        <flux:input wire:model="title" required maxlength="255" autofocus />
        <flux:error name="title" />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('Content') }}</flux:label>
        <flux:textarea wire:model="content" rows="10" required />
        <flux:error name="content" />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('Tags') }}</flux:label>

        <flux:pillbox wire:model="selectedTagIds" variant="combobox" multiple :placeholder="__('Choose tags...')">
            <x-slot name="input">
                <flux:pillbox.input wire:model.live="tagSearch" :placeholder="__('Choose tags...')" />
            </x-slot>

            @foreach ($this->tags as $tag)
                <flux:pillbox.option :value="$tag->id">{{ $tag->name }}</flux:pillbox.option>
            @endforeach

            <flux:pillbox.option.create wire:click="createTag" min-length="1">
                {{ __('Create') }} &ldquo;<span wire:text="tagSearch"></span>&rdquo;
            </flux:pillbox.option.create>
        </flux:pillbox>

        <flux:description>{{ __('Pick an existing tag or create a new one.') }}</flux:description>
    </flux:field>

    <div class="flex items-center gap-3">
        <flux:button type="submit" variant="primary">{{ $note ? __('Save changes') : __('Create note') }}</flux:button>
        <flux:button :href="route('notes.index')" wire:navigate>{{ __('Cancel') }}</flux:button>
    </div>
</form>
