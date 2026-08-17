<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TagsController extends Controller
{
    /**
     * Display a listing of the tags.
     */
    public function index(): View
    {
        return view('tags.index', [
            'tags' => Tag::withCount('notes')->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new tag.
     */
    public function create(): View
    {
        return view('tags.create');
    }

    /**
     * Store a newly created tag in storage.
     */
    public function store(StoreTagRequest $request): RedirectResponse
    {
        Tag::create($request->validated());

        return redirect()->route('tags.index');
    }

    /**
     * Show the form for editing the specified tag.
     */
    public function edit(Tag $tag): View
    {
        return view('tags.edit', [
            'tag' => $tag,
        ]);
    }

    /**
     * Update the specified tag in storage.
     */
    public function update(UpdateTagRequest $request, Tag $tag): RedirectResponse
    {
        $tag->update($request->validated());

        return redirect()->route('tags.index');
    }

    /**
     * Remove the specified tag from storage.
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();

        return redirect()->route('tags.index');
    }
}
