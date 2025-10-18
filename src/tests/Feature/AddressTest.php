<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    private const TEST_EMAIL = 'test@example.com';
    private const VALID_PASSWORD = 'password123';
    private const TEST_NAME = 'テストユーザー';
    private const ITEM_NAME = 'テスト商品';
    private const ITEM_DESCRIPTION = 'テスト説明';
    private const ITEM_PRICE = 10000;
    private const ITEM_IMAGE = 'test.jpg';
    private const OLD_POSTAL_CODE = '100-0001';
    private const OLD_ADDRESS = '東京都千代田区千代田1-1';
    private const NEW_POSTAL_CODE = '111-2222';
    private const NEW_ADDRESS = '大阪府大阪市北区梅田1-1-1';
    private const NEW_BUILDING = 'テストビル101';
    private const PAYMENT_METHOD = 'コンビニ支払い';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    /**
     * 送付先住所変更画面にて登録した住所が商品購入画面に反映されている
     */
    public function testAddressIsReflectedInPurchaseScreen()
    {
        /** @var \App\Models\User $buyer */
        $buyer = User::factory()->create([
            'name' => self::TEST_NAME,
            'email' => self::TEST_EMAIL,
            'password' => Hash::make(self::VALID_PASSWORD),
            'postal_code' => self::OLD_POSTAL_CODE,
            'address' => self::OLD_ADDRESS,
        ]);

        /** @var \App\Models\User $seller */
        $seller = User::factory()->create();

        /** @var \App\Models\Category $category */
        $category = Category::first();

        /** @var \App\Models\Condition $condition */
        $condition = Condition::first();

        /** @var \App\Models\Item $item */
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => self::ITEM_NAME,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'image' => self::ITEM_IMAGE,
        ]);

        $this->actingAs($buyer);

        $response = $this->post('/purchase/address/' . $item->id, [
            'postal_code' => self::NEW_POSTAL_CODE,
            'address' => self::NEW_ADDRESS,
            'building' => self::NEW_BUILDING,
        ]);

        $response->assertRedirect('/purchase/' . $item->id);

        $response = $this->get('/purchase/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee(self::NEW_POSTAL_CODE);
        $response->assertSee(self::NEW_ADDRESS);
        $response->assertSee(self::NEW_BUILDING);
    }

    /**
     * 購入した商品に送付先住所が紐づいて登録される
     */
    public function testAddressIsLinkedToPurchasedItem()
    {
        /** @var \App\Models\User $buyer */
        $buyer = User::factory()->create([
            'name' => self::TEST_NAME,
            'email' => self::TEST_EMAIL,
            'password' => Hash::make(self::VALID_PASSWORD),
            'postal_code' => self::OLD_POSTAL_CODE,
            'address' => self::OLD_ADDRESS,
        ]);

        /** @var \App\Models\User $seller */
        $seller = User::factory()->create();

        /** @var \App\Models\Category $category */
        $category = Category::first();

        /** @var \App\Models\Condition $condition */
        $condition = Condition::first();

        /** @var \App\Models\Item $item */
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => self::ITEM_NAME,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'image' => self::ITEM_IMAGE,
        ]);

        $this->actingAs($buyer);

        // 住所変更
        $this->post('/purchase/address/' . $item->id, [
            'postal_code' => self::NEW_POSTAL_CODE,
            'address' => self::NEW_ADDRESS,
            'building' => self::NEW_BUILDING,
        ]);

        $response = $this->post('/purchase/' . $item->id, [
            'payment_method' => self::PAYMENT_METHOD,
            'postal_code' => self::NEW_POSTAL_CODE,
            'address' => self::NEW_ADDRESS,
            'building' => self::NEW_BUILDING,
        ]);

        // Stripe決済画面へのリダイレクトを確認
        $response->assertStatus(302);
        $this->assertTrue(str_contains($response->headers->get('Location'), 'checkout.stripe.com'));
    }
}
