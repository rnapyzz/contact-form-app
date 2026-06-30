<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;

class TagController extends Controller
{
    public function store(StoreTagRequest $request)
    {
        $validated = $request->validated();

        Tag::create([
            'name' => $validated['name'],
        ]);

        return redirect('/admin');
    }

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $validated = $request->validated();

        $tag->update([
            'name' => $validated['name'],
        ]);

        return redirect('/admin');
    }

    public function destroy(Tag $tag)
    {
        $tag->contacts()->detach();

        $tag->delete();

        return redirect('/admin');
    }
}
