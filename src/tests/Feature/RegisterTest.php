<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private const VALID_PASSWORD = 'password123';
    private const INVALID_SHORT_PASSWORD = 'pass123';
    private const DIFFERENT_PASSWORD = 'different123';
    private const TEST_EMAIL = 'test@example.com';
    private const TEST_NAME = 'テストユーザー';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    /**
     * 名前が入力されていない場合、バリデーションメッセージが表示される
     */
    public function testNameIsRequired()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => self::TEST_EMAIL,
            'password' => self::VALID_PASSWORD,
            'password_confirmation' => self::VALID_PASSWORD,
        ]);

        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);
    }

    /**
     * メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     */
    public function testEmailIsRequired()
    {
        $response = $this->post('/register', [
            'name' => self::TEST_NAME,
            'email' => '',
            'password' => self::VALID_PASSWORD,
            'password_confirmation' => self::VALID_PASSWORD,
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function testPasswordIsRequired()
    {
        $response = $this->post('/register', [
            'name' => self::TEST_NAME,
            'email' => self::TEST_EMAIL,
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /**
     * パスワードが7文字以下の場合、バリデーションメッセージが表示される
     */
    public function testPasswordMustBeAtLeastEightCharacters()
    {
        $response = $this->post('/register', [
            'name' => self::TEST_NAME,
            'email' => self::TEST_EMAIL,
            'password' => self::INVALID_SHORT_PASSWORD,
            'password_confirmation' => self::INVALID_SHORT_PASSWORD,
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    /**
     * パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される
     */
    public function testPasswordConfirmationMustMatch()
    {
        $response = $this->post('/register', [
            'name' => self::TEST_NAME,
            'email' => self::TEST_EMAIL,
            'password' => self::VALID_PASSWORD,
            'password_confirmation' => self::DIFFERENT_PASSWORD,
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードと一致しません']);
    }

    /**
     * 全ての項目が入力されている場合、会員情報が登録され、プロフィール設定画面に遷移される
     */
    public function testUserCanRegisterSuccessfully()
    {
        $response = $this->post('/register', [
            'name' => self::TEST_NAME,
            'email' => self::TEST_EMAIL,
            'password' => self::VALID_PASSWORD,
            'password_confirmation' => self::VALID_PASSWORD,
        ]);

        $this->assertDatabaseHas('users', [
            'name' => self::TEST_NAME,
            'email' => self::TEST_EMAIL,
        ]);

        $response->assertRedirect('/mypage/profile');
        $this->assertAuthenticated();
    }
}
