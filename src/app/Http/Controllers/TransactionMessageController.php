<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransactionMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Transaction $transaction)
    {
        $user = Auth::user();

        // 取引の参加者かチェック
        if ($transaction->buyer_id !== $user->id && $transaction->seller_id !== $user->id) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

        // バリデーション
        $request->validate([
            'message' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:5120',
        ]);

        // 画像アップロード処理
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('transaction_messages', 'public');
        }

        // メッセージ作成
        TransactionMessage::create([
            'transaction_id' => $transaction->id,
            'user_id' => $user->id,
            'message' => $request->message,
            'image_path' => $imagePath,
            'is_read' => false,
        ]);

        return redirect()->route('transactions.chat', $transaction)
            ->with('success', 'メッセージを送信しました。');
    }
}
