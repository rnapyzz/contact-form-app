<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;

class AdminController extends Controller
{
    public function index(IndexContactRequest $request)
    {
        $validated = $request->validated();

        $categories = Category::all();
        $tags = Tag::all();

        $contacts = Contact::with(['category', 'tags'])
            ->search($validated)
            ->paginate(7);

        return view('admin.index', compact('categories', 'tags', 'contacts'));
    }

    public function show(Contact $contact)
    {
        $contact->load(['category', 'tags']);

        return view('admin.show', compact('contact'));
    }

    public function destroy(Contact $contact)
    {
        $contact->tags()->detach();

        $contact->delete();

        return redirect('/admin');
    }
}
