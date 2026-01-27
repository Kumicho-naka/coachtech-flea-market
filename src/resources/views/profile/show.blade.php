@extends('layouts.app')

@section('title', 'マイページ - COACHTECH フリマ')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage-container">
    <!-- ユーザー情報セクション -->
    <div class="user-info">
        <div class="profile-image">
            @if($user->profile_image)
            <img src="{{ Storage::url($user->profile_image) }}"
                alt="{{ $user->name }}"
                class="profile-avatar">
            @else
            <div class="profile-avatar-placeholder"></div>
            @endif
        </div>

        <h1 class="user-name">{{ $user->name }}</h1>

        <!-- 評価星の表示 -->
        @if($averageRating > 0)
        <div class="user-rating">
            @for ($i = 1; $i <= 5; $i++)
                @if ($i <=$averageRating)
                <img src="{{ asset('images/star-filled.svg') }}" alt="★" class="star-icon">
                @else
                <img src="{{ asset('images/star-empty.svg') }}" alt="☆" class="star-icon">
                @endif
                @endfor
        </div>
        @endif

        <a href="{{ route('profile.edit') }}" class="profile-edit-button">
            プロフィールを編集
        </a>
    </div>

    <!-- タブナビゲーション -->
    <div class="tab-navigation">
        <a href="{{ route('profile.show', ['page' => 'sell']) }}"
            class="tab-link {{ $page === 'sell' ? 'active' : '' }}">
            出品した商品
        </a>
        <a href="{{ route('profile.show', ['page' => 'buy']) }}"
            class="tab-link {{ $page === 'buy' ? 'active' : '' }}">
            購入した商品
        </a>
        <a href="{{ route('profile.show', ['page' => 'trading']) }}"
            class="tab-link {{ $page === 'trading' ? 'active' : '' }}">
            取引中の商品
            @if($unreadCount > 0)
            <span class="notification-badge">{{ $unreadCount }}</span>
            @endif
        </a>
    </div>

    <!-- 商品一覧 or 取引一覧 -->
    @if($page === 'trading')
    <!-- 取引中の商品一覧 -->
    <div class="items-section">
        @if(isset($transactions) && $transactions->count() > 0)
        <div class="items-grid">
            @foreach($transactions as $transaction)
            <a href="{{ route('transactions.chat', $transaction) }}" class="item-card">
                <div class="item-image">
                    @if($transaction->item->image)
                    <img src="{{ Storage::url($transaction->item->image) }}"
                        alt="{{ $transaction->item->name }}"
                        class="item-photo">
                    @else
                    <span class="item-placeholder">商品画像</span>
                    @endif

                    <!-- 未読通知バッジ -->
                    @if($transaction->unread_count > 0)
                    <div class="unread-badge">{{ $transaction->unread_count }}</div>
                    @endif
                </div>
                <p class="item-name">{{ $transaction->item->name }}</p>
            </a>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <p class="empty-message">取引中の商品がありません</p>
        </div>
        @endif
    </div>
    @else
    <!-- 通常の商品一覧 -->
    <div class="items-section">
        @if(isset($items) && $items->count() > 0)
        <div class="items-grid">
            @foreach($items as $item)
            <a href="{{ route('items.show', $item) }}" class="item-card">
                <div class="item-image">
                    @if($item->image)
                    <img src="{{ Storage::url($item->image) }}"
                        alt="{{ $item->name }}"
                        class="item-photo">
                    @else
                    <span class="item-placeholder">商品画像</span>
                    @endif

                    @if($item->is_sold)
                    <div class="sold-badge">Sold</div>
                    @endif
                </div>
                <p class="item-name">{{ $item->name }}</p>
            </a>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <p class="empty-message">
                @if($page === 'buy')
                購入した商品がありません
                @else
                出品した商品がありません
                @endif
            </p>
        </div>
        @endif
    </div>
    @endif
</div>
@endsection