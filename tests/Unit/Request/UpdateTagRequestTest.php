<?php

namespace Tests\Unit\Request;

use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateTagRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function タグ名が入力されていない場合は失敗する(): void
    {
        // Arrange
        $request = new UpdateTagRequest;
        $data = [];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /** @test */
    public function タグ名が50文字を超える場合は失敗する(): void
    {
        // Arrange
        $request = new UpdateTagRequest;
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
    public function 自分の名前のままの場合は重複エラーにならずに成功する(): void
    {
        // Arrange
        $tag = Tag::factory()->create(['name' => 'me']);
        $request = new UpdateTagRequest;

        $route = new Route('POST', '/tags/{tag}', []);
        $route->bind(new Request);
        $route->setParameter('tag', $tag->id);
        $request->setRouteResolver(fn () => $route);

        $data = [
            'name' => 'me',
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->passes(), '自分の名前なのに重複エラーとして拒否されました');
    }

    /** @test */
    public function 自分以外の既存の名前の場合は重複エラーで失敗する(): void
    {
        // Arrange
        Tag::factory()->create(['name' => 'other']);
        $tag = Tag::factory()->create(['name' => 'me']);
        $request = new UpdateTagRequest;

        $route = new Route('POST', '/tags/{tag}', []);
        $route->bind(new Request);
        $route->setParameter('tag', $tag->id);
        $request->setRouteResolver(fn () => $route);

        $data = [
            'name' => 'other',
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertFalse($validator->passes(), '他のタグ名と重複しているが通過しました');
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }
}
