@extends('layouts.app')

@section('title', '商品の出品 - COACHTECH フリマ')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell-container">
    <h1 class="sell-title">商品の出品</h1>

    <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data" class="sell-form">
        @csrf

        <!-- 商品画像セクション -->
        <div class="image-section">
            <h2 class="form-label">商品画像</h2>

            <div class="image-upload-area" id="imageUploadArea">
                <div class="upload-placeholder" id="uploadPlaceholder">
                    <button type="button" class="upload-button" id="uploadButton">
                        画像を選択する
                    </button>
                    <input type="file"
                        name="image"
                        id="imageInput"
                        accept="image/jpeg,image/png"
                        class="image-input">
                </div>
                <div class="image-preview" id="imagePreview" style="display: none;">
                    <img id="previewImg" src="" alt="プレビュー">
                    <button type="button" class="image-remove" id="imageRemove">×</button>
                </div>
            </div>
            @error('image')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <!-- 商品の詳細セクション -->
        <div class="detail-section">
            <h2 class="section-title">商品の詳細</h2>

            <!-- カテゴリー -->
            <div class="form-group">
                <label class="form-label">カテゴリー</label>
                <div class="category-grid">
                    @foreach($categories as $category)
                    <label class="category-item">
                        <input type="checkbox"
                            name="categories[]"
                            value="{{ $category->id }}"
                            class="category-checkbox"
                            {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                        <span class="category-label">{{ $category->name }}</span>
                    </label>
                    @endforeach
                </div>
                @error('categories')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <!-- 商品の状態 -->
            <div class="form-group">
                <label for="condition_id" class="form-label">商品の状態</label>
                <div class="condition-dropdown" id="conditionDropdown">
                    <div class="dropdown-selected" id="dropdownSelected">
                        <span id="selectedText">選択してください</span>
                        <span class="dropdown-arrow"></span>
                    </div>
                    <div class="dropdown-options" id="dropdownOptions">
                        @foreach($conditions as $condition)
                        <div class="dropdown-option" data-value="{{ $condition->id }}">
                            {{ $condition->name }}
                        </div>
                        @endforeach
                    </div>
                </div>
                <input type="hidden" name="condition_id" id="conditionId" value="{{ old('condition_id') }}">
                @error('condition_id')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- 商品名と説明セクション -->
        <div class="info-section">
            <h2 class="section-title">商品名と説明</h2>

            <div class="form-group">
                <label for="name" class="form-label">商品名</label>
                <input type="text"
                    class="form-input @error('name') is-error @enderror"
                    name="name"
                    id="name"
                    value="{{ old('name') }}">
                @error('name')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="brand" class="form-label">ブランド名</label>
                <input type="text"
                    class="form-input @error('brand') is-error @enderror"
                    name="brand"
                    id="brand"
                    value="{{ old('brand') }}">
                @error('brand')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">商品の説明</label>
                <textarea class="form-textarea @error('description') is-error @enderror"
                    name="description"
                    id="description"
                    rows="5">{{ old('description') }}</textarea>
                @error('description')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="price" class="form-label">販売価格</label>
                <div class="price-input-wrapper">
                    <span class="price-symbol">¥</span>
                    <input type="number"
                        class="form-input price-input @error('price') is-error @enderror"
                        name="price"
                        id="price"
                        value="{{ old('price') }}"
                        min="0">
                </div>
                @error('price')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button type="submit" class="button-submit">出品する</button>
    </form>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/sell.js') }}"></script>
@endsection