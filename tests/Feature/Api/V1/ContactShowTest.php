<?php

namespace Tests\Feature\Api\V1;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 既存の_i_dのお問い合わせ詳細を取得できる(): void
    {
        // Arrange
        $contact = Contact::factory()->hasTags(2)->create();

        // Act
        $response = $this->getJson("/api/v1/contacts/{$contact->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonPath('data.id', $contact->id)
            ->assertJsonStructure([
                'data' => ['id', 'first_name', 'last_name', 'email', 'category', 'tags'],
            ]);
    }

    /** @test */
    public function 存在しない_i_dを指定した場合は404エラーが返る(): void
    {
        // Act
        $response = $this->getJson('/api/v1/contacts/100');

        // Assert
        $response->assertStatus(404)
            ->assertJson([
                'error' => 'お問い合わせが見つかりませんでした。',
            ]);
    }
}
