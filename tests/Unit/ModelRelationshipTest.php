<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipTest extends TestCase
{
    use RefreshDatabase;

    private function createContact(Category $category, string $detail = 'お問い合わせテスト'): Contact
    {
        $contact = new Contact([
            'last_name' => '山田',
            'first_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都大田区',
            'building' => 'サンプルハイツ101',
            'detail' => $detail,
        ]);
        $contact->category_id = $category->id;
        $contact->save();

        return $contact;
    }

    public function test_カテゴリが複数の問い合わせを持てること(): void
    {
        $category = Category::create(['content' => '商品について']);
        $contact1 = $this->createContact($category, '1件目のお問い合わせ');
        $contact2 = $this->createContact($category, '2件目のお問い合わせ');

        $this->assertCount(2, $category->contacts);
        $this->assertTrue($category->contacts->contains($contact1));
        $this->assertTrue($category->contacts->contains($contact2));
    }

    public function test_問い合わせがカテゴリに属し複数のタグを関連付けられること(): void
    {
        $category = Category::create(['content' => 'サポート']);
        $contact = $this->createContact($category);

        $tag1 = Tag::create(['name' => '重要']);
        $tag2 = Tag::create(['name' => '至急']);

        $contact->tags()->sync([$tag1->id, $tag2->id]);

        $this->assertTrue($contact->category->is($category));
        $this->assertCount(2, $contact->tags);
        $this->assertTrue($contact->tags->contains($tag1));
        $this->assertTrue($contact->tags->contains($tag2));
    }

    public function test_タグが中間テーブル経由でお問い合わせと関連付けられること(): void
    {
        $category = Category::create(['content' => 'その他']);
        $contact1 = $this->createContact($category, '問い合わせA');
        $contact2 = $this->createContact($category, '問い合わせB');

        $tag = Tag::create(['name' => '確認済み']);
        $tag->contacts()->attach([$contact1->id, $contact2->id]);

        $this->assertCount(2, $tag->contacts);
        $this->assertTrue($tag->contacts->contains($contact1));
        $this->assertTrue($tag->contacts->contains($contact2));
    }
}