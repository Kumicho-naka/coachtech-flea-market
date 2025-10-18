<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\EmailVerificationController;

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
    Route::get('/purchase/success/{item}', [PurchaseController::class, 'success'])->name('purchase.success');

    // 住所変更
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
});

// メール認証関連のルート
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');
});