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

        return view('transactions.chat', compact('transaction', 'messages', 'otherTransactions', 'user'));
    }

    public function complete(Transaction $transaction)
    {
        $user = Auth::user();

        if ($transaction->buyer_id !== $user->id && $transaction->seller_id !== $user->id) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

        if ($transaction->buyer_id === $user->id) {
            if (!$transaction->buyer_completed) {
                $transaction->buyer_completed = true;
                $transaction->save();

                Mail::to($transaction->seller->email)
                    ->send(new TransactionCompletedMail($transaction));
            }
        } else {
            $transaction->seller_completed = true;
            $transaction->save();
        }

        if ($transaction->buyer_completed && $transaction->seller_completed) {
            $hasRated = $user->givenRatings()
                ->where('transaction_id', $transaction->id)
                ->exists();

            if (!$hasRated) {
                return redirect()->route('transactions.chat', $transaction)
                    ->with('show_rating_modal', true);
            } else {
                return redirect()->route('profile.show')
                    ->with('success', '取引が完了しました。');
            }
        }

        return redirect()->route('transactions.chat', $transaction)
            ->with('success', '取引を完了しました。相手も完了すると評価画面が表示されます。');
    }
}
