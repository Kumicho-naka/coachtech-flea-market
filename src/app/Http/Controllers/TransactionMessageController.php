<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionMessage;
use App\Http\Requests\TransactionMessageRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransactionMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(TransactionMessageRequest $request, Transaction $transaction)
    {
        $user = Auth::user();

        // 取引の参加者かチェック
        if ($transaction->buyer_id !== $user->id && $transaction->seller_id !== $user->id) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

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

    public function update(TransactionMessageRequest $request, Transaction $transaction, TransactionMessage $message)
    {
        $user = Auth::user();

        // メッセージの所有者かチェック
        if ($message->user_id !== $user->id) {
            abort(403, 'このメッセージを編集する権限がありません。');
        }

        // メッセージが属する取引かチェック
        if ($message->transaction_id !== $transaction->id) {
            abort(404);
        }

        // メッセージ更新（編集時はmessageが必須）
        $message->update([
            'message' => $request->message,
        ]);

        return redirect()->route('transactions.chat', $transaction)
            ->with('success', 'メッセージを編集しました。');
    }

    public function destroy(Transaction $transaction, TransactionMessage $message)
    {
        $user = Auth::user();

        // メッセージの所有者かチェック
        if ($message->user_id !== $user->id) {
            abort(403, 'このメッセージを削除する権限がありません。');
        }

        // メッセージが属する取引かチェック
        if ($message->transaction_id !== $transaction->id) {
            abort(404);
        }

        // 画像がある場合は削除
        if ($message->image_path) {
            Storage::disk('public')->delete($message->image_path);
        }

        // メッセージ削除
        $message->delete();

        return redirect()->route('transactions.chat', $transaction)
            ->with('success', 'メッセージを削除しました。');
    }
}
