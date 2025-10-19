# COACHTECH フリマ

COACHTECHが開発した独自のフリマアプリケーション。  
商品の出品、購入、いいね、コメント機能を備えたフリマサービス。

---

## 環境構築

### Dockerビルド
```bash
git clone https://github.com/Kumicho-naka/coachtech-flea-market.git
cd coachtech-flea-market
docker-compose up -d --build
```

### Laravel環境構築
```bash
docker-compose exec php bash
composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate
php artisan db:seed
```

### .env設定（重要）

`.env`ファイルの以下の項目を設定してください：

#### Stripe設定（決済機能に必須）
```env
STRIPE_KEY=pk_test_xxxxx
STRIPE_SECRET=sk_test_xxxxx
```

> **Stripeのテストキー取得方法**:
> 1. https://dashboard.stripe.com/register でアカウント作成（無料）
> 2. 「開発者」→「APIキー」からテストモードのキーをコピー
> 3. `.env`の`STRIPE_KEY`と`STRIPE_SECRET`に貼り付け

#### Mailtrap設定（メール認証機能を使用する場合）
```env
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
```

> **Mailtrapの設定方法**:
> 1. https://mailtrap.io/ でアカウント作成（無料）
> 2. Inboxを作成
> 3. 「SMTP Settings」から認証情報をコピー
> 4. `.env`に貼り付け

設定後、キャッシュをクリア：
```bash
php artisan config:clear
```

---

## 使用技術（実行環境）
- PHP 8.1.33
- Laravel 8.83.29
- MySQL 8.0.26
- nginx 1.21.1
- Docker

**認証**
- Laravel Fortify 1.19.1

**決済**
- Stripe API (stripe-php 18.0.0)

**メール送信**
- Mailtrap

---

## ER図
![ER図](docs/erd.png)

---

## URL
- 開発環境：http://localhost/
- phpMyAdmin：http://localhost:8080/

---

## アカウント情報
テスト用アカウント:
- メールアドレス: test@example.com
- パスワード: password

---

## 機能確認

### 基本機能
1. **会員登録**: http://localhost/register
2. **ログイン**: http://localhost/login
3. **商品一覧**: http://localhost/
4. **商品検索**: ヘッダーの検索ボックスで「マイク」など検索
5. **商品出品**: ログイン後、「出品」ボタンから出品

### 決済機能（Stripe）
1. 商品詳細ページで「購入手続きへ」
2. 支払い方法で「カード支払い」を選択
3. Stripe決済画面でテストカード情報を入力:
   - カード番号: `4242 4242 4242 4242`
   - 有効期限: `12/34`
   - CVC: `123`
4. 決済完了後、商品一覧で「Sold」表示を確認

### コンビニ支払いのテスト（任意）
コンビニ支払いの完全な動作確認には、Stripe CLIが必要です:

```bash
# Stripe CLIでWebhookを転送
docker run --rm -it \
  -v ~/.config/stripe:/root/.config/stripe \
  stripe/stripe-cli:latest listen --forward-to host.docker.internal/webhook/stripe
```

出力された`whsec_xxxxx`を`.env`の`STRIPE_WEBHOOK_SECRET`に設定後、コンビニ支払いをテストできます。

> **注意**: Stripe CLIなしでもカード支払いで全機能の確認が可能です。

---

## テストの実行
```bash
php artisan test
```