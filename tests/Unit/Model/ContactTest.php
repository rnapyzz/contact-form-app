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

    /** @test */
    public function scope_searchで正しくフィルターができる(): void
    {
        // Arrange
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        Contact::factory()->create([
            'gender' => 1,
            'created_at' => '2026-07-01 10:00:00',
            'category_id' => $category1->id,
        ]);
        Contact::factory()->create([
            'gender' => 2,
            'created_at' => '2026-06-01 10:00:00',
            'category_id' => $category2->id,
        ]);

        // Act
        $resultGender = Contact::search(['gender' => 1])->get();
        $resultDate = Contact::search(['date' => '2026-07-01'])->get();
        $resultCategory = Contact::search(['category_id' => $category1->id])->get();

        // Assert
        $this->assertCount(1, $resultGender);
        $this->assertEquals(1, $resultGender->first()->gender);

        $this->assertCount(1, $resultDate);
        $this->assertEquals('2026-07-01 10:00:00', $resultDate->first()->created_at);

        $this->assertCount(1, $resultCategory);
        $this->assertEquals($category1->id, $resultCategory->first()->category_id);
    }
}
