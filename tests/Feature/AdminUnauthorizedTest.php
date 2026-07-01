<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminUnauthorizedTest extends TestCase
{
    /** @test */
    public function 未認証ユーザーはお問い合わせ一覧を表示できずログイン画面へリダイレクトされる(): void
    {
        // Act
        $this->get('/admin')->assertRedirect('/login');
    }

    /** @test */
    public function 未認証ユーザーはお問い合わせ詳細を表示できずログイン画面へリダイレクトされる(): void
    {
        // Act
        $this->get('/admin/contacts/1')->assertRedirect('/login');
    }

    /** @test */
    public function 未認証ユーザーはお問い合わせを削除できずログイン画面へリダイレクトされる(): void
    {
        // Act
        $this->delete('/admin/contacts/1')->assertRedirect('/login');
    }
}
