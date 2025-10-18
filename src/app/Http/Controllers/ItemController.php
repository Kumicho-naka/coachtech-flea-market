<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['categories', 'likes', 'user']);

        // 自分が出品した商品は表示しない
        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }

        // マイリストタブの処理
        if ($request->has('tab') && $request->tab === 'mylist') {
            if (Auth::check()) {
                // ログインユーザーがいいねした商品のみ取得
                $query = Item::with(['categories', 'likes', 'user'])
                    ->whereHas('likes', function ($q) {
                        $q->where('user_id', Auth::id());
                    });
            } else {
                $query = Item::whereRaw('1 = 0');
            }
        }

        // 商品名で部分一致検索
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $items = $query->latest()->get();

        return view('items.index', compact('items'));
    }

    public function show($id)
    {
        $item = Item::with([
            'categories',
            'likes',
            'comments.user',
            'condition',
            'user'
        ])->findOrFail($id);

        return view('items.show', compact('item'));
    }

    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();

        return view('items.create', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        $data = $request->validated();

        // 商品画像をstorageに保存
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('items', 'public');
        }

        $data['user_id'] = Auth::id();

        $item = Item::create($data);

        // 選択されたカテゴリを商品に紐付け
        $item->categories()->attach($request->categories);

        return redirect()->route('items.index')->with('success', '商品を出品しました。');
    }
}
