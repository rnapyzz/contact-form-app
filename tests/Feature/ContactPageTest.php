<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせ入力フォームページが表示されカテゴリとタグが渡される(): void
    {
        // Arrange
        $categories = Category::factory()->count(3)->create();
        $tags = Tag::factory()->count(3)->create();

        // Act
        $response = $this->get('/');

        // Assert
        $response->assertStatus(200);

        $response->assertViewHas('categories');
        $response->assertViewHas('tags');

        $response->assertSee($categories->first()->name);
        $response->assertSee($tags->first()->name);
    }

    /** @test */
    public function サンクスページが正常に表示される(): void
    {
        // Act
        $response = $this->get('/thanks');

        // Assert
        $response->assertStatus(200);
    }
}
