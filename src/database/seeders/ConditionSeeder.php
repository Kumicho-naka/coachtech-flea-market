<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Condition;

class ConditionSeeder extends Seeder
{
    public function run(): void
    {
        $conditions = [
            '良好',
            '目立った傷や汚れなし',
            'やや傷や汚れあり',
            '状態が悪い',
        ];

        foreach ($conditions as $condition) {
            Condition::create(['name' => $condition]);
        }
    }
}
