<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Http\Request;


class AdminController extends Controller
{
    public function index(SearchContactRequest $request)
    {
        $validated = $request->validated();

        $categories = Category::all();
        $tags = Tag::all();

        $query = Contact::with(['category', 'tags']);
        // キーワード（名前・メールアドレス）
        if (!empty($validated['keyword'] ?? null)) {
            $keyword = $validated['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        // 性別（0は「すべて」想定で除外）
        if (!empty($validated['gender'] ?? null) && $validated['gender'] !== '0') {
            $query->where('gender', $validated['gender']);
        }

        // お問い合わせの種類
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // 日付
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        $contacts = $query->paginate(7)->appends($request->query());

        return view('admin.index', compact('categories', 'contacts', 'tags'));
    }

    // お問い合わせ詳細表示（GET /admin/contacts/{contact}）
    public function show(Contact $contact)
    {
        $contact->load(['category', 'tags']);

        return view('admin.show', compact('contact'));
    }

    // お問い合わせ削除（DELETE /admin/contacts/{contact}）
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect('/admin')->with('status', 'お問い合わせを削除しました。');
    }
}
