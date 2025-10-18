<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ItemListTest extends TestCase
{
    use RefreshDatabase;

    private const TEST_EMAIL = 'test@example.com';
    private const VALID_PASSWORD = 'password123';
    private const TEST_NAME = 'テストユーザー';
    private const ITEM_NAME_1 = 'テスト商品1';
    private const ITEM_NAME_2 = 'テスト商品2';
    private const ITEM_NAME_3 = '自分の商品';
    private const ITEM_DESCRIPTION = 'テスト説明';
    private const ITEM_PRICE = 10000;
    private const ITEM_IMAGE = 'test.jpg';
    private const HOME_URL = '/';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    /**
     * 全商品を取得できる
     */
    public function testCanViewAllItems()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'name' => self::TEST_NAME,
            'email' => self::TEST_EMAIL,
            'password' => Hash::make(self::VALID_PASSWORD),
        ]);

        /** @var \App\Models\Condition $condition */
        $condition = Condition::first();

        Item::factory()->create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => self::ITEM_NAME_1,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'image' => self::ITEM_IMAGE,
        ]);

        Item::factory()->create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => self::ITEM_NAME_2,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'image' => self::ITEM_IMAGE,
        ]);

        $response = $this->get(self::HOME_URL);

        $response->assertStatus(200);
        $response->assertSee(self::ITEM_NAME_1);
        $response->assertSee(self::ITEM_NAME_2);
    }

    /**
     * 購入済み商品は「Sold」と表示される
     */
    public function testPurchasedItemsShowSoldLabel()
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

        $item = Item::factory()->create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => self::ITEM_NAME_1,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'image' => self::ITEM_IMAGE,
            'is_sold' => true,
        ]);

        $response = $this->get(self::HOME_URL);

        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /**
     * 自分が出品した商品は表示されない
     */
    public function testOwnItemsAreNotDisplayed()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'name' => self::TEST_NAME,
            'email' => self::TEST_EMAIL,
            'password' => Hash::make(self::VALID_PASSWORD),
        ]);

        /** @var \App\Models\User $anotherUser */
        $anotherUser = User::factory()->create();

        /** @var \App\Models\Category $category */
        $category = Category::first();

        /** @var \App\Models\Condition $condition */
        $condition = Condition::first();

        Item::factory()->create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => self::ITEM_NAME_3,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'image' => self::ITEM_IMAGE,
        ]);

        Item::factory()->create([
            'user_id' => $anotherUser->id,
            'condition_id' => $condition->id,
            'name' => self::ITEM_NAME_1,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'image' => self::ITEM_IMAGE,
        ]);

        $this->actingAs($user);

        $response = $this->get(self::HOME_URL);

        $response->assertStatus(200);
        $response->assertDontSee(self::ITEM_NAME_3);
        $response->assertSee(self::ITEM_NAME_1);
    }
}
