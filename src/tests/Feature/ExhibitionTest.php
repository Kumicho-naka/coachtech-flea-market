<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExhibitionTest extends TestCase
{
    use RefreshDatabase;

    private const TEST_EMAIL = 'test@example.com';
    private const VALID_PASSWORD = 'password123';
    private const TEST_NAME = 'テストユーザー';
    private const ITEM_NAME = 'テスト商品';
    private const ITEM_BRAND = 'テストブランド';
    private const ITEM_DESCRIPTION = 'これはテスト商品の説明です';
    private const ITEM_PRICE = 15000;
    private const IMAGE_FILENAME = 'test.jpg';
    private const SELL_URL = '/sell';
    private const HOME_URL = '/';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\ConditionSeeder::class);
        Storage::fake('public');
    }

    /**
     * 商品出品画面にて必要な情報が保存できること
     */
    public function testAllRequiredInformationCanBeSaved()
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

        $this->actingAs($user);

        $file = UploadedFile::fake()->image(self::IMAGE_FILENAME);

        $response = $this->post(self::SELL_URL, [
            'name' => self::ITEM_NAME,
            'brand' => self::ITEM_BRAND,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'condition_id' => $condition->id,
            'categories' => [$category->id],
            'image' => $file,
        ]);

        $response->assertRedirect(self::HOME_URL);

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => self::ITEM_NAME,
            'brand' => self::ITEM_BRAND,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'condition_id' => $condition->id,
        ]);

        /** @var \App\Models\Item $item */
        $item = Item::where('name', self::ITEM_NAME)->first();

        $this->assertDatabaseHas('item_categories', [
            'item_id' => $item->id,
            'category_id' => $category->id,
        ]);

        // 画像が保存されたか確認
        $this->assertNotNull($item->image);
        $this->assertTrue(Storage::disk('public')->exists($item->image));
    }
}
