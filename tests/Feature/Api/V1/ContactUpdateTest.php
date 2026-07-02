<?php

namespace Tests\Feature\Api\V1;

use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 既存のお問い合わせを更新できる(): void
    {
        // Arrange
        $tags = Tag::factory()->count(2)->create();
        $contact = Contact::factory()->create([
            'gender' => 1,
        ]);
        $contact->tags()->attach($tags->pluck('id'));
        $newTag = Tag::factory()->create();

        $data = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'gender' => 2,
            'email' => 'updated@example.com',
            'address' => 'Tokyo',
            'tel' => '08012345678',
            'category_id' => $contact->category_id,
            'detail' => '更新された内容',
            'tag_ids' => [$newTag->id],
        ];

        // Act
        $response = $this->putJson("/api/v1/contacts/{$contact->id}", $data);

        // Assert
        $response->assertStatus(200)
            ->assertJsonPath('data.first_name', 'Test');

        $this->assertCount(1, $response->json('data.tags'));
        $this->assertEquals($newTag->id, $response->json('data.tags.0.id'));
    }

    /** @test */
    public function 必須項目がない場合は失敗する(): void
    {
        // Arrange
        $contact = Contact::factory()->create();

        // Act
        $response = $this->putJson("/api/v1/contacts/{$contact->id}", []);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name', 'last_name', 'gender', 'email', 'tel', 'address', 'category_id', 'detail',
            ]);
    }

    /** @test */
    public function 存在しないお問い合わせの場合は失敗する(): void
    {
        // Arrange
        $contact = Contact::factory()->create();
        $data = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '08012345678',
            'address' => 'Tokyo',
            'category_id' => $contact->category_id,
            'detail' => '詳細',
        ];

        // Act
        $response = $this->putJson('/api/v1/contacts/100', $data);

        // Assert
        $response->assertStatus(404)
            ->assertJson(['error' => 'お問い合わせが見つかりませんでした。']);

    }
}
