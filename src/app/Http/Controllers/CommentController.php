<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(CommentRequest $request, Item $item)
    {
        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'content' => $request->content,
        ]);

        return redirect()->back()->with('success', 'コメントを投稿しました。');
    }
}
