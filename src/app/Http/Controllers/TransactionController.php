<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Mail\TransactionCompletedMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function chat(Transaction $transaction)
    {
        $user = Auth::user();

        if ($transaction->buyer_id !== $user->id && $transaction->seller_id !== $user->id) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

        $transaction->messages()
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $transaction->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

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

        // 出品者が購入者完了後にアクセスした時、評価モーダルを表示
        if (
            $transaction->seller_id === $user->id &&
            $transaction->buyer_completed &&
            !$transaction->seller_completed
        ) {

            // 既に評価済みかチェック
            $hasRated = $user->givenRatings()
                ->where('transaction_id', $transaction->id)
                ->exists();

            if (!$hasRated) {
                session()->flash('show_rating_modal', true);
            }
        }

        return view('transactions.chat', compact('transaction', 'messages', 'otherTransactions', 'user'));
    }

    public function complete(Transaction $transaction)
    {
        $user = Auth::user();

        // 権限チェック
        if ($transaction->buyer_id !== $user->id && $transaction->seller_id !== $user->id) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

        // 購入者のみが取引完了操作を行える
        if ($transaction->buyer_id === $user->id) {
            if (!$transaction->buyer_completed) {
                $transaction->buyer_completed = true;
                $transaction->save();

                // 出品者にメール送信
                Mail::to($transaction->seller->email)
                    ->send(new TransactionCompletedMail($transaction));

                // 購入者が完了ボタンをクリック → 評価モーダル表示
                $hasRated = $user->givenRatings()
                    ->where('transaction_id', $transaction->id)
                    ->exists();

                if (!$hasRated) {
                    return redirect()->route('transactions.chat', $transaction)
                        ->with('show_rating_modal', true);
                } else {
                    return redirect()->route('transactions.chat', $transaction)
                        ->with('success', '取引を完了しました。');
                }
            }
        }

        // 出品者がアクセスした場合は何もせずリダイレクト
        return redirect()->route('transactions.chat', $transaction);
    }
}