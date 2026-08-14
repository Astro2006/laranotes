<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotesRequest;
use App\Http\Requests\UpdateNotesRequest;
use App\Models\Notes;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class NotesController extends Controller
{
    /**
     * Display a paginated listing of the notes.
     */
    public function index(): View
    {
        return view('notes.index', [
            'notes' => Notes::latest()->paginate(15),
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
        $note = Notes::create($request->validated());

        return redirect()->route('notes.show', $note);
    }

    /**
     * Display the specified note.
     */
    public function show(Notes $note): View
    {
        return view('notes.show', [
            'note' => $note,
        ]);
    }

    /**
     * Show the form for editing the specified note.
     */
    public function edit(Notes $note): View
    {
        return view('notes.edit', [
            'note' => $note,
        ]);
    }

    /**
     * Update the specified note in storage.
     */
    public function update(UpdateNotesRequest $request, Notes $note): RedirectResponse
    {
        $note->update($request->validated());

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
