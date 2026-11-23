<?php

namespace Tests\Unit;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ContactStoreValidationTest extends TestCase
{
    use RefreshDatabase;

    private function validate(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $request = new StoreContactRequest();

        return Validator::make($data, $request->rules(), $request->messages());
    }

    private function validPayload(int $categoryId, array $tagIds = []): array
    {
        return [
            'last_name' => '山田',
            'first_name' => '太郎',
            'gender' => 1,
            'email' => 'taro@example.com',
            'tel' => '09012345678',
            'address' => '東京都大田区',
            'building' => 'メゾンサンプル101',
            'category_id' => $categoryId,
            'detail' => 'お問い合わせ内容のテストです。',
            'tag_ids' => $tagIds,
        ];
    }

    public function test_すべての必須項目とタグ入力を受け付けること(): void
    {
        $category = Category::create(['content' => 'テストカテゴリ']);
        $tag = Tag::create(['name' => 'テストタグ']);

        $validator = $this->validate(
            $this->validPayload($category->id, [$tag->id])
        );

        $this->assertFalse($validator->fails());
    }

    public function test_不正な電話番号形式を拒否すること(): void
    {
        $category = Category::create(['content' => 'テストカテゴリ']);

        $data = $this->validPayload($category->id);
        $data['tel'] = '090-1234-5678'; // ハイフンありは regex:/^[0-9]{10,11}$/ に違反

        $validator = $this->validate($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('tel', $validator->errors()->toArray());
    }
}/**
 * A basic unit test example.
 */


