<?php

namespace Tests\Unit;


use App\Http\Requests\StoreTagRequest; // 実際のクラス名に合わせて変更してください
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class TagValidationTest extends TestCase
{
    use RefreshDatabase;

    private function validateStore(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $request = new StoreTagRequest();

        return Validator::make($data, $request->rules(), $request->messages());
    }

    public function test_有効なタグ名を受け付けること(): void
    {
        $validator = $this->validateStore([
            'name' => str_repeat('あ', 50), // 50文字ちょうどはOK
        ]);

        $this->assertFalse($validator->fails());
    }

    public function test_タグ名が必須であること(): void
    {
        $validator = $this->validateStore([
            'name' => '',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_タグ名が50文字を超える場合は拒否されること(): void
    {
        $validator = $this->validateStore([
            'name' => str_repeat('あ', 51), // 51文字はNG
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_重複するタグ名は拒否されること(): void
    {
        Tag::create(['name' => '重要']);

        $validator = $this->validateStore([
            'name' => '重要',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }
}