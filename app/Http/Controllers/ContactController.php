<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportContactRequest;
use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('contact.index', compact('categories', 'tags'));
    }

    public function confirm(StoreContactRequest $request)
    {
        $validated = $request->validated();

        $category = Category::find($validated['category_id']);

        $tags = collect();
        if (! empty($validated['tag_ids'])) {
            $tags = Tag::whereIn('id', $validated['tag_ids'])->get();
        }

        return view('contact.confirm', [
            'validated' => $validated,
            'category' => $category,
            'tags' => $tags,
        ]);
    }

    public function store(StoreContactRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $contact = Contact::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'gender' => $validated['gender'],
                'email' => $validated['email'],
                'tel' => $validated['tel'],
                'address' => $validated['address'],
                'building' => $validated['building'] ?? null,
                'category_id' => $validated['category_id'],
                'detail' => $validated['detail'],
            ]);

            if (! empty($validated['tag_ids'])) {
                $contact->tags()->sync($validated['tag_ids']);
            }
        });

        return redirect()->route('contact.thanks');
    }

    public function thanks()
    {
        return view('contact.thanks');
    }

    public function export(ExportContactRequest $request): StreamedResponse
    {
        $filters = $request->validated();

        $query = Contact::query()->with('category')->search($filters);
        $hasFilters = collect($filters)->filter()->isNotEmpty();

        if (! $hasFilters) {
            $query->latest();
        }

        return response()->streamDownload(function () use ($query) {

            $stream = fopen('php://output', 'w');

            fprintf($stream, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($stream, [
                'ID', '氏名', '性別', 'メール', '電話',
                '住所', '建物', 'カテゴリ', '内容', '作成日時',
            ]);

            $query->each(function ($contact) use ($stream) {
                fputcsv($stream, [
                    $contact->id,
                    $contact->last_name.' '.$contact->first_name,
                    $this->getGenderLabel($contact->gender),
                    $contact->email,
                    $contact->tel,
                    $contact->address,
                    $contact->building ?? '',
                    $contact->category->content ?? '',
                    $contact->detail,
                    $contact->created_at,
                ]);
            });

            fclose($stream);
        }, 'contacts_export_'.date('YmdHis').'.csv');
    }

    private function getGenderLabel(int $gender): string
    {
        return [
            0 => '未選択',
            1 => '男性',
            2 => '女性',
            3 => 'その他',
        ][$gender] ?? '不明';
    }
}
