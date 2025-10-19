<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    private const TEST_EMAIL = 'test@example.com';
    private const VALID_PASSWORD = 'password123';
    private const TEST_NAME = 'テストユーザー';
    private const ITEM_NAME = 'テスト商品';
    private const ITEM_DESCRIPTION = 'テスト説明';
    private const ITEM_PRICE = 10000;
    private const ITEM_IMAGE = 'test.jpg';
    private const PAYMENT_METHOD = 'コンビニ支払い';
    private const POSTAL_CODE = '123-4567';
    private const ADDRESS = '東京都渋谷区千駄ヶ谷1-2-3';
    private const BUILDING = 'テストビル101';
    private const HOME_URL = '/';
    private const MYPAGE_BUY_URL = '/mypage?page=buy';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\ConditionSeeder::class);
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * 「購入する」ボタンを押下すると購入が完了する
     */
    public function testUserCanPurchaseItem()
    {
        /** @var \App\Models\User $buyer */
        $buyer = User::factory()->create([
            'name' => self::TEST_NAME,
            'email' => self::TEST_EMAIL,
            'password' => Hash::make(self::VALID_PASSWORD),
            'postal_code' => self::POSTAL_CODE,
            'address' => self::ADDRESS,
            'building' => self::BUILDING,
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

        $response = $this->post('/purchase/' . $item->id, [
            'payment_method' => self::PAYMENT_METHOD,
            'postal_code' => self::POSTAL_CODE,
            'address' => self::ADDRESS,
            'building' => self::BUILDING,
        ]);

        // Stripe決済画面へのリダイレクトを確認
        $response->assertStatus(302);
        $redirectUrl = $response->headers->get('Location');
        $this->assertTrue(str_contains($redirectUrl, 'checkout.stripe.com'));

        // URLからセッションIDを取得
        $urlParts = parse_url($redirectUrl);
        $path = $urlParts['path'] ?? '';
        $pathSegments = explode('/', $path);
        $sessionId = end($pathSegments);

        // cs_test_ で始まることを確認
        $this->assertStringStartsWith('cs_test_', $sessionId);

        // 決済完了をシミュレート（successページに直接アクセス）
        $successResponse = $this->actingAs($buyer)->get("/purchase/{$item->id}/success?session_id={$sessionId}");

        $successResponse->assertRedirect(self::HOME_URL);

        // 購入情報がデータベースに保存されていることを確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'payment_method' => self::PAYMENT_METHOD,
            'postal_code' => self::POSTAL_CODE,
            'address' => self::ADDRESS,
            'building' => self::BUILDING,
        ]);

        // 商品が売り切れになっていることを確認
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'is_sold' => true,
        ]);
    }

    /**
     * 購入した商品は商品一覧画面にて「sold」と表示される
     */
    public function testPurchasedItemShowsSoldOnItemList()
    {
        /** @var \App\Models\User $buyer */
        $buyer = User::factory()->create([
            'name' => self::TEST_NAME,
            'email' => self::TEST_EMAIL,
            'password' => Hash::make(self::VALID_PASSWORD),
            'postal_code' => self::POSTAL_CODE,
            'address' => self::ADDRESS,
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

        Purchase::create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'payment_method' => self::PAYMENT_METHOD,
            'postal_code' => self::POSTAL_CODE,
            'address' => self::ADDRESS,
            'building' => self::BUILDING,
        ]);

        $item->update(['is_sold' => true]);

        $this->actingAs($buyer);

        $response = $this->get(self::HOME_URL);

        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /**
     * 「プロフィール/購入した商品一覧」に追加されている
     */
    public function testPurchasedItemAppearsInProfile()
    {
        /** @var \App\Models\User $buyer */
        $buyer = User::factory()->create([
            'name' => self::TEST_NAME,
            'email' => self::TEST_EMAIL,
            'password' => Hash::make(self::VALID_PASSWORD),
            'postal_code' => self::POSTAL_CODE,
            'address' => self::ADDRESS,
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

        Purchase::create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'payment_method' => self::PAYMENT_METHOD,
            'postal_code' => self::POSTAL_CODE,
            'address' => self::ADDRESS,
            'building' => self::BUILDING,
        ]);

        $this->actingAs($buyer);

        $response = $this->get(self::MYPAGE_BUY_URL);

        $response->assertStatus(200);
        $response->assertSee(self::ITEM_NAME);
    }
}
