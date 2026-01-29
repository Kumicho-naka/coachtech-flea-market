<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionMessageController;
use App\Http\Controllers\RatingController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// 商品関連のルート
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

// 認証が必要なルート
Route::middleware(['auth'])->group(function () {
    // 商品出品
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');

    // プロフィール
    Route::get('/mypage', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    // いいね機能
    Route::post('/item/{item}/like', [LikeController::class, 'toggle'])->name('items.like');

    // コメント機能
    Route::post('/item/{item}/comment', [CommentController::class, 'store'])->name('comments.store');

    // 購入関連
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/{item}/success', [PurchaseController::class, 'success'])->name('purchase.success');

    // 住所変更
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

    // 取引チャット
    Route::get('/transaction/{transaction}', [TransactionController::class, 'chat'])->name('transactions.chat');

    // 取引完了
    Route::post('/transaction/{transaction}/complete', [TransactionController::class, 'complete'])->name('transactions.complete');

    // メッセージ送信
    Route::post('/transaction/{transaction}/message', [TransactionMessageController::class, 'store'])->name('transactions.message.store');

    // メッセージ編集・削除
    Route::put('/transaction/{transaction}/message/{message}', [TransactionMessageController::class, 'update'])->name('transactions.message.update');
    Route::delete('/transaction/{transaction}/message/{message}', [TransactionMessageController::class, 'destroy'])->name('transactions.message.destroy');

    // 評価送信
    Route::post('/transaction/{transaction}/rating', [RatingController::class, 'store'])->name('ratings.store');
});

// メール認証関連（応用機能）
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/mypage/profile')->with('success', 'メール認証が完了しました。');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('resent', true);
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// Stripe Webhook（CSRF保護を除外）
Route::post('/webhook/stripe', [PurchaseController::class, 'webhook'])->name('stripe.webhook');