<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private const TEST_EMAIL = 'test@example.com';
    private const VALID_PASSWORD = 'password123';
    private const TEST_NAME = 'テストユーザー';
    private const POSTAL_CODE = '123-4567';
    private const ADDRESS = '東京都渋谷区千駄ヶ谷1-2-3';
    private const BUILDING = 'テストビル101';
    private const MYPAGE_URL = '/mypage';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    /**
     * 必要な情報が取得できる
     */
    public function testAllUserInformationIsDisplayed()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'name' => self::TEST_NAME,
            'email' => self::TEST_EMAIL,
            'password' => Hash::make(self::VALID_PASSWORD),
            'postal_code' => self::POSTAL_CODE,
            'address' => self::ADDRESS,
            'building' => self::BUILDING,
        ]);

        $this->actingAs($user);

        $response = $this->get(self::MYPAGE_URL);

        $response->assertStatus(200);
        $response->assertSee(self::TEST_NAME);
    }
}
