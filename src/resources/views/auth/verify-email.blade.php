<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>メール認証 - COACHTECH フリマ</title>
    <link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="verify-email-container">
        <header class="verify-email-header">
            <a href="{{ route('items.index') }}" class="verify-email-logo">
                <img src="{{ asset('logo.svg') }}" alt="COACHTECH">
            </a>
        </header>

        <div class="verify-email-content">
            <div class="verification-message">
                <h2>
                    登録していただいたメールアドレスに認証メールを送付しました。<br>
                    メール認証を完了してください。
                </h2>
            </div>

            <div class="verification-actions">
                @if (session('resent'))
                <div class="resent-message">
                    認証メールを再送信しました。
                </div>
                @endif

                <form method="POST" action="{{ route('verification.send') }}" class="resend-form">
                    @csrf
                    <button type="submit" class="button-resend">
                        認証メールを再送する
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>