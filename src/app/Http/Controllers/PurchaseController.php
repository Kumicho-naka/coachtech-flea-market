<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function show(Item $item)
    {
        if ($item->is_sold || $item->user_id === Auth::id()) {
            return redirect()->route('items.index');
        }

        $user = Auth::user();

        return view('purchase.show', compact('item', 'user'));
    }

    public function store(PurchaseRequest $request, Item $item)
    {
        if ($item->is_sold || $item->user_id === Auth::id()) {
            return redirect()->route('items.index');
        }

        // すべての支払い方法でStripe決済画面へ遷移
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.success', ['item' => $item->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('purchase.show', $item),
            'metadata' => [
                'item_id' => $item->id,
                'user_id' => Auth::id(),
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $request->building,
                'payment_method' => $request->payment_method,
            ],
        ]);

        return redirect($session->url);
    }

    public function success(Item $item, Request $request)
    {
        if ($request->has('session_id')) {
            $session = Session::retrieve($request->session_id);

            if ($session->payment_status === 'paid') {
                $metadata = $session->metadata;

                $this->completePurchase($item, [
                    'postal_code' => $metadata->postal_code,
                    'address' => $metadata->address,
                    'building' => $metadata->building,
                    'payment_method' => $metadata->payment_method,
                ]);
            }
        }

        return redirect()->route('items.index')->with('success', '商品を購入しました。');
    }

    public function editAddress(Item $item)
    {
        $user = Auth::user();
        return view('purchase.address', compact('item', 'user'));
    }

    public function updateAddress(AddressRequest $request, Item $item)
    {
        session([
            'purchase_address' => $request->validated()
        ]);

        return redirect()->route('purchase.show', $item);
    }

    // 購入情報をデータベースに保存
    private function completePurchase(Item $item, array $data)
    {
        Purchase::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'payment_method' => $data['payment_method'],
            'postal_code' => $data['postal_code'],
            'address' => $data['address'],
            'building' => $data['building'] ?? null,
        ]);

        $item->update(['is_sold' => true]);
    }
}
