<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'COACHTECH フリマ')</title>

    <!-- 共通CSS -->
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">

    <!-- 各ページ専用CSS -->
    @yield('styles')
    @stack('styles')
</head>

<body>
    <header class="header">
        <div class="header-content">
            <a href="{{ route('items.index') }}" class="logo">
                <img src="{{ asset('logo.svg') }}" alt="COACHTECH">
            </a>

            <div class="search-container">
                <form method="GET" action="{{ route('items.index') }}">
                    @if(request('tab'))
                    <input type="hidden" name="tab" value="{{ request('tab') }}">
                    @endif
                    <input type="search"
                        name="search"
                        class="search-box"
                        placeholder="なにをお探しですか？"
                        value="{{ request('search') }}">
                </form>
            </div>

            <nav class="navigation-buttons">
                @auth
                <a href="{{ route('logout') }}"
                    class="navigation-link"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    ログアウト
                </a>
                <a href="{{ route('profile.show') }}" class="navigation-link">マイページ</a>
                <a href="{{ route('items.create') }}" class="button-primary">出品</a>

                <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="hidden-form">
                    @csrf
                </form>
                @else
                <a href="{{ route('login') }}" class="navigation-link">ログイン</a>
                <a href="{{ route('register') }}" class="navigation-link">マイページ</a>
                <a href="{{ route('login') }}" class="button-primary">出品</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="main-content">
        @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert-error">
            {{ session('error') }}
        </div>
        @endif

        @yield('content')
    </main>

    <!-- 各ページ専用JS -->
    @yield('scripts')
    @stack('scripts')
</body>

</html>