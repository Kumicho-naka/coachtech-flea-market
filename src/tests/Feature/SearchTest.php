<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    private const TEST_EMAIL = 'test@example.com';
    private const VALID_PASSWORD = 'password123';
    private const TEST_NAME = 'テストユーザー';
    private const ITEM_NAME_MICROPHONE = 'マイク';
    private const ITEM_NAME_HDD = 'HDD';
    private const ITEM_DESCRIPTION = 'テスト説明';
    private const ITEM_PRICE = 10000;
    private const ITEM_IMAGE = 'test.jpg';
    private const SEARCH_KEYWORD = 'マイク';
    private const HOME_URL = '/';
    private const MYLIST_URL = '/?tab=mylist';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    /**
     * 「商品名」で部分一致検索ができる
     */
    public function testCanSearchByProductName()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'name' => self::TEST_NAME,
            'email' => self::TEST_EMAIL,
            'password' => Hash::make(self::VALID_PASSWORD),
        ]);

        /** @var \App\Models\Category $category */
        $category = Category::first();

        /** @var \App\Models\Condition $condition */
        $condition = Condition::first();

        Item::factory()->create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => self::ITEM_NAME_MICROPHONE,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'image' => self::ITEM_IMAGE,
        ]);

        Item::factory()->create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => self::ITEM_NAME_HDD,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'image' => self::ITEM_IMAGE,
        ]);

        $response = $this->get(self::HOME_URL . '?search=' . self::SEARCH_KEYWORD);

        $response->assertStatus(200);
        $response->assertSee(self::ITEM_NAME_MICROPHONE);
        $response->assertDontSee(self::ITEM_NAME_HDD);
    }

    /**
     * 検索状態がマイリストでも保持されている
     */
    public function testSearchStateIsPreservedInMyList()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'name' => self::TEST_NAME,
            'email' => self::TEST_EMAIL,
            'password' => Hash::make(self::VALID_PASSWORD),
        ]);

        $this->actingAs($user);

        $response = $this->get(self::HOME_URL . '?search=' . self::SEARCH_KEYWORD);

        $response->assertStatus(200);

        $response = $this->get(self::MYLIST_URL . '&search=' . self::SEARCH_KEYWORD);

        $response->assertStatus(200);
        $response->assertSee('value="' . self::SEARCH_KEYWORD . '"', false);
    }
}
