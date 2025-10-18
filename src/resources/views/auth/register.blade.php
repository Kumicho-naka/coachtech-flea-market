@extends('layouts.auth')

@section('title', '会員登録 - COACHTECH フリマ')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <h1 class="auth-title">会員登録</h1>

        <form method="POST" action="{{ route('register') }}" class="auth-form" novalidate>
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">ユーザー名</label>
                <input
                    type="text"
                    class="form-input @error('name') error @enderror"
                    name="name"
                    id="name"
                    value="{{ old('name') }}"
                    required
                    autocomplete="name"
                    aria-invalid="@error('name') true @else false @enderror">
                @error('name')
                <div class="error-message" role="alert">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">メールアドレス</label>
                <input
                    type="email"
                    class="form-input @error('email') error @enderror"
                    name="email"
                    id="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    aria-invalid="@error('email') true @else false @enderror">
                @error('email')
                <div class="error-message" role="alert">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">パスワード</label>
                <input
                    type="password"
                    class="form-input @error('password') error @enderror"
                    name="password"
                    id="password"
                    required
                    autocomplete="new-password"
                    aria-invalid="@error('password') true @else false @enderror">
                @error('password')
                <div class="error-message" role="alert">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">確認用パスワード</label>
                <input
                    type="password"
                    class="form-input"
                    name="password_confirmation"
                    id="password_confirmation"
                    required
                    autocomplete="new-password">
            </div>

            <button type="submit" class="button-submit">登録する</button>
        </form>

        <div class="auth-link">
            <a href="{{ route('login') }}">ログインはこちら</a>
        </div>
    </div>
</div>
@endsection