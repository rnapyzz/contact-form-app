<?php

namespace Tests\Unit\Api\V1;

use App\Http\Requests\Api\V1\IndexContactRequest;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class IndexContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しい検索条件が渡された場合には成功する(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $data = [
            'keyword' => 'Test',
            'gender' => 1,
            'category_id' => $category->id,
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-31',
            'per_page' => 10,
        ];

        // Act
        $request = new IndexContactRequest;
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertFalse(
            $validator->fails(),
            'バリデーションエラーが発生しました: '.json_encode($validator->errors()->all())
        );
    }

    /** @test */
    public function 検索条件に不正な値が渡された場合には失敗する(): void
    {
        // Arrange
        $data = [
            'gender' => 100,
            'category_id' => 100,
            'date' => 'invalid-date',
        ];
        $request = new IndexContactRequest;

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('gender', $validator->errors()->toArray());
        $this->assertArrayHasKey('category_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('date', $validator->errors()->toArray());
    }
}
