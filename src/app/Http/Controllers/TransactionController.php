<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function chat(Transaction $transaction)
    {
        $user = Auth::user();

        // 取引の参加者かチェック
        if ($transaction->buyer_id !== $user->id && $transaction->seller_id !== $user->id) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

        // メッセージを既読にする（相手のメッセージのみ）
        $transaction->messages()
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // メッセージを取得
        $messages = $transaction->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // 他の取引を取得（サイドバー用）
        $otherTransactions = $user->buyerTransactions()
            ->with(['item', 'messages'])
            ->where('id', '!=', $transaction->id)
            ->where(function ($query) {
                $query->where('buyer_completed', false)
                    ->orWhere('seller_completed', false);
            })
            ->get()
            ->merge(
                $user->sellerTransactions()
                    ->with(['item', 'messages'])
                    ->where('id', '!=', $transaction->id)
                    ->where(function ($query) {
                        $query->where('buyer_completed', false)
                            ->orWhere('seller_completed', false);
                    })
                    ->get()
            )
            ->sortByDesc(function ($t) {
                $latest = $t->latestMessage();
                return $latest ? $latest->created_at : $t->created_at;
            });

        return view('transactions.chat', compact('transaction', 'messages', 'otherTransactions', 'user'));
    }
}
