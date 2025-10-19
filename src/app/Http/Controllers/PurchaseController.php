<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['webhook']);
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

        $paymentMethodTypes = $request->payment_method === 'コンビニ支払い'
            ? ['konbini']
            : ['card'];

        $sessionData = [
            'payment_method_types' => $paymentMethodTypes,
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
                'building' => $request->building ?? '',
                'payment_method' => $request->payment_method,
            ],
        ];

        if ($request->payment_method === 'コンビニ支払い') {
            $sessionData['payment_method_options'] = [
                'konbini' => [
                    'expires_after_days' => 3,
                ],
            ];
        }

        $session = Session::create($sessionData);

        return redirect($session->url);
    }

    public function success(Item $item, Request $request)
    {
        if ($request->has('session_id')) {
            $session = Session::retrieve($request->session_id);

            if ($session->payment_status === 'paid' || $session->payment_status === 'unpaid') {
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

    private function completePurchase(Item $item, array $data)
    {
        $userId = $data['user_id'] ?? Auth::id();

        $existingPurchase = Purchase::where('user_id', $userId)
            ->where('item_id', $item->id)
            ->first();

        if ($existingPurchase) {
            return;
        }

        Purchase::create([
            'user_id' => $userId,
            'item_id' => $item->id,
            'payment_method' => $data['payment_method'],
            'postal_code' => $data['postal_code'],
            'address' => $data['address'],
            'building' => $data['building'] ?? null,
        ]);

        $item->update(['is_sold' => true]);
    }


    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            if ($endpointSecret) {
                $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            } else {
                $event = json_decode($payload);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Webhook error'], 400);
        }

        // checkout.session.completedイベント
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $metadata = $session->metadata;

            $item = Item::find($metadata->item_id);

            if ($item && !$item->is_sold) {
                Purchase::create([
                    'user_id' => $metadata->user_id,
                    'item_id' => $item->id,
                    'payment_method' => $metadata->payment_method,
                    'postal_code' => $metadata->postal_code,
                    'address' => $metadata->address,
                    'building' => $metadata->building ?? null,
                ]);

                $item->update(['is_sold' => true]);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
