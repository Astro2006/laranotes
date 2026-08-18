<?php

namespace App\NativeComponents;

use App\Models\Tags;
use Codebar\NativeCrudFormV2\NativeCrudFormV2;
use Illuminate\View\View;
use Native\Mobile\Attributes\On;
use Native\Mobile\Edge\NativeComponent;
use Native\Mobile\Events\NativeForm\ValueChanged;
use Native\Mobile\Facades\NativeForm;

class TagsIndex extends NativeComponent
{
    /** @var array<int, array{uuid: string, name: string, notesCount: int}> */
    public array $tags = [];

    public function mount(): void
    {
        $this->loadTags();
    }

    public function refresh(): void
    {
        $this->loadTags();
    }

    /**
     * Present the native bottom sheet to create a new tag.
     */
    public function create(): void
    {
        $this->tagForm()->presentCreate();
    }

    /**
     * Present the native bottom sheet pre-filled to edit the given tag.
     */
    public function edit(string $uuid): void
    {
        $tag = Tags::findByUuidOrFail($uuid);

        $this->tagForm()->presentEdit($tag);
    }

    public function delete(string $uuid): void
    {
        Tags::where('uuid', $uuid)->delete();

        $this->loadTags();
    }

    /**
     * Same pattern as NotesIndex::onNativeFormValueChanged — the checkmark's
     * ValueChanged event is delivered natively to this screen's own #[On]
     * handler rather than the plugin's global listener, so persistence is
     * handled here.
     */
    #[On(ValueChanged::class)]
    public function onNativeFormValueChanged(string $rowId, ?string $id = null, mixed $value = null): void
    {
        if ($id === null || ! str($id)->startsWith('crud:tags:')) {
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

        $this->loadTags();
    }

    public function render(): View
    {
        return view('native.tags-index');
    }

    private function tagForm(): NativeCrudFormV2
    {
        return NativeCrudFormV2::for(Tags::class)
            ->titles(create: 'New tag', edit: 'Edit tag')
            ->saveLabels(create: 'Create', edit: 'Save')
            ->deletable('Delete tag')
            ->field('name', 'text', 'Name');
    }

    /**
     * Delete the tag the "…" menu's Delete item was tapped for, tear down
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

        $this->loadTags();
    }

    private function loadTags(): void
    {
        $this->tags = Tags::withCount('notes')->orderBy('name')->get()
            ->map(fn (Tags $tag): array => [
                'uuid' => $tag->uuid,
                'name' => $tag->name,
                'notesCount' => $tag->notes_count,
            ])
            ->all();
    }
}
