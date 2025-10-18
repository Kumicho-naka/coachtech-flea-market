<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'ファッション',
            'メンズ',
            'レディース',
            'ベビー・キッズ',
            'インテリア・住まい・小物',
            'おもちゃ・ホビー・グッズ',
            'コスメ・香水・美容',
            'その他',
        ];

        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}
