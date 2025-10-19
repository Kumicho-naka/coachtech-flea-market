<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    private const TEST_EMAIL = 'test@example.com';
    private const VALID_PASSWORD = 'password123';
    private const TEST_NAME = 'テストユーザー';
    private const ITEM_NAME = 'テスト商品';
    private const ITEM_BRAND = 'テストブランド';
    private const ITEM_DESCRIPTION = 'これはテスト商品の説明です';
    private const ITEM_PRICE = 15000;
    private const ITEM_IMAGE = 'test.jpg';
    private const COMMENT_CONTENT = 'テストコメント';
    private const CATEGORY_NAME_1 = 'ファッション';
    private const CATEGORY_NAME_2 = 'メンズ';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    /**
     * 必要な情報が表示される
     */
    public function testAllRequiredInformationIsDisplayed()
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

        /** @var \App\Models\Item $item */
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => self::ITEM_NAME,
            'brand' => self::ITEM_BRAND,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'image' => self::ITEM_IMAGE,
        ]);

        $item->categories()->attach($category->id);

        Comment::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => self::COMMENT_CONTENT,
        ]);

        $response = $this->get('/item/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee(self::ITEM_NAME);
        $response->assertSee(self::ITEM_BRAND);
        $response->assertSee(self::ITEM_DESCRIPTION);
        $response->assertSee(number_format(self::ITEM_PRICE));
        $response->assertSee($category->name);
        $response->assertSee($condition->name);
        $response->assertSee(self::COMMENT_CONTENT);
        $response->assertSee(self::TEST_NAME);
    }

    /**
     * 複数選択されたカテゴリが表示されている
     */
    public function testMultipleCategoriesAreDisplayed()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'name' => self::TEST_NAME,
            'email' => self::TEST_EMAIL,
            'password' => Hash::make(self::VALID_PASSWORD),
        ]);

        /** @var \App\Models\Condition $condition */
        $condition = Condition::first();

        /** @var \App\Models\Item $item */
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => self::ITEM_NAME,
            'description' => self::ITEM_DESCRIPTION,
            'price' => self::ITEM_PRICE,
            'image' => self::ITEM_IMAGE,
        ]);

        /** @var \App\Models\Category $category1 */
        $category1 = Category::where('name', self::CATEGORY_NAME_1)->first();

        /** @var \App\Models\Category $category2 */
        $category2 = Category::where('name', self::CATEGORY_NAME_2)->first();

        $item->categories()->attach([$category1->id, $category2->id]);

        $response = $this->get('/item/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee(self::CATEGORY_NAME_1);
        $response->assertSee(self::CATEGORY_NAME_2);
    }
}
