<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotesRequest;
use App\Http\Requests\UpdateNotesRequest;
use App\Models\Notes;
use App\Models\Tags;
use Codebar\NativeCrudFormV2\NativeCrudFormV2;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotesController extends Controller
{
    /**
     * Display a paginated listing of the notes.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value() ?: null;

        return view('notes.index', [
            'notes' => Notes::search($search)->with('tags')->latest()->paginate(15)->withQueryString(),
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new note.
     */
    public function create(): View
    {
        return view('notes.create');
    }

    /**
     * Present the native create-note popup.
     */
    public function presentNativeCreate(): RedirectResponse
    {
        $this->noteForm()->presentCreate();

        return redirect()->route('notes.index');
    }

    /**
     * Present the native edit-note popup.
     */
    public function presentNativeEdit(Notes $note): RedirectResponse
    {
        $this->noteForm()->presentEdit($note->load('tags'));

        return redirect()->route('notes.index');
    }

    /**
     * Build the native CRUD form definition for notes.
     */
    protected function noteForm(): NativeCrudFormV2
    {
        return NativeCrudFormV2::for(Notes::class)
            ->titles(create: 'New note', edit: 'Edit note')
            ->saveLabels(create: 'Create', edit: 'Save')
            ->field('title', 'text', 'Title')
            ->field('content', 'textarea', 'Content')
            ->relation('tags', Tags::class, 'name', 'Tags');
    }

    /**
     * Store a newly created note in storage.
     */
    public function store(StoreNotesRequest $request): RedirectResponse
    {
        $note = Notes::create($request->safe()->except('tags'));

        $note->tags()->sync(Tags::fromNameList($request->validated('tags'))->pluck('id'));

        session()->flash('toast', ['text' => 'Note created.', 'variant' => 'success']);

        return redirect()->route('notes.show', $note);
    }

    /**
     * Display the specified note.
     */
    public function show(Notes $note): View
    {
        return view('notes.show', [
            'note' => $note->load('tags'),
        ]);
    }

    /**
     * Show the form for editing the specified note.
     */
    public function edit(Notes $note): View
    {
        return view('notes.edit', [
            'note' => $note->load('tags'),
        ]);
    }

    /**
     * Update the specified note in storage.
     */
    public function update(UpdateNotesRequest $request, Notes $note): RedirectResponse
    {
        $note->update($request->safe()->except('tags'));

        if ($request->has('tags')) {
            $note->tags()->sync(Tags::fromNameList($request->validated('tags'))->pluck('id'));
        }

        session()->flash('toast', ['text' => 'Note updated.', 'variant' => 'success']);

        return redirect()->route('notes.show', $note);
    }

    /**
     * Remove the specified note from storage.
     */
    public function destroy(Notes $note): RedirectResponse
    {
        $note->delete();

        session()->flash('toast', ['text' => 'Note deleted.', 'variant' => 'danger']);

        return redirect()->route('notes.index');
    }
}
