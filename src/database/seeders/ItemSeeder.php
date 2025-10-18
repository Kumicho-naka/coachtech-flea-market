<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Models\Condition;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();
        $conditions = Condition::all();
        $categories = Category::all();

        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'items/dummy1.jpg',
                'condition' => '良好',
                'categories' => ['ファッション', 'メンズ'],
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'items/dummy2.jpg',
                'condition' => '良好',
                'categories' => ['その他'],
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand' => null,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'items/dummy3.jpg',
                'condition' => 'やや傷や汚れあり',
                'categories' => ['その他'],
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'brand' => null,
                'description' => 'クラシックなデザインの革靴',
                'image' => 'items/dummy4.jpg',
                'condition' => '状態が悪い',
                'categories' => ['ファッション', 'メンズ'],
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'brand' => null,
                'description' => '高性能なノートパソコン',
                'image' => 'items/dummy5.jpg',
                'condition' => '良好',
                'categories' => ['その他'],
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'brand' => null,
                'description' => '高音質のレコーディング用マイク',
                'image' => 'items/dummy6.jpg',
                'condition' => '良好',
                'categories' => ['その他'],
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'items/dummy7.jpg',
                'condition' => 'やや傷や汚れあり',
                'categories' => ['ファッション', 'レディース'],
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'brand' => null,
                'description' => '使いやすいタンブラー',
                'image' => 'items/dummy8.jpg',
                'condition' => '状態が悪い',
                'categories' => ['インテリア・住まい・小物'],
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'image' => 'items/dummy9.jpg',
                'condition' => '良好',
                'categories' => ['インテリア・住まい・小物'],
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'brand' => null,
                'description' => '便利なメイクアップセット',
                'image' => 'items/dummy10.jpg',
                'condition' => '良好',
                'categories' => ['コスメ・香水・美容', 'レディース'],
            ],
        ];

        foreach ($items as $itemData) {
            $condition = $conditions->where('name', $itemData['condition'])->first();
            $categoryNames = $itemData['categories'];

            unset($itemData['condition'], $itemData['categories']);

            $item = Item::create([
                'user_id' => $user->id,
                'condition_id' => $condition->id,
                'name' => $itemData['name'],
                'brand' => $itemData['brand'],
                'description' => $itemData['description'],
                'price' => $itemData['price'],
                'image' => $itemData['image'],
            ]);

            foreach ($categoryNames as $categoryName) {
                $category = $categories->where('name', $categoryName)->first();
                if ($category) {
                    $item->categories()->attach($category->id);
                }
            }
        }
    }
}
