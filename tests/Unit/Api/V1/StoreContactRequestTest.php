<?php

namespace Tests\Unit\Api\V1;

use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 必須項目が欠けている場合にバリデーションエラーになる(): void
    {
        // Arrange
        $data = [];
        $request = new StoreContactRequest;

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('first_name', $validator->errors()->toArray());
        $this->assertArrayHasKey('last_name', $validator->errors()->toArray());
        $this->assertArrayHasKey('gender', $validator->errors()->toArray());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('address', $validator->errors()->toArray());
        $this->assertArrayHasKey('category_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('detail', $validator->errors()->toArray());
    }

    /** @test */
    public function 電話番号が10桁未満のときは失敗する(): void
    {
        // Arrange
        $data = ['tel' => '080123456'];
        $request = new StoreContactRequest;

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('tel', $validator->errors()->toArray());
    }

    /** @test */
    public function 電話番号が11桁超のときは失敗する(): void
    {
        // Arrange
        $data = ['tel' => '080123456789'];
        $request = new StoreContactRequest;

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('tel', $validator->errors()->toArray());
    }

    /** @test */
    public function 性別値が規定値以外のときは失敗する(): void
    {
        // Arrange
        $data = ['gender' => 4];
        $request = new StoreContactRequest;

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('gender', $validator->errors()->toArray());
    }

    /** @test */
    public function 必須項目と正しいタグが入力されていればバリデーションが通る(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();
        $tagIds = $tags->pluck('id')->toArray();

        $data = [
            'first_name' => 'test',
            'last_name' => 'user',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '08012345678',
            'address' => 'Tokyo',
            'category_id' => $category->id,
            'detail' => '詳細内容',
            'tag_ids' => $tagIds,
        ];

        // Act
        $request = new StoreContactRequest;
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertFalse(
            $validator->fails(),
            'バリデーションエラーが発生しました'.json_encode($validator->errors()->all())
        );
    }
}
