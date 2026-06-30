<?php

namespace Tests\Unit\Request;

use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreTagRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 有効なタグ名が指定された場合は成功する(): void
    {
        // Arrange
        $request = new StoreTagRequest;
        $data = [
            'name' => 'Test',
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->passes(), '正しいタグ名が拒否されました');
    }

    /** @test */
    public function タグ名が入力されていない場合は失敗する(): void
    {
        // Arrange
        $request = new StoreTagRequest;
        $data = [];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /** @test */
    public function タグ名が50文字を超えると失敗する(): void
    {
        // Arrange
        $request = new StoreTagRequest;
        $data = [
            'name' => str_repeat('a', 51),
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /** @test */
    public function 既に存在するタグ名は重複エラーで失敗する(): void
    {
        // Arrange
        Tag::factory()->create(['name' => 'ExistsTag']);

        $request = new StoreTagRequest;
        $data = [
            'name' => 'ExistsTag',
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }
}
