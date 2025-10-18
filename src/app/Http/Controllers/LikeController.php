<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function toggle(Item $item)
    {
        $user = Auth::user();

        // すでにいいねしているか確認
        $like = Like::where('user_id', $user->id)
            ->where('item_id', $item->id)
            ->first();

        // いいね済みなら削除、未いいねなら追加
        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            Like::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
            ]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $item->likes()->count(),
        ]);
    }
}
