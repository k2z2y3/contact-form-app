<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchContactRequest;
use App\Http\Requests\ExportContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;


class AdminController extends Controller
{
    public function export(ExportContactRequest $request): StreamedResponse
    {
        $validated = $request->validated();

        $query = Contact::with(['category', 'tags']);

        if (!empty($validated['keyword'] ?? null)) {
            $keyword = $validated['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if (isset($validated['gender']) && (int) $validated['gender'] !== 0) {
            $query->where('gender', (int) $validated['gender']);
        }

        if (!empty($validated['category_id'] ?? null)) {
            $query->where('category_id', $validated['category_id']);
        }

        if (!empty($validated['date'] ?? null)) {
            $query->whereDate('created_at', $validated['date']);
        }

        $contacts = $query->latest()->get();
        $fileName = 'contacts_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($contacts) {
            $handle = fopen('php://output', 'w');

            // Excelでの文字化け防止用 UTF-8 BOM
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID',
                '姓',
                '名',
                '性別',
                'メールアドレス',
                '電話番号',
                '住所',
                '建物名',
                'お問い合わせの種類',
                'タグ',
                'お問い合わせ内容',
                '作成日時',
            ]);

            foreach ($contacts as $contact) {
                $tagNames = $contact->tags->pluck('name')->implode(' / ');

                fputcsv($handle, [
                    $contact->id,
                    $contact->last_name,
                    $contact->first_name,
                    $contact->gender,
                    $contact->email,
                    $contact->tel,
                    $contact->address,
                    $contact->building,
                    optional($contact->category)->content,
                    $tagNames,
                    $contact->detail,
                    optional($contact->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
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
