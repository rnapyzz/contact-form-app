<?php

namespace Tests\Unit\Request;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 全ての必須項目とタグ入力が含まれる場合にバリデーションを通過する(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(3)->create();
        $tagIds = $tags->pluck('id')->toArray();

        $request = new StoreContactRequest;
        $data = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'gender' => 1,
            'email' => 'test_user@example.com',
            'tel' => '08012345678',
            'address' => 'Tokyo',
            'building' => 'Tower',
            'category_id' => $category->id,
            'detail' => '120文字以内の内容',
            'tag_ids' => $tagIds,
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->passes(), '正しい入力が拒否されました');
    }

    /** @test */
    public function 必須項目が入力されていない場合は失敗する(): void
    {
        // Arrange
        $request = new StoreContactRequest;
        $data = [];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('first_name', $validator->errors()->toArray());
        $this->assertArrayHasKey('last_name', $validator->errors()->toArray());
        $this->assertArrayHasKey('gender', $validator->errors()->toArray());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('tel', $validator->errors()->toArray());
        $this->assertArrayHasKey('address', $validator->errors()->toArray());
        $this->assertArrayHasKey('category_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('detail', $validator->errors()->toArray());
    }

    /** @test */
    public function 不正な電話番号形式では失敗する(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $request = new StoreContactRequest;
        $data = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'gender' => 1,
            'email' => 'test_user@example.com',
            'tel' => '080-1234-5678',
            'address' => 'Tokyo',
            'category_id' => $category->id,
            'detail' => '120文字以内の内容',
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('tel', $validator->errors()->toArray());
    }

    /** @test */
    public function 詳細内容が120文字を超えると失敗する(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $request = new StoreContactRequest;
        $data = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'gender' => 1,
            'email' => 'test_user@example.com',
            'tel' => '08012345678',
            'address' => 'Tokyo',
            'category_id' => $category->id,
            'detail' => str_repeat('a', 121),
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('detail', $validator->errors()->toArray());
    }

    /** @test */
    public function 存在しないタグ_i_dが含まれる場合は失敗する(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $request = new StoreContactRequest;
        $data = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'gender' => 1,
            'email' => 'test_user@example.com',
            'tel' => '08012345678',
            'address' => 'Tokyo',
            'category_id' => $category->id,
            'detail' => '120文字以内の内容',
            'tag_ids' => [900, 1000],
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('tag_ids.0', $validator->errors()->toArray());
    }
}
