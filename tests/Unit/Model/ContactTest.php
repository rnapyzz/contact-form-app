<?php

namespace Tests\Unit\Model;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせは特定の1つのカテゴリに属する(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $contact = Contact::factory()->create(['category_id' => $category->id]);

        // Assert
        $this->assertEquals($category->id, $contact->category->id, 'カテゴリの紐付けが不正です');
    }

    /** @test */
    public function お問い合わせは複数のタグと同期する(): void
    {
        // Arrange
        $contact = Contact::factory()->create();
        $tags = Tag::factory()->count(3)->create();
        $tagIds = $tags->pluck('id')->toArray();

        // Act
        $contact->tags()->sync($tagIds);

        // Assert
        $this->assertCount(3, $contact->tags);

        $attachedTagIds = $contact->tags->pluck('id')->toArray();
        $this->assertEqualsCanonicalizing($tagIds, $attachedTagIds, '紐づいているタグのIDが一致しません');
    }
}
