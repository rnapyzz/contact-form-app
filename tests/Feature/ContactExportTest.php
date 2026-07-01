<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    /** @test */
    public function フィルタ条件なしでエクスポートすると全件が新着順で取得できる(): void
    {
        // Arrange
        $oldContact = Contact::factory()->create([
            'created_at' => '2026-06-01 10:00:00',
        ]);
        $newContact = Contact::factory()->create([
            'created_at' => '2026-07-01 10:00:00',
        ]);

        // Act
        $response = $this->get(route('contacts.export'));

        // Assert
        $response->assertStatus(200);

        $csvContent = $response->streamedContent();
        $this->assertLessThan(
            strpos($csvContent, $oldContact->email),
            strpos($csvContent, $newContact->email),
        );
    }

    /** @test */
    public function フィルタ条件を指定して表示してからエクスポートすると条件に一致したデータのみ取得できる(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $target = Contact::factory()->create([
            'gender' => 1,
            'category_id' => $category->id,
        ]);
        $other = Contact::factory()->create([
            'gender' => 2,
            'category_id' => $category->id,
        ]);

        // Act
        $response = $this->get(route('contacts.export', ['gender' => 1]));
        $csvContent = $response->streamedContent();

        // Assert
        $response->assertStatus(200);
        $this->assertStringContainsString($target->email, $csvContent);
        $this->assertStringNotContainsString($other->email, $csvContent);
    }
}
