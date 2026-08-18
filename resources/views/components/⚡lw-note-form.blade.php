<?php

use App\Actions\Notes\CreateNote;
use App\Actions\Notes\UpdateNote;
use App\Models\Notes;
use App\Models\Tags;
use App\Rules\NoteRules;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public ?Notes $note = null;

    public string $title = '';

    public string $content = '';

    /** @var array<int, int> */
    public array $selectedTagIds = [];

    public string $tagSearch = '';

    public function mount(?Notes $note = null): void
    {
        $this->note = $note;
        $this->title = $note->title ?? '';
        $this->content = $note->content ?? '';
        $this->selectedTagIds = $note?->tags->pluck('id')->all() ?? [];
    }

    /**
     * @return Collection<int, Tags>
     */
    #[Computed]
    public function tags(): Collection
    {
        return Tags::query()->orderBy('name')->get();
    }

    public function createTag(): void
    {
        $tag = Tags::fromNameList($this->tagSearch)->first();

        if ($tag && ! in_array($tag->id, $this->selectedTagIds, true)) {
            $this->selectedTagIds[] = $tag->id;
        }

        $this->tagSearch = '';
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', new NoteRules],
            'content' => ['required', 'string', new NoteRules],
        ];
    }

    public function save(CreateNote $createNote, UpdateNote $updateNote): void
    {
        $this->validate();

        $isNew = ! $this->note;

        $note = $this->note
            ? $updateNote->execute($this->note, $this->title, $this->content)
            : $createNote->execute($this->title, $this->content);

        $note->tags()->sync($this->selectedTagIds);

        Flux::toast(text: $isNew ? __('Note created.') : __('Note updated.'), variant: 'success');

        $this->redirect(route('lw.notes.show', $note), navigate: true);
    }
};
?>

<form wire:submit="save" class="mt-6 space-y-6">
    <flux:field>
        <flux:label>{{ __('Title') }}</flux:label>
        <flux:input wire:model="title" required maxlength="255" autofocus />
        <flux:error name="title" />
    </flux:field>

    <flux:editor
        wire:model="content"
        :label="__('Content')"
        :description="__('Format your note with headings, bold text, and lists.')"
        toolbar="heading | bold italic strike | bullet ordered | blockquote | link"
    />
    <flux:error name="content" />

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
        <flux:button :href="route('lw.notes.index')" wire:navigate>{{ __('Cancel') }}</flux:button>
    </div>
</form>
