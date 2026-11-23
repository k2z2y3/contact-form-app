<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_contacts(): void
    {
        $category = Category::create([
            'content' => 'テストカテゴリ',
        ]);

        Contact::create([
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => 'テスト',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都江戸川区東葛西1-1-1',
            'building' => 'テストハイツ101',
            'detail' => '一覧テスト用のお問い合わせです',
        ]);

        $response = $this->getJson('/api/v1/contacts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'category_id',
                        'first_name',
                        'last_name',
                        'gender',
                        'email',
                        'tel',
                        'address',
                        'building',
                        'detail',
                        'category',
                        'tags',
                    ],
                ],
            ]);
    }
    public function test_can_create_contact(): void
    {
        $category = Category::create([
            'content' => 'テストカテゴリ',
        ]);

        $payload = [
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => 'テスト',
            'gender' => 1,
            'email' => 'test-create@example.com',
            'tel' => '09012345678',
            'address' => '東京都江戸川区東葛西1-1-1',
            'building' => 'テストハイツ101',
            'detail' => '作成テスト用のお問い合わせです',
            'tags' => [],
        ];

        $response = $this->postJson('/api/v1/contacts', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.email', 'test-create@example.com')
            ->assertJsonPath('data.detail', '作成テスト用のお問い合わせです');

        $this->assertDatabaseHas('contacts', [
            'email' => 'test-create@example.com',
            'detail' => '作成テスト用のお問い合わせです',
        ]);
    }
    public function test_can_show_contact(): void
    {
        $category = Category::create([
            'content' => 'テストカテゴリ',
        ]);

        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '花子',
            'last_name' => 'テスト',
            'gender' => 2,
            'email' => 'hanako@example.com',
            'tel' => '08011112222',
            'address' => '東京都江戸川区東葛西2-2-2',
            'building' => 'サンプルマンション202',
            'detail' => '詳細取得テスト用です',
        ]);

        $response = $this->getJson("/api/v1/contacts/{$contact->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $contact->id)
            ->assertJsonPath('data.email', 'hanako@example.com');
    }

    public function test_returns_404_when_contact_not_found(): void
    {
        $response = $this->getJson('/api/v1/contacts/999999');

        $response->assertStatus(404);
    }
    public function test_can_update_contact(): void
    {
        $category = Category::create([
            'content' => 'テストカテゴリ',
        ]);

        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => 'テスト',
            'gender' => 1,
            'email' => 'before@example.com',
            'tel' => '09012345678',
            'address' => '東京都江戸川区東葛西1-1-1',
            'building' => 'テストハイツ101',
            'detail' => '更新前の内容です',
        ]);

        $response = $this->patchJson("/api/v1/contacts/{$contact->id}", [
            'detail' => '更新後の内容に変わりました',
            'tags' => [],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $contact->id)
            ->assertJsonPath('data.detail', '更新後の内容に変わりました');

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'detail' => '更新後の内容に変わりました',
        ]);
    }
    public function test_can_delete_contact(): void
    {
        $category = Category::create([
            'content' => 'テストカテゴリ',
        ]);

        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => 'テスト',
            'gender' => 1,
            'email' => 'delete@example.com',
            'tel' => '09012345678',
            'address' => '東京都江戸川区東葛西1-1-1',
            'building' => 'テストハイツ101',
            'detail' => '削除テスト用です',
        ]);

        $response = $this->deleteJson("/api/v1/contacts/{$contact->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }
    public function test_create_contact_validation_error(): void
    {
        $response = $this->postJson('/api/v1/contacts', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'category_id',
                'first_name',
                'last_name',
                'gender',
                'email',
                'tel',
                'address',
                'detail',
            ]);
    }
    public function test_create_contact_invalid_data_returns_422(): void
    {
        $response = $this->postJson('/api/v1/contacts', [
            'category_id' => 999999,
            'first_name' => '太郎',
            'last_name' => 'テスト',
            'gender' => 1,
            'email' => 'not-an-email',
            'tel' => '09012345678',
            'address' => '東京都江戸川区東葛西1-1-1',
            'building' => 'テストハイツ101',
            'detail' => '不正値テスト用です',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'category_id',
                'email',
            ]);
    }
}