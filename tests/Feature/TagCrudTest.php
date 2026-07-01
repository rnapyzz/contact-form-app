<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    /** @test */
    public function タグの作成ができる(): void
    {
        // Act
        $response = $this->post('/admin/tags', ['name' => 'test']);

        // Assert
        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', ['name' => 'test']);
    }

    /** @test */
    public function タグの編集画面が表示できる(): void
    {
        // Arrange
        $tag = Tag::factory()->create();

        // Act
        $response = $this->get("/admin/tags/{$tag->id}/edit");

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function タグの更新ができる(): void
    {
        // Arrange
        $tag = Tag::factory()->create(['name' => 'old tag']);

        // Act
        $response = $this->put("/admin/tags/{$tag->id}", ['name' => 'new tag']);

        // Assert
        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'new tag',
        ]);
    }

    /** @test */
    public function タグの削除ができる(): void
    {
        // Arrange
        $tag = Tag::factory()->create();

        // Act
        $response = $this->delete("/admin/tags/{$tag->id}");

        // Assert
        $response->assertRedirect('/admin');
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);

    }
}
