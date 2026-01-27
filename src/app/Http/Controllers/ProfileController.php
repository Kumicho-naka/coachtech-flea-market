<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Request $request)
    {
        $user = Auth::user();
        $page = $request->get('page', 'sell');

        // 全ページで未読数を計算
        $unreadCount = $user->buyerTransactions()
            ->where(function ($query) {
                $query->where('buyer_completed', false)
                    ->orWhere('seller_completed', false);
            })
            ->get()
            ->merge(
                $user->sellerTransactions()
                    ->where(function ($query) {
                        $query->where('buyer_completed', false)
                            ->orWhere('seller_completed', false);
                    })
                    ->get()
            )
            ->sum(function ($transaction) use ($user) {
                return $transaction->unreadMessagesCount($user->id);
            });

        if ($page === 'buy') {
            $items = $user->purchasedItems()->with(['categories', 'likes'])->latest()->get();
        } elseif ($page === 'trading') {
            $buyerTransactions = $user->buyerTransactions()
                ->with(['item.categories', 'messages'])
                ->where(function ($query) {
                    $query->where('buyer_completed', false)
                        ->orWhere('seller_completed', false);
                })
                ->get();

            $sellerTransactions = $user->sellerTransactions()
                ->with(['item.categories', 'messages'])
                ->where(function ($query) {
                    $query->where('buyer_completed', false)
                        ->orWhere('seller_completed', false);
                })
                ->get();

            $transactions = $buyerTransactions->merge($sellerTransactions)
                ->sortByDesc(function ($transaction) {
                    $latestMessage = $transaction->latestMessage();
                    return $latestMessage ? $latestMessage->created_at : $transaction->created_at;
                });

            foreach ($transactions as $transaction) {
                $transaction->unread_count = $transaction->unreadMessagesCount($user->id);
            }

            $averageRating = $user->averageRating();

            return view('profile.show', compact('user', 'transactions', 'page', 'averageRating', 'unreadCount'));
        } else {
            $items = $user->items()->with(['categories', 'likes'])->latest()->get();
        }

        $averageRating = $user->averageRating();

        return view('profile.show', compact('user', 'items', 'page', 'averageRating', 'unreadCount'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        // プロフィール画像の更新
        if ($request->hasFile('profile_image')) {
            // 古い画像を削除
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $data['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        $user->update($data);

        return redirect()->route('profile.show')->with('success', 'プロフィールを更新しました。');
    }
}
