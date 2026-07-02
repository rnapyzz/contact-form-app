<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせを新規作成できる(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $data = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '08012345678',
            'address' => 'Tokyo',
            'category_id' => $category->id,
            'detail' => '内容詳細',
            'tag_ids' => $tags->pluck('id')->toArray(),
        ];

        // Act
        $response = $this->postJson('/api/v1/contacts', $data);

        // Assert
        $response->assertStatus(201)
            ->assertJsonPath('data.first_name', 'Test')
            ->assertJsonPath('data.category.id', $category->id);

        $this->assertCount(2, $response->json('data.tags'));
        $this->assertDatabaseHas('contacts', [
            'first_name' => 'Test',
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function 必須項目が入っていない場合は422エラーが返る(): void
    {
        // Act
        $response = $this->postJson('/api/v1/contacts', []);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name', 'last_name', 'gender', 'email', 'tel', 'address', 'category_id', 'detail',
            ]);
    }
}
