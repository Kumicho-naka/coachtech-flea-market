<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ユーザー1: C001～C005の商品を出品
        User::create([
            'name' => '出品者1',
            'email' => 'seller1@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // ユーザー2: C006～C010の商品を出品
        User::create([
            'name' => '出品者2',
            'email' => 'seller2@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // ユーザー3: 何も紐づけられていない（購入者用）
        User::create([
            'name' => '購入者',
            'email' => 'buyer@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }
}
