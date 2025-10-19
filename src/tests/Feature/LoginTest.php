<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private const VALID_PASSWORD = 'password123';
    private const WRONG_PASSWORD = 'wrongpassword';
    private const TEST_EMAIL = 'test@example.com';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    /**
     * メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     */
    public function testEmailIsRequired()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => self::VALID_PASSWORD,
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function testPasswordIsRequired()
    {
        $response = $this->post('/login', [
            'email' => self::TEST_EMAIL,
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * 入力情報が間違っている場合、バリデーションメッセージが表示される
     */
    public function testLoginFailsWithInvalidCredentials()
    {
        $user = User::factory()->create([
            'email' => self::TEST_EMAIL,
            'password' => Hash::make(self::VALID_PASSWORD),
        ]);

        $response = $this->post('/login', [
            'email' => self::TEST_EMAIL,
            'password' => self::WRONG_PASSWORD,
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * 正しい情報が入力された場合、ログイン処理が実行される
     */
    public function testUserCanLoginWithCorrectCredentials()
    {
        $user = User::factory()->create([
            'email' => self::TEST_EMAIL,
            'password' => Hash::make(self::VALID_PASSWORD),
        ]);

        $response = $this->post('/login', [
            'email' => self::TEST_EMAIL,
            'password' => self::VALID_PASSWORD,
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }
}
