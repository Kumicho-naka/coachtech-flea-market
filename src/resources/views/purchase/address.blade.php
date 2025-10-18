@extends('layouts.app')

@section('title', '住所の変更 - COACHTECH フリマ')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="address-container">
    <h1 class="address-title">住所の変更</h1>

    <form method="POST" action="{{ route('purchase.address.update', $item) }}" class="address-form">
        @csrf

        <div class="form-group">
            <label for="postal_code" class="form-label">郵便番号</label>
            <input type="text"
                class="form-input @error('postal_code') is-error @enderror"
                name="postal_code"
                id="postal_code"
                value="{{ old('postal_code', session('purchase_address.postal_code', $user->postal_code)) }}"
                placeholder="123-4567">
            @error('postal_code')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="address" class="form-label">住所</label>
            <input type="text"
                class="form-input @error('address') is-error @enderror"
                name="address"
                id="address"
                value="{{ old('address', session('purchase_address.address', $user->address)) }}"
                placeholder="東京都渋谷区千駄ヶ谷1-2-3">
            @error('address')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="building" class="form-label">建物名</label>
            <input type="text"
                class="form-input @error('building') is-error @enderror"
                name="building"
                id="building"
                value="{{ old('building', session('purchase_address.building', $user->building)) }}"
                placeholder="千駄ヶ谷マンション101">
            @error('building')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="button-submit">更新する</button>
    </form>
</div>
@endsection