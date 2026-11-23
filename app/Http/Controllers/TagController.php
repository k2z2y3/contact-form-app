<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;

class TagController extends Controller
{
    // タグ追加（POST /admin/tags）
    public function store(StoreTagRequest $request)
    {

        Tag::create($request->validated());

        return redirect('/admin')->with('status', 'タグを追加しました。');
    }

    // タグ編集画面（GET /admin/tags/{tag}/edit）
    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    // タグ更新（PUT /admin/tags/{tag}）
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());

        return redirect('/admin')->with('status', 'タグを更新しました。');
    }

    // タグ削除（DELETE /admin/tags/{tag}）
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect('/admin')->with('status', 'タグを削除しました。');
    }
}
