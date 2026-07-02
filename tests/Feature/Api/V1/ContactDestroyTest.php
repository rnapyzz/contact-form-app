<?php

namespace Feature\Api\V1;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactDestroyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせを削除できる(): void
    {
        // Arrange
        $contact = Contact::factory()->create();

        // Act
        $response = $this->deleteJson("/api/v1/contacts/{$contact->id}");

        // Assert
        $response->assertStatus(204);
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }

    /** @test */
    public function 存在しないお問い合わせの削除は404が返る(): void
    {
        // Act
        $response = $this->deleteJson('/api/v1/contacts/100');

        // Assert
        $response->assertStatus(404)
            ->assertJson(['error' => 'お問い合わせが見つかりませんでした。']);
    }
}
