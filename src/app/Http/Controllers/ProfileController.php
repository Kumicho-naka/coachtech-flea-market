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

        if ($page === 'buy') {
            $items = $user->purchasedItems()->with(['categories', 'likes'])->latest()->get();
        } else {
            $items = $user->items()->with(['categories', 'likes'])->latest()->get();
        }

        return view('profile.show', compact('user', 'items', 'page'));
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
