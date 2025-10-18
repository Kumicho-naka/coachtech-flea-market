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

class LikeTest extends TestCase
{
    use RefreshDatabase;

    private const TEST_EMAIL = 'test@example.com';
    private const VALID_PASSWORD = 'password123';
    private const TEST_NAME = 'テストユーザー';
    private const ITEM_NAME = 'テスト商品';
    private const ITEM_DESCRIPTION = 'テスト説明';
    private const ITEM_PRICE = 10000;
    private const ITEM_IMAGE = 'test.jpg';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    /**
     * いいねアイコンを押下することによって、いいねした商品として登録できる
     */
    public function testUserCanLikeItem()
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

        /** @var \App\Models\Item $item */
        $item = Item::factory()->create([
            'user_id' => $anotherUser->id,
            'condition_id' => $condition->id,
            'name' => self::ITEM_NAME,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'image' => self::ITEM_IMAGE,
        ]);

        $this->actingAs($user);

        $response = $this->post('/item/' . $item->id . '/like');

        $response->assertStatus(200);
        $response->assertJson([
            'liked' => true,
            'likes_count' => 1,
        ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /**
     * 追加済みのアイコンは色が変化する
     */
    public function testLikeIconChangesAfterLiking()
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

        /** @var \App\Models\Item $item */
        $item = Item::factory()->create([
            'user_id' => $anotherUser->id,
            'condition_id' => $condition->id,
            'name' => self::ITEM_NAME,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'image' => self::ITEM_IMAGE,
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/item/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee('liked');
    }

    /**
     * 再度いいねアイコンを押下することによって、いいねを解除できる
     */
    public function testUserCanUnlikeItem()
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

        /** @var \App\Models\Item $item */
        $item = Item::factory()->create([
            'user_id' => $anotherUser->id,
            'condition_id' => $condition->id,
            'name' => self::ITEM_NAME,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'image' => self::ITEM_IMAGE,
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user);

        $response = $this->post('/item/' . $item->id . '/like');

        $response->assertStatus(200);
        $response->assertJson([
            'liked' => false,
            'likes_count' => 0,
        ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
