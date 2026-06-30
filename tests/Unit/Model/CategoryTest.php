<?php

namespace Tests\Unit\Model;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function カテゴリに紐づく複数のお問い合わせが取得できる(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $contacts = Contact::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        // Act
        $relatedContacts = $category->contacts;

        // Assert
        $this->assertCount(3, $relatedContacts);
        $this->assertInstanceOf(Contact::class, $relatedContacts->first());
        $this->assertEquals($contacts->pluck('id')->toArray(), $relatedContacts->pluck('id')->toArray());
    }
}
