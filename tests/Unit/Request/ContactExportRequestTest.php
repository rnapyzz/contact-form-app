<?php

namespace Tests\Unit\Request;

use App\Http\Requests\ExportContactRequest;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ContactExportRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 適切な検索条件でバリデーションは通過する(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $data = [
            'keyword' => 'Test User',
            'gender' => 1,
            'category_id' => $category->id,
            'date' => '2026-07-01',
        ];
        $request = new ExportContactRequest;

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function 不正な検索条件は失敗する(): void
    {
        // Arrange
        $data = [
            'gender' => 100,
            'category_id' => 1000,
            'date' => 'invalid date',
        ];
        $request = new ExportContactRequest;

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('gender', $validator->errors()->toArray());
        $this->assertArrayHasKey('category_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('date', $validator->errors()->toArray());
    }
}
