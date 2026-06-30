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
            ->when($validated['keyword'] ?? null, function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('first_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('last_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('email', 'LIKE', "%{$keyword}%");
                });
            })
            ->when($validated['gender'] ?? null, function ($query, $gender) {
                $query->where('gender', $gender);
            })
            ->when($validated['category_id'] ?? null, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($validated['date'] ?? null, function ($query, $date) {
                $query->whereDate('created_at', $date);
            })
            ->paginate(7);

        return view('admin.index', compact('categories', 'tags', 'contacts'));
    }
}
