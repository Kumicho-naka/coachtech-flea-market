@extends('layouts.app')

@section('title', $item->name . ' - COACHTECH フリマ')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/items.css') }}">
@endpush

@section('content')
<div class="product-detail">
    <div class="product-image-section">
        <div class="product-image-large">
            @if($item->image)
            <img src="{{ Storage::url($item->image) }}"
                alt="{{ $item->name }}">
            @else
            商品画像
            @endif
        </div>
    </div>

    <div class="product-info-section">
        <h1 class="product-title">{{ $item->name }}</h1>

        @if($item->brand)
        <p class="product-brand">{{ $item->brand }}</p>
        @endif

        <div class="product-price">¥{{ number_format($item->price) }} (税込)</div>

        <div class="product-actions">
            @auth
            <button class="like-button {{ $item->isLikedBy(auth()->user()) ? 'liked' : '' }}"
                data-item-id="{{ $item->id }}">
                <i class="star-icon">☆</i>
                <span class="likes-count">{{ $item->likes_count }}</span>
            </button>
            @else
            <div class="like-display">
                <i class="star-icon">☆</i>
                <span>{{ $item->likes_count }}</span>
            </div>
            @endauth

            <div class="comment-display">
                <i class="comment-icon">💬</i>
                <span>{{ $item->comments_count }}</span>
            </div>
        </div>

        @if(!$item->is_sold && auth()->check() && $item->user_id !== auth()->id())
        <a href="{{ route('purchase.show', $item) }}" class="purchase-button">
            購入手続きへ
        </a>
        @elseif($item->is_sold)
        <button class="purchase-button" disabled>
            売り切れ
        </button>
        @elseif(!auth()->check())
        <a href="{{ route('login') }}" class="purchase-button">
            購入手続きへ
        </a>
        @endif

        <div class="product-description">
            <h2>商品説明</h2>
            <p>{{ $item->description }}</p>
        </div>

        <div class="product-info-table">
            <h2>商品の情報</h2>

            <div class="info-row">
                <div class="info-label">カテゴリー</div>
                <div class="info-value">
                    @foreach($item->categories as $category)
                    <span class="category-badge">{{ $category->name }}</span>
                    @endforeach
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">商品の状態</div>
                <div class="info-value">{{ $item->condition->name }}</div>
            </div>
        </div>

        <div class="comments-section">
            <h2 class="comments-title">コメント ({{ $item->comments_count }})</h2>

            <div class="comment-list">
                @foreach($item->comments as $comment)
                <div class="comment-item">
                    <div class="comment-avatar">
                        @if($comment->user->profile_image)
                        <img src="{{ Storage::url($comment->user->profile_image) }}"
                            alt="{{ $comment->user->name }}">
                        @endif
                    </div>
                    <div class="comment-content">
                        <div class="comment-author">{{ $comment->user->name }}</div>
                        <div class="comment-text">{{ $comment->content }}</div>
                    </div>
                </div>
                @endforeach

                @if($item->comments->isEmpty())
                <p class="empty-comment-message">
                    まだコメントがありません
                </p>
                @endif
            </div>

            <form class="comment-form" action="{{ route('comments.store', $item) }}" method="POST">
                @csrf
                <h3>商品へのコメント</h3>
                <textarea class="form-textarea"
                    name="content"
                    placeholder="商品へのコメント">{{ old('content') }}</textarea>

                @error('content')
                <div class="error-message">{{ $message }}</div>
                @enderror

                <button type="submit" class="button-submit">
                    コメントを送信する
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/product.js') }}"></script>
@endpush