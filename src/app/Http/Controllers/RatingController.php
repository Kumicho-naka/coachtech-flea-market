<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\UserRating;
use App\Http\Requests\RatingRequest;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(RatingRequest $request, Transaction $transaction)
    {
        $user = Auth::user();

        // 取引の参加者かチェック
        if ($transaction->buyer_id !== $user->id && $transaction->seller_id !== $user->id) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

        // 両者が取引を完了しているかチェック
        if (!$transaction->buyer_completed || !$transaction->seller_completed) {
            return redirect()->route('transactions.chat', $transaction)
                ->with('error', '取引が完了していません。');
        }

        // 既に評価済みかチェック
        $existingRating = UserRating::where('transaction_id', $transaction->id)
            ->where('rater_id', $user->id)
            ->first();

        if ($existingRating) {
            return redirect()->route('profile.show')
                ->with('error', '既に評価済みです。');
        }

        // 評価対象のユーザーを特定
        $ratedUserId = $transaction->buyer_id === $user->id
            ? $transaction->seller_id
            : $transaction->buyer_id;

        // 評価を保存
        UserRating::create([
            'user_id' => $ratedUserId,
            'rater_id' => $user->id,
            'transaction_id' => $transaction->id,
            'rating' => $request->rating,
        ]);

        return redirect()->route('items.index')
            ->with('success', '評価を送信しました。');
    }
}
