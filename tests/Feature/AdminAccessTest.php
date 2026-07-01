<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 認証済みユーザーは管理画面にアクセスできる(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->get('/admin');

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function 未認証ユーザーは管理画面にアクセスできずログイン画面にリダイレクトされる(): void
    {
        // Act
        $response = $this->get('/admin');

        // Assert
        $response->assertRedirect('/login');
    }
}
