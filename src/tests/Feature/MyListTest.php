<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    private const TEST_EMAIL = 'test@example.com';
    private const VALID_PASSWORD = 'password123';
    private const TEST_NAME = 'テストユーザー';
    private const LIKED_ITEM_NAME = 'いいねした商品';
    private const NOT_LIKED_ITEM_NAME = 'いいねしていない商品';
    private const ITEM_DESCRIPTION = 'テスト説明';
    private const ITEM_PRICE = 10000;
    private const ITEM_IMAGE = 'test.jpg';
    private const MYLIST_URL = '/?tab=mylist';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    /**
     * いいねした商品だけが表示される
     */
    public function testCanViewLikedItemsOnly()
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

        /** @var \App\Models\Item $likedItem */
        $likedItem = Item::factory()->create([
            'user_id' => $anotherUser->id,
            'condition_id' => $condition->id,
            'name' => self::LIKED_ITEM_NAME,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'image' => self::ITEM_IMAGE,
        ]);

        $notLikedItem = Item::factory()->create([
            'user_id' => $anotherUser->id,
            'condition_id' => $condition->id,
            'name' => self::NOT_LIKED_ITEM_NAME,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'image' => self::ITEM_IMAGE,
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(self::MYLIST_URL);

        $response->assertStatus(200);
        $response->assertSee(self::LIKED_ITEM_NAME);
        $response->assertDontSee(self::NOT_LIKED_ITEM_NAME);
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

        /** @var \App\Models\User $anotherUser */
        $anotherUser = User::factory()->create();

        /** @var \App\Models\Category $category */
        $category = Category::first();

        /** @var \App\Models\Condition $condition */
        $condition = Condition::first();

        /** @var \App\Models\Item $soldItem */
        $soldItem = Item::factory()->create([
            'user_id' => $anotherUser->id,
            'condition_id' => $condition->id,
            'name' => self::LIKED_ITEM_NAME,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'image' => self::ITEM_IMAGE,
            'is_sold' => true,
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $soldItem->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(self::MYLIST_URL);

        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /**
     * 未認証の場合は何も表示されない
     */
    public function testGuestUserSeesNoItems()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        /** @var \App\Models\Category $category */
        $category = Category::first();

        /** @var \App\Models\Condition $condition */
        $condition = Condition::first();

        $item = Item::factory()->create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => self::LIKED_ITEM_NAME,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'image' => self::ITEM_IMAGE,
        ]);

        $response = $this->get(self::MYLIST_URL);

        $response->assertStatus(200);
        // 空の状態メッセージが表示されることを確認
        $response->assertSee('まだいいねした商品がありません');
        // 商品カードのクラスが存在しないことを確認
        $response->assertDontSee('item-card');
    }
}
