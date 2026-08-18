<?php

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

    public function save(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255', new NoteRules],
            'content' => ['required', 'string', new NoteRules],
        ]);

        $isNew = ! $this->note;

        if ($this->note) {
            $this->note->update($validated);
        } else {
            $this->note = Notes::create($validated);
        }

        $this->note->tags()->sync($this->selectedTagIds);

        Flux::toast(text: $isNew ? __('Note created.') : __('Note updated.'), variant: 'success');

        $this->redirect(route('notes.show', $this->note), navigate: true);
    }
};
