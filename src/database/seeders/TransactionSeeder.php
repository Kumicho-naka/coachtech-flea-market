<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Transaction;
use App\Models\TransactionMessage;
use App\Models\UserRating;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        $buyer = User::where('email', 'test@example.com')->first();

        $seller = User::firstOrCreate(
            ['email' => 'seller@example.com'],
            [
                'name' => '出品者テスト',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $items = Item::take(2)->get();

        foreach ($items as $index => $item) {
            Purchase::create([
                'user_id' => $buyer->id,
                'item_id' => $item->id,
                'payment_method' => 'カード支払い',
                'postal_code' => '123-4567',
                'address' => '東京都渋谷区',
                'building' => 'テストビル101',
            ]);

            $item->update(['is_sold' => true]);

            $transaction = Transaction::create([
                'item_id' => $item->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $seller->id,
                'buyer_completed' => false,
                'seller_completed' => false,
            ]);

            TransactionMessage::create([
                'transaction_id' => $transaction->id,
                'user_id' => $buyer->id,
                'message' => 'こんにちは。商品を購入しました。よろしくお願いします。',
                'is_read' => true,
            ]);

            TransactionMessage::create([
                'transaction_id' => $transaction->id,
                'user_id' => $seller->id,
                'message' => 'ご購入ありがとうございます。本日発送いたします。',
                'is_read' => false,
            ]);

            if ($index === 0) {
                TransactionMessage::create([
                    'transaction_id' => $transaction->id,
                    'user_id' => $seller->id,
                    'message' => '発送が完了しました。',
                    'is_read' => false,
                ]);
            }
        }

        $completedItem = Item::skip(2)->first();

        if ($completedItem) {
            $completedTransaction = Transaction::create([
                'item_id' => $completedItem->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $seller->id,
                'buyer_completed' => true,
                'seller_completed' => true,
            ]);

            UserRating::create([
                'user_id' => $seller->id,
                'rater_id' => $buyer->id,
                'transaction_id' => $completedTransaction->id,
                'rating' => 5,
            ]);

            UserRating::create([
                'user_id' => $buyer->id,
                'rater_id' => $seller->id,
                'transaction_id' => $completedTransaction->id,
                'rating' => 4,
            ]);
        }
    }
}
