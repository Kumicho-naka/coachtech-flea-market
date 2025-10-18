@extends('layouts.auth')

@section('title', 'ログイン - COACHTECH フリマ')

@section('content')
<div class="auth-container">
    <div class="auth-card login-card">
        <h1 class="auth-title">ログイン</h1>

        <form method="POST" action="{{ route('login') }}" class="auth-form">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">メールアドレス</label>
                <input type="email"
                    class="form-input @error('email') error @enderror"
                    name="email"
                    id="email"
                    value="{{ old('email') }}">
                @error('email')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">パスワード</label>
                <input type="password"
                    class="form-input @error('password') error @enderror"
                    name="password"
                    id="password">
                @error('password')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="button-submit">ログインする</button>
        </form>

        <div class="auth-link">
            <a href="{{ route('register') }}">会員登録はこちら</a>
        </div>
    </div>
</div>
@endsection