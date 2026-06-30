<?php

namespace Tests\Unit\Model;

use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function タグは複数のお問い合わせに紐づく(): void
    {
        // Arrange
        $tag = Tag::factory()->create();
        $contacts = Contact::factory()->count(2)->create();

        // Act
        $tag->contacts()->attach($contacts->pluck('id'));

        // Assert
        $this->assertCount(2, $tag->contacts);

        $attachedContactIds = $tag->contacts->pluck('id')->toArray();
        foreach ($contacts as $contact) {
            $this->assertContains($contact->id, $attachedContactIds, '紐づいているお問い合わせIDが一致しません');
        }
    }
}
