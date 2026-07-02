<?php

namespace Tests\Feature\Api\V1;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせ一覧を取得できる(): void
    {
        // Arrange
        Contact::factory()->count(10)->create();

        // Act
        $response = $this->getJson('/api/v1/contacts');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'first_name', 'last_name', 'email', 'tel', 'gender', 'category', 'tags'],
                ],
                'meta',
                'links',
            ]);
    }

    /** @test */
    public function 検索フィルタが正常に機能する(): void
    {
        // Arrange
        $contact = Contact::factory()->create([
            'first_name' => 'Test',
        ]);
        Contact::factory()->create([
            'first_name' => 'Other',
        ]);

        // Act
        $response = $this->getJson('/api/v1/contacts?keyword=Test');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.first_name', 'Test');
    }

    /** @test */
    public function 一覧取得でページネーションが機能する(): void
    {
        // Arrange
        Contact::factory()->count(15)->create();

        // Act
        $responseP1 = $this->getJson('/api/v1/contacts?per_page=10&page=1');
        $responseP2 = $this->getJson('/api/v1/contacts?per_page=10&page=2');

        // Assert
        $responseP1->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.last_page', 2);

        $responseP2->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.current_page', 2);
    }

    /** @test */
    public function 検索条件で性別が不正な場合に422エラーが返る(): void
    {
        // Act
        $response = $this->getJson('/api/v1/contacts?gender=0');

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['gender']);
    }

    /** @test */
    public function 検索条件で日付形式が不正な場合に422エラーが返る(): void
    {
        // Act
        $response = $this->getJson('/api/v1/contacts?date=invalid-date');

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date']);
    }

    /** @test */
    public function 検索条件でカテゴリーが不正な場合に422エラーが返る(): void
    {
        // Act
        $response = $this->getJson('/api/v1/contacts?category_id=100');

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category_id']);
    }
}
