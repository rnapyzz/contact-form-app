<?php

namespace Tests\Unit\Request;

use App\Http\Requests\IndexContactRequest;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class IndexContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 有効なフィルター条件がすべてバリデーションを通過すること(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $request = new IndexContactRequest;
        $data = [
            'keyword' => 'Test User',
            'gender' => '1',
            'category_id' => $category->id,
            'date' => '2026-06-30',
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->passes(), '正しいフィルター条件が拒否されました');
    }

    /** @test */
    public function フィルターが空でもバリデーションを通過すること(): void
    {
        // Arrange
        $request = new IndexContactRequest;
        $data = [];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->passes(), '空のフィルター条件が拒否されました');
    }

    /** @test */
    public function 不正な性別値は拒否されること(): void
    {
        // Arrange
        $request = new IndexContactRequest;
        $data = [
            'gender' => '5',
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertFalse($validator->passes(), '不正な性別値が通過してしまいました');
        $this->assertArrayHasKey('gender', $validator->errors()->toArray());
    }
}
