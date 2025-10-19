@extends('layouts.app')

@section('title', '商品購入 - COACHTECH フリマ')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endpush

@section('content')
<div class="purchase-container">
    <div class="purchase-content">
        <div class="purchase-left">
            <div class="purchase-item">
                <div class="item-image-small">
                    @if($item->image)
                    <img src="{{ Storage::url($item->image) }}"
                        alt="{{ $item->name }}">
                    @else
                    商品画像
                    @endif
                </div>
                <div class="item-info">
                    <h2 class="item-title">{{ $item->name }}</h2>
                    <div class="item-price">¥ {{ number_format($item->price) }}</div>
                </div>
            </div>

            <hr class="divider">

            <div class="section-header">
                <h3 class="section-title">支払い方法</h3>
            </div>

            <div class="payment-dropdown" id="paymentDropdown">
                <div class="dropdown-selected" id="paymentSelected">
                    選択してください
                </div>
                <div class="dropdown-options" id="paymentOptions">
                    <div class="dropdown-option" data-value="コンビニ支払い">コンビニ支払い</div>
                    <div class="dropdown-option" data-value="カード支払い">カード支払い</div>
                </div>
            </div>
            @error('payment_method')
            <p class="error-message">{{ $message }}</p>
            @enderror

            <hr class="divider">

            <div class="shipping-section">
                <div class="section-header">
                    <h3 class="section-title">配送先</h3>
                    <a href="{{ route('purchase.address.edit', $item) }}" class="change-link">変更する</a>
                </div>

                <div class="shipping-address">
                    @php
                    $address = session('purchase_address', [
                    'postal_code' => $user->postal_code,
                    'address' => $user->address,
                    'building' => $user->building,
                    ]);
                    @endphp

                    <div class="address-line">〒 {{ $address['postal_code'] ?? 'XXX-YYYY' }}</div>
                    <div class="address-line">{{ $address['address'] ?? 'ここには住所と建物が入ります' }}</div>
                    @if(!empty($address['building']))
                    <div class="address-line">{{ $address['building'] }}</div>
                    @endif
                </div>
            </div>

            <hr class="divider">
        </div>

        <div class="purchase-right">
            <div class="purchase-summary">
                <div class="summary-row">
                    <span class="summary-label">商品代金</span>
                    <span class="summary-value">¥ {{ number_format($item->price) }}</span>
                </div>

                <div class="summary-row">
                    <span class="summary-label">支払い方法</span>
                    <span class="summary-value" id="paymentDisplay">コンビニ支払い</span>
                </div>

                <form method="POST" action="{{ route('purchase.store', $item) }}" id="purchaseForm">
                    @csrf
                    <input type="hidden" name="payment_method" id="paymentMethodInput" value="">
                    <input type="hidden" name="postal_code" value="{{ $address['postal_code'] }}">
                    <input type="hidden" name="address" value="{{ $address['address'] }}">
                    <input type="hidden" name="building" value="{{ $address['building'] ?? '' }}">

                    <button type="submit" class="button-purchase">購入する</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/purchase.js') }}"></script>
@endpush