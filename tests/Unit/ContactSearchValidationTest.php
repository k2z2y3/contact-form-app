<?php

namespace Tests\Unit;

use App\Http\Requests\SearchContactRequest;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ContactSearchValidationTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    use RefreshDatabase;

    private function validate(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $request = new SearchContactRequest();
        $rules = $request->rules();

        return Validator::make($data, $request->rules(), $request->messages());
    }

    /** @test */

    public function 有効な検索条件を受け付けること(): void
    {
        $category = Category::create([
            'content' => 'テストカテゴリ',
        ]);

        $validator = $this->validate([
            'keyword' => '山田',
            'gender' => 1,
            'category_id' => $category->id,
            'date' => '2026-11-01',
        ]);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function 不正な性別値を拒否すること(): void
    {
        $validator = $this->validate([
            'gender' => 999,
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('gender', $validator->errors()->toArray());
    }
}

