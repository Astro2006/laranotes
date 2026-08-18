<?php

use App\Actions\Notes\CreateNote;
use App\Actions\Notes\UpdateNote;
use App\Models\Notes;
use App\Rules\NoteRules;
use Livewire\Component;

new class extends Component
{
    public ?Notes $note = null;

    public string $title = '';

    public string $content = '';

    public function mount(?Notes $note = null): void
    {
        $this->note = $note;
        $this->title = $note->title ?? '';
        $this->content = $note->content ?? '';
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

        $note = $this->note
            ? $updateNote->execute($this->note, $this->title, $this->content)
            : $createNote->execute($this->title, $this->content);

        $this->redirect(route('lw.notes.show', $note), navigate: true);
    }
};
?>

<form wire:submit="save" class="mt-6 space-y-6">
    <flux:field>
        <flux:label>Title</flux:label>
        <flux:input wire:model="title" required maxlength="255" />
        <flux:error name="title" />
    </flux:field>

    <flux:editor
        wire:model="content"
        label="Content"
        description="Format your note with headings, bold text, and lists."
        toolbar="heading | bold italic strike | bullet ordered | blockquote | link"
    />
    <flux:error name="content" />

    <div class="flex items-center gap-3">
        <flux:button type="submit" variant="primary">{{ $note ? 'Save changes' : 'Create note' }}</flux:button>
        <flux:button :href="route('lw.notes.index')" wire:navigate>Cancel</flux:button>
    </div>
</form>
