@extends('layouts.app')

@section('title', '取引チャット - COACHTECH フリマ')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/transaction-chat.css') }}">
@endsection

@section('content')
<div class="chat-container">
    <!-- サイドバー -->
    <aside class="chat-sidebar">
        <h2 class="sidebar-title">その他の取引</h2>
        <div class="sidebar-list">
            @foreach($otherTransactions as $otherTransaction)
            <a href="{{ route('transactions.chat', $otherTransaction) }}"
                class="sidebar-item {{ $otherTransaction->id === $transaction->id ? 'active' : '' }}">
                <span class="item-name">{{ $otherTransaction->item->name }}</span>
            </a>
            @endforeach
        </div>
    </aside>

    <!-- メインコンテンツ -->
    <main class="chat-main">
        <!-- ヘッダー -->
        <header class="chat-header">
            <div class="header-left">
                <div class="user-avatar">
                    @php
                    $partner = $transaction->buyer_id === $user->id ? $transaction->seller : $transaction->buyer;
                    @endphp
                    @if($partner->profile_image)
                    <img src="{{ Storage::url($partner->profile_image) }}" alt="{{ $partner->name }}">
                    @else
                    <div class="avatar-placeholder"></div>
                    @endif
                </div>
                <h1 class="chat-title">「{{ $partner->name }}」さんとの取引画面</h1>
            </div>

            @php
            $isCompleted = ($transaction->buyer_id === $user->id && $transaction->buyer_completed)
            || ($transaction->seller_id === $user->id && $transaction->seller_completed);
            $canComplete = ($transaction->buyer_id === $user->id && !$transaction->buyer_completed)
            || ($transaction->seller_id === $user->id && !$transaction->seller_completed);
            @endphp

            @if($canComplete)
            <form action="{{ route('transactions.complete', $transaction) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="complete-button" onclick="return confirm('取引を完了してもよろしいですか？');">
                    取引を完了する
                </button>
            </form>
            @elseif($isCompleted)
            <div class="complete-button" style="background: #CCC; cursor: default;">
                完了済み
            </div>
            @endif
        </header>

        <div class="divider"></div>

        <!-- 商品情報 -->
        <section class="item-info">
            <div class="item-image">
                @if($transaction->item->image)
                <img src="{{ Storage::url($transaction->item->image) }}" alt="{{ $transaction->item->name }}">
                @else
                <span class="image-placeholder">商品画像</span>
                @endif
            </div>
            <div class="item-details">
                <h2 class="item-name">{{ $transaction->item->name }}</h2>
                <p class="item-price">¥{{ number_format($transaction->item->price) }}</p>
            </div>
        </section>

        <div class="divider"></div>

        <!-- チャットメッセージ -->
        <section class="chat-messages" id="chatMessages">
            @foreach($messages as $message)
            <div class="message-wrapper {{ $message->user_id === $user->id ? 'message-right' : 'message-left' }}">
                @if($message->user_id !== $user->id)
                <div class="message-avatar">
                    @if($message->user->profile_image)
                    <img src="{{ Storage::url($message->user->profile_image) }}" alt="{{ $message->user->name }}">
                    @else
                    <div class="avatar-placeholder-small"></div>
                    @endif
                </div>
                @endif

                <div class="message-content">
                    <div class="message-header">
                        <span class="message-username">{{ $message->user->name }}</span>
                    </div>
                    <div class="message-bubble">
                        @if($message->image_path)
                        <img src="{{ Storage::url($message->image_path) }}" alt="添付画像" class="message-image">
                        @endif
                        @if($message->message)
                        <p class="message-text" id="message-text-{{ $message->id }}">{{ $message->message }}</p>
                        @endif
                    </div>
                    @if($message->user_id === $user->id)
                    <div class="message-actions">
                        <button class="action-button edit-message-btn"
                            data-message-id="{{ $message->id }}"
                            data-message-text="{{ $message->message }}"
                            data-update-url="{{ route('transactions.message.update', [$transaction, $message]) }}">
                            編集
                        </button>
                        <form action="{{ route('transactions.message.destroy', [$transaction, $message]) }}" method="POST" style="display: inline;" onsubmit="return confirm('本当に削除しますか？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-button">削除</button>
                        </form>
                    </div>
                    @endif
                </div>

                @if($message->user_id === $user->id)
                <div class="message-avatar">
                    @if($message->user->profile_image)
                    <img src="{{ Storage::url($message->user->profile_image) }}" alt="{{ $message->user->name }}">
                    @else
                    <div class="avatar-placeholder-small"></div>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </section>

        <!-- メッセージ入力 -->
        <section class="message-input-section">
            <form action="{{ route('transactions.message.store', $transaction) }}" method="POST" enctype="multipart/form-data" class="message-form" id="messageForm">
                @csrf
                <input type="text"
                    name="message"
                    placeholder="取引メッセージを記入してください"
                    class="message-input"
                    id="messageInput">

                <label for="imageUpload" class="image-upload-button">
                    画像を追加
                </label>
                <input type="file"
                    name="image"
                    id="imageUpload"
                    accept="image/*"
                    style="display: none;">

                <button type="submit" class="send-button">
                    <img src="{{ asset('images/send-icon.svg') }}" alt="送信">
                </button>
            </form>
        </section>
    </main>
</div>

<!-- 評価モーダル -->
@if(session('show_rating_modal'))
<div class="rating-modal-overlay" id="ratingModal">
    <div class="rating-modal">
        <h2 class="modal-title">取引が完了しました。</h2>
        <div class="modal-divider"></div>

        <p class="modal-subtitle">今回の取引相手はどうでしたか?</p>

        <form action="{{ route('ratings.store', $transaction) }}" method="POST" class="rating-form" id="ratingForm">
            @csrf
            <div class="star-rating" id="starRating">
                <input type="hidden" name="rating" id="ratingValue" value="0" required>
                @for ($i = 1; $i <= 5; $i++)
                    <img src="{{ asset('images/star-empty.svg') }}"
                    alt="星{{ $i }}"
                    class="star-rate"
                    data-value="{{ $i }}"
                    data-filled-url="{{ asset('images/star-filled.svg') }}"
                    data-empty-url="{{ asset('images/star-empty.svg') }}"
                    onclick="setRating({{ $i }})">
                    @endfor
            </div>

            <div class="modal-divider"></div>

            <button type="submit" class="submit-rating-button">送信する</button>
        </form>
    </div>
</div>
@endif
@endsection