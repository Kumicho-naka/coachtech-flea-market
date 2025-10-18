<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    private const TEST_EMAIL = 'test@example.com';
    private const VALID_PASSWORD = 'password123';
    private const TEST_NAME = 'テストユーザー';
    private const POSTAL_CODE = '123-4567';
    private const ADDRESS = '東京都渋谷区千駄ヶ谷1-2-3';
    private const BUILDING = 'テストビル101';
    private const PROFILE_EDIT_URL = '/mypage/profile';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    /**
     * 変更項目が初期値として過去設定されていること
     */
    public function testInitialValuesAreDisplayed()
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

        $response = $this->get(self::PROFILE_EDIT_URL);

        $response->assertStatus(200);
        $response->assertSee('value="' . self::TEST_NAME . '"', false);
        $response->assertSee('value="' . self::POSTAL_CODE . '"', false);
        $response->assertSee('value="' . self::ADDRESS . '"', false);
        $response->assertSee('value="' . self::BUILDING . '"', false);
    }
}
