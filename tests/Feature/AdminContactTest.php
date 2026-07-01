<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactTest extends TestCase
{
    use RefreshDatabase;

    protected function setup(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    /** @test */
    public function 検索とページネーションが機能する(): void
    {
        // Arrange
        $contacts = Contact::factory()->count(8)->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
        ]);
        Contact::factory()->create(
            [
                'first_name' => 'Target',
                'last_name' => 'User',
                'email' => 'target@example.com',
            ]);

        // Act
        $response = $this->get('/admin?keyword=Target');

        // Assert
        $response->assertSee('Target');
        $response->assertDontSee('Test');
    }

    /** @test */
    public function お問い合わせ詳細が表示できる(): void
    {
        // Arrange
        $category = Category::factory()->create(['content' => 'Test Cat']);
        $contact = Contact::factory()->create(['category_id' => $category->id]);

        // Act
        $response = $this->get("/admin/contacts/{$contact->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Test Cat');
    }

    /** @test */
    public function お問い合わせが削除され一覧へリダイレクトされる(): void
    {
        // Arrange
        $contact = Contact::factory()->create();

        // Act
        $response = $this->delete("/admin/contacts/{$contact->id}");

        // Assert
        $response->assertRedirect('/admin');
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
