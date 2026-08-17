<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotesRequest;
use App\Http\Requests\UpdateNotesRequest;
use App\Models\Notes;
use App\Models\Tags;
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
     * Store a newly created note in storage.
     */
    public function store(StoreNotesRequest $request): RedirectResponse
    {
        $note = Notes::create($request->safe()->except('tags'));

        $note->tags()->sync(Tags::fromNameList($request->validated('tags'))->pluck('id'));

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

        return redirect()->route('notes.show', $note);
    }

    /**
     * Remove the specified note from storage.
     */
    public function destroy(Notes $note): RedirectResponse
    {
        $note->delete();

        return redirect()->route('notes.index');
    }
}
