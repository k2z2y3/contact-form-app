<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    //
    public function index(Request $request)
    {
        $categories = Category::all();
        $tags = Tag::all();

        $inputs = $request->all();

        return view('contact.index', compact('categories', 'tags'));
    }

    public function confirm(StoreContactRequest $request)
    {
        $validated = $request->validated();

        $genderLabels = [
            1 => '男性',
            2 => '女性',
            3 => 'その他',
        ];
        $genderLabel = $genderLabels[(int) $validated['gender']] ?? '';

        $category = Category::find($validated['category_id']);
        $categoryName = $category?->content ?? '';

        $tagIds = $validated['tag_ids'] ?? [];
        $selectedTags = Tag::whereIn('id', $tagIds)->get();

        return view('contact.confirm', [
            'validated' => $validated,
            'inputs' => $validated,
            'genderLabel' => $genderLabel,
            'category' => $category,
            'categoryName' => $categoryName,
            'tags' => $selectedTags,
            'selectedTags' => $selectedTags,
        ]);
    }

    /**
     * お問い合わせ送信・保存処理（POST /contacts）
     */
    public function store(StoreContactRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $contact = Contact::create([
                'category_id' => $validated['category_id'],
                'last_name' => $validated['last_name'],
                'first_name' => $validated['first_name'],
                'gender' => $validated['gender'],
                'email' => $validated['email'],
                'tel' => $validated['tel'],
                'address' => $validated['address'],
                'building' => $validated['building'] ?? null,
                'detail' => $validated['detail'],
            ]);

            if (! empty($validated['tag_ids'])) {
                $contact->tags()->sync($validated['tag_ids']);
            }
        });

        return redirect('/thanks');
    }

    /**
     * サンクスページ（GET /thanks）
     */
    public function thanks()
    {
        return view('contact.thanks');
    }
}
