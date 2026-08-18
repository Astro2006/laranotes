<?php

namespace App\NativeComponents;

use App\Models\Notes;
use App\Models\Tags;
use Codebar\NativeCrudFormV2\NativeCrudFormV2;
use Illuminate\View\View;
use Native\Mobile\Attributes\On;
use Native\Mobile\Edge\NativeComponent;
use Native\Mobile\Events\NativeForm\ValueChanged;
use Native\Mobile\Facades\NativeForm;

class NotesIndex extends NativeComponent
{
    /** @var array<int, array{uuid: string, title: string, content: string, tags: string}> */
    public array $notes = [];

    public function mount(): void
    {
        $this->loadNotes();
    }

    public function refresh(): void
    {
        $this->loadNotes();
    }

    /**
     * Present the native bottom sheet to create a new note.
     */
    public function create(): void
    {
        $this->noteForm()->presentCreate();
    }

    /**
     * Present the native bottom sheet pre-filled to edit the given note.
     */
    public function edit(string $uuid): void
    {
        $note = Notes::findByUuidOrFail($uuid)->load('tags');

        $this->noteForm()->presentEdit($note);
    }

    public function delete(string $uuid): void
    {
        Notes::where('uuid', $uuid)->delete();

        $this->loadNotes();
    }

    /**
     * The plugin's PersistNativeCrudFormV2 listener is registered via
     * Event::listen(), but the checkmark's ValueChanged event is delivered
     * as a native event (only to the active screen's own #[On] handlers) —
     * it never reaches that global listener, so the plugin's normal
     * persistence never runs. Do it here instead, the same way
     * PersistNativeCrudFormV2 does.
     */
    #[On(ValueChanged::class)]
    public function onNativeFormValueChanged(string $rowId, ?string $id = null, mixed $value = null): void
    {
        if ($id === null || ! str($id)->startsWith('crud:notes:')) {
            return;
        }

        if ($rowId === NativeCrudFormV2::DELETE_ROW_ID) {
            $this->deleteFromSheet($id);

            return;
        }

        if ($rowId !== 'save') {
            NativeCrudFormV2::remember($id, $rowId, $value);

            return;
        }

        $meta = NativeCrudFormV2::meta($id);

        if ($meta === null) {
            return;
        }

        $values = NativeCrudFormV2::values($id, $meta);

        if (! NativeCrudFormV2::isComplete($meta, $values)) {
            return;
        }

        NativeCrudFormV2::persist($meta, $values);
        NativeCrudFormV2::forgetByMeta($id, $meta);

        NativeForm::dismiss();

        $this->loadNotes();
    }

    public function render(): View
    {
        return view('native.notes-index');
    }

    private function noteForm(): NativeCrudFormV2
    {
        return NativeCrudFormV2::for(Notes::class)
            ->titles(create: 'New note', edit: 'Edit note')
            ->saveLabels(create: 'Create', edit: 'Save')
            ->deletable('Delete note')
            ->field('title', 'text', 'Title')
            ->field('content', 'textarea', 'Content')
            ->relation('tags', Tags::class, 'name', 'Tags');
    }

    /**
     * Delete the note the "…" menu's Delete item was tapped for, tear down
     * the sheet's cached field state, and dismiss it.
     */
    private function deleteFromSheet(string $handle): void
    {
        $meta = NativeCrudFormV2::meta($handle);

        if ($meta === null || $meta['key'] === null) {
            return;
        }

        $meta['model']::destroy($meta['key']);

        NativeCrudFormV2::forgetByMeta($handle, $meta);
        NativeForm::dismiss();

        $this->loadNotes();
    }

    private function loadNotes(): void
    {
        $this->notes = Notes::with('tags')->latest()->get()
            ->map(fn (Notes $note): array => [
                'uuid' => $note->uuid,
                'title' => $note->title,
                'content' => $note->content,
                'tags' => $note->tags->pluck('name')->implode(', '),
            ])
            ->all();
    }
}
