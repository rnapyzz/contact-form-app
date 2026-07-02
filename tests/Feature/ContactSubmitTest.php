<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactSubmitTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function バリデーション通過時は確認ページが表示される(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $data = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '00012345678',
            'address' => 'Tokyo',
            'category_id' => $category->id,
            'detail' => '詳細内容',
        ];

        // Act
        $response = $this->post('/contacts/confirm', $data);

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('contact.confirm');
        $response->assertSee('test@example.com');
    }

    /** @test */
    public function タグの設定がある場合でもバリデーションが通り確認ページが表示される(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();
        $tagIds = $tags->pluck('id')->toArray();
        $data = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '00012345678',
            'address' => 'Tokyo',
            'category_id' => $category->id,
            'detail' => '詳細内容',
            'tag_ids' => $tagIds,
        ];

        // Act
        $response = $this->post('/contacts/confirm', $data);

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('tags', function ($tag) use ($tags) {
            return $tag->count() === 2 && $tag->contains('id', $tags[0]->id);
        });
    }

    /** @test */
    public function バリデーションエラー時は元の入力画面にリダイレクトされる(): void
    {
        // Act
        $response = $this->post('/contacts/confirm', []);

        // Assert
        $response->assertSessionHasErrors(
            [
                'first_name',
                'last_name',
                'gender',
                'email',
                'tel',
                'address',
                'category_id',
                'detail',
            ]
        );
        $response->assertRedirect('/');
    }

    /** @test */
    public function 確認画面から送信成功すると_d_bに保存されサンクスページへ遷移する(): void
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
            'detail' => '詳細内容',
            'tag_ids' => $tags->pluck('id')->toArray(),
        ];

        // Act
        $response = $this->post('/contacts', $data);

        // Assert
        $response->assertRedirect('/thanks');
        $this->assertDatabaseHas('contacts', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
        ]);
        $this->assertDatabaseCount('contact_tag', 2);
    }
}
