@extends('layouts.app')

@section('title', '商品一覧 - COACHTECH フリマ')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/items.css') }}">
@endpush

@section('content')
<div class="tab-navigation">
    <div class="tab-container">
        <a href="{{ route('items.index', request()->only(['search'])) }}"
            class="tab-button {{ !request('tab') ? 'active' : '' }}">
            おすすめ
        </a>
        <a href="{{ route('items.index', array_merge(request()->only(['search']), ['tab' => 'mylist'])) }}"
            class="tab-button {{ request('tab') === 'mylist' ? 'active' : '' }}">
            マイリスト
        </a>
    </div>
</div>

<div class="items-container">
    @if($items->count() > 0)
    <div class="items-grid">
        @foreach($items as $item)
        <a href="{{ route('items.show', $item) }}" class="item-card">
            <div class="item-image">
                @if($item->image)
                <img src="{{ Storage::url($item->image) }}"
                    alt="{{ $item->name }}">
                @else
                商品画像
                @endif

                @if($item->is_sold)
                <div class="sold-overlay">
                    <span class="sold-badge">Sold</span>
                </div>
                @endif
            </div>
            <div class="item-name">{{ $item->name }}</div>
        </a>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <p>
            @if(request('tab') === 'mylist')
            まだいいねした商品がありません
            @else
            商品がありません
            @endif
        </p>
    </div>
    @endif
</div>
@endsection