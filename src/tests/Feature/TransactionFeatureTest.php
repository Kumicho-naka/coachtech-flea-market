<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // FN001-FN005: 取引チャット確認
    public function test_buyer_can_view_transactions()
    {
        $buyer = User::where('email', 'buyer@example.com')->first();
        $response = $this->actingAs($buyer)->get('/mypage');
        $response->assertStatus(200);
        $response->assertSee('取引中の商品');
    }

    // FN006: 取引チャット投稿
    public function test_can_send_message()
    {
        $buyer = User::where('email', 'buyer@example.com')->first();
        $transaction = Transaction::where('buyer_id', $buyer->id)->first();

        $response = $this->actingAs($buyer)->post("/transaction/{$transaction->id}/message", [
            'message' => 'テストメッセージ'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('transaction_messages', [
            'transaction_id' => $transaction->id,
            'message' => 'テストメッセージ'
        ]);
    }

    // FN012: 購入者が取引完了
    public function test_buyer_can_complete_transaction()
    {
        $buyer = User::where('email', 'buyer@example.com')->first();
        $transaction = Transaction::where('buyer_id', $buyer->id)
            ->where('buyer_completed', false)->first();

        $response = $this->actingAs($buyer)->post("/transaction/{$transaction->id}/complete");

        $response->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'buyer_completed' => true
        ]);
    }

    // FN014: 評価送信後に商品一覧画面に遷移
    public function test_rating_redirects_to_items_index()
    {
        $buyer = User::where('email', 'buyer@example.com')->first();
        $transaction = Transaction::where('buyer_id', $buyer->id)->first();
        $transaction->update(['buyer_completed' => true]);

        $response = $this->actingAs($buyer)->post(route('ratings.store', $transaction), [
            'rating' => 5
        ]);

        $response->assertRedirect(route('items.index'));
    }
}
