<?php


namespace Tests\Unit;

use App\Http\Requests\UpdateTagRequest; // 実際のクラス名に合わせて変更してください
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class TagValidationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Determine if the user is authorized to make this request.
     */

    private function validateUpdate(array $data, mixed $tagRouteParam): \Illuminate\Contracts\Validation\Validator
    {
        $request = new UpdateTagRequest();

        // ルートパラメータ `tag` を擬似的にセットする
        $route = new Route('PUT', 'tags/{tag}', []);
        $route->bind($request);
        $route->setParameter('tag', $tagRouteParam);
        $request->setRouteResolver(fn() => $route);

        return Validator::make($data, $request->rules(), $request->messages());
    }

    public function test_更新時に自分のタグ名のまま維持できること(): void
    {
        $tag = Tag::create(['name' => '重要']);
        Tag::create(['name' => '質問']);

        // モデルインスタンスを渡すパターン（ルートモデルバインディング想定）
        $validator = $this->validateUpdate(['name' => '重要'], $tag);

        $this->assertFalse($validator->fails());
    }

    public function test_更新時に他のタグで使われている名前には変更できないこと(): void
    {
        $tag = Tag::create(['name' => '重要']);
        Tag::create(['name' => '質問']);

        $validator = $this->validateUpdate(['name' => '質問'], $tag);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tag = $this->route('tag');
        $ignoreId = is_object($tag) ? $tag->id : $tag;

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tags', 'name')->ignore($ignoreId),
            ],
        ];
    }
}
