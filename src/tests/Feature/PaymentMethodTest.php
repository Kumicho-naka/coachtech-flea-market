<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    private const TEST_EMAIL = 'test@example.com';
    private const VALID_PASSWORD = 'password123';
    private const TEST_NAME = 'テストユーザー';
    private const ITEM_NAME = 'テスト商品';
    private const ITEM_DESCRIPTION = 'テスト説明';
    private const ITEM_PRICE = 10000;
    private const ITEM_IMAGE = 'test.jpg';
    private const PAYMENT_METHOD_CONVENIENCE = 'コンビニ支払い';
    private const PAYMENT_METHOD_CARD = 'カード支払い';
    private const POSTAL_CODE = '123-4567';
    private const ADDRESS = '東京都渋谷区千駄ヶ谷1-2-3';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    /**
     * 小計画面で変更が反映される
     */
    public function testPaymentMethodSelectionIsReflected()
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

        $this->actingAs($buyer);

        $response = $this->get('/purchase/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee(self::PAYMENT_METHOD_CONVENIENCE);
        $response->assertSee(self::PAYMENT_METHOD_CARD);
    }
}
