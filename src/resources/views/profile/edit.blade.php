@extends('layouts.app')

@section('title', 'プロフィール設定 - COACHTECH フリマ')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')
<div class="profile-edit-container">
    <h1 class="profile-edit-title">プロフィール設定</h1>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="profile-edit-form">
        @csrf
        @method('PUT')

        <div class="profile-image-section">
            <div class="profile-image-preview">
                @if($user->profile_image)
                <img src="{{ Storage::url($user->profile_image) }}"
                    alt="{{ $user->name }}"
                    class="profile-image-display"
                    id="profileImagePreview">
                @else
                <div class="profile-image-placeholder" id="profileImagePreview"></div>
                @endif
            </div>

            <div class="profile-image-upload">
                <label for="profile_image" class="image-upload-label">
                    画像を選択する
                </label>
                <input type="file"
                    name="profile_image"
                    id="profile_image"
                    accept="image/*"
                    class="image-upload-input">
                @error('profile_image')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="name" class="form-label">ユーザー名</label>
            <input type="text"
                class="form-input @error('name') error @enderror"
                name="name"
                id="name"
                value="{{ old('name', $user->name) }}">
            @error('name')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="postal_code" class="form-label">郵便番号</label>
            <input type="text"
                class="form-input @error('postal_code') error @enderror"
                name="postal_code"
                id="postal_code"
                value="{{ old('postal_code', $user->postal_code) }}"
                placeholder="123-4567">
            @error('postal_code')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="address" class="form-label">住所</label>
            <input type="text"
                class="form-input @error('address') error @enderror"
                name="address"
                id="address"
                value="{{ old('address', $user->address) }}">
            @error('address')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="building" class="form-label">建物名</label>
            <input type="text"
                class="form-input @error('building') error @enderror"
                name="building"
                id="building"
                value="{{ old('building', $user->building) }}">
            @error('building')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="button-submit">更新する</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('profile_image');
        const imagePreview = document.getElementById('profileImagePreview');

        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    imagePreview.outerHTML = `
                        <img src="${e.target.result}" 
                             alt="プレビュー画像" 
                             class="profile-image-display"
                             id="profileImagePreview">
                    `;
                };

                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endsection