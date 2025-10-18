# COACHTECH フリマ

## 目次
1. [アプリケーション概要](#アプリケーション概要)
2. [機能一覧](#機能一覧)
3. [使用技術](#使用技術)
4. [テーブル設計](#テーブル設計)
5. [環境構築](#環境構築)
6. [動作確認](#動作確認)
7. [工夫した点](#工夫した点)

---

## アプリケーション概要

**アプリケーション名**: COACHTECH フリマ

**プロジェクト概要**:  
COACHTECHが開発した独自のフリマアプリケーション。  
ユーザーは商品の出品、購入、いいね、コメント機能を利用できます。

**作成した目的**:  
初年度でのユーザー数1000人を目標とし、シンプルで使いやすいフリマサービスを提供するため。

**URL**:
- アプリケーション: http://localhost/
- phpMyAdmin: http://localhost:8080/

---

## 機能一覧

### ユーザー認証
- 会員登録・ログイン・ログアウト機能
- プロフィール管理機能

### 商品機能
- 商品一覧表示・検索機能
- 商品詳細表示機能
- 商品出品機能（画像アップロード対応）

### いいね・コメント機能
- いいね機能（Ajax実装）
- コメント投稿機能

### 購入機能
- 商品購入機能（Stripe決済対応）
- 配送先住所変更機能
- 購入履歴・出品履歴表示機能

---

## 使用技術

### バックエンド
- PHP 8.1.0
- Laravel 8.83.29
- MySQL 8.0.26

### フロントエンド
- HTML / CSS
- JavaScript (Vanilla JS)

### インフラ
- Docker / Docker Compose
- Nginx 1.21.1

### 決済
- Stripe API

---

## テーブル設計

### テーブル一覧
| テーブル名 | 説明 |
|-----------|------|
| users | ユーザー情報 |
| items | 商品情報 |
| categories | カテゴリー |
| item_categories | 商品とカテゴリーの中間テーブル |
| conditions | 商品の状態 |
| likes | いいね |
| comments | コメント |
| purchases | 購入履歴 |

詳細なテーブル設計は[ER図](./docs/erd.png)を参照してください。

### ER図
![ER図](./docs/erd.png)

---

## 環境構築

### 前提条件
- Docker Desktop がインストールされていること
- Git がインストールされていること
- Stripeアカウント（テストモード用）

---

### 1. Dockerビルド
```bash
# リポジトリのクローン
git clone <リポジトリURL>
cd coachtech-fleamarket

# Dockerコンテナの起動
docker-compose up -d --build

# PHPコンテナに入る
docker-compose exec php bash
```

---

### 2. Laravel環境構築
```bash
# Composerパッケージのインストール
composer install

# 環境設定ファイルの作成
cp .env.example .env

# アプリケーションキーの生成
php artisan key:generate
```

---

### 3. .envファイルの編集

`.env`ファイルを開き、以下の設定を記載してください：
```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

# データベース設定
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

# Stripe設定（決済機能）
STRIPE_KEY=pk_test_あなたのPublishableKey
STRIPE_SECRET=sk_test_あなたのSecretKey
```

> **Stripeテストキーの取得方法**:
> 1. [Stripe Dashboard](https://dashboard.stripe.com/register) でアカウント作成（無料）
> 2. 「開発者」→「APIキー」→「テストモードのキー」をコピー
> 3. `.env`に貼り付け

---

### 4. データベースのセットアップ
```bash
# マイグレーションの実行
php artisan migrate

# シーディングの実行
php artisan db:seed
```

---

### 5. ストレージの設定
```bash
# ストレージリンクの作成
php artisan storage:link

# 商品画像用ディレクトリの作成
mkdir -p storage/app/public/items
```

---

### 6. 商品画像の配置（任意）

ダミーデータ用の商品画像を以下のディレクトリに配置してください：
```
storage/app/public/items/
├── dummy1.jpg  # 腕時計
├── dummy2.jpg  # HDD
├── dummy3.jpg  # 玉ねぎ3束
├── dummy4.jpg  # 革靴
├── dummy5.jpg  # ノートPC
├── dummy6.jpg  # マイク
├── dummy7.jpg  # ショルダーバッグ
├── dummy8.jpg  # タンブラー
├── dummy9.jpg  # コーヒーミル
└── dummy10.jpg # メイクセット
```

> **注意**: 画像がない場合は、プレースホルダー画像が表示されます。

---

### 7. キャッシュのクリア
```bash
php artisan config:clear
php artisan cache:clear
```

---

## 動作確認

### アクセスURL
- アプリケーション: http://localhost/
- phpMyAdmin: http://localhost:8080/

### テストアカウント
- **メールアドレス**: test@example.com
- **パスワード**: password

---

### 基本的な動作確認手順

1. **会員登録**
   - http://localhost/register にアクセス
   - 新規ユーザーを登録

2. **ログイン**
   - 登録したアカウントまたはテストアカウントでログイン

3. **商品一覧の確認**
   - トップページに10件の商品が表示されることを確認

4. **商品検索**
   - 検索ボックスで「マイク」と検索
   - 該当商品が表示されることを確認

5. **いいね機能**
   - 商品詳細ページでいいねボタンをクリック
   - いいね数が増加することを確認（Ajax動作）

6. **コメント機能**
   - 商品詳細ページでコメントを投稿
   - 投稿したコメントが表示されることを確認

7. **商品出品**
   - ヘッダーの「出品」ボタンから商品を出品
   - 画像アップロード、カテゴリ選択、商品情報を入力して出品完了

### 8. 商品購入（Stripe決済）

#### カード支払い
1. 商品詳細ページから「購入手続きへ」をクリック
2. 支払い方法で「カード支払い」を選択
3. 「購入する」ボタンをクリック
4. Stripe決済画面でテストカード情報を入力
   - カード番号: 4242 4242 4242 4242
   - 有効期限: 12/34
   - CVC: 123
5. 購入完了

### 8. 商品購入（Stripe決済）

1. 商品詳細ページから「購入手続きへ」をクリック
2. 支払い方法で「カード支払い」または「コンビニ支払い」を選択
3. 配送先住所を確認
4. 「購入する」ボタンをクリック
5. Stripe決済画面に遷移（どちらの支払い方法でも同じカード決済画面）
6. テストカード情報を入力：
   - **カード番号**: 4242 4242 4242 4242
   - **有効期限**: 12/34（任意の未来の日付）
   - **CVC**: 123（任意の3桁）
7. 決済完了後、商品一覧ページに遷移
8. マイページの購入履歴で、選択した支払い方法が正しく記録されていることを確認

> **注意**: 開発環境では両方の支払い方法でカード決済画面に遷移しますが、
> 選択した支払い方法（コンビニ支払い/カード支払い）は購入履歴に正しく保存されます。

9. **プロフィール確認**
   - マイページから購入履歴・出品履歴を確認

---

## 工夫した点

### 1. JavaScriptによるインタラクティブな実装
- **いいね機能**: Ajaxを使用して、ページ遷移なしでいいねの追加・削除が可能
- **カスタムドロップダウン**: selectタグではなく、カスタムUIを実装
- **画像プレビュー機能**: 商品出品時にアップロード前の画像をプレビュー表示

### 2. Stripe決済の実装
- Stripe Checkoutを使用した決済機能を実装
- 「カード支払い」「コンビニ支払い」の両方の支払い方法を選択可能
- 選択した支払い方法は`purchases`テーブルに記録され、購入履歴で確認可能
- 開発環境の制約を考慮し、実装可能な範囲で仕様を満たす実装を行った

### 3. レスポンシブデザイン
- Figmaデザインに忠実な実装
- PC表示（1400-1540px）に最適化

### 4. ユーザビリティの向上
- 検索状態の保持（マイリストタブでも検索が維持される）
- バリデーションエラーメッセージの日本語化
- 購入済み商品の明確な表示（Soldバッジ）

### 5. セキュリティ対策
- CSRF保護の実装
- パスワードのハッシュ化
- FormRequestによる入力バリデーション

---

## トラブルシューティング

### データベース接続エラーが発生する場合
```bash
# コンテナの再起動
docker-compose restart

# キャッシュクリア
php artisan config:clear
php artisan cache:clear
```

### マイグレーションエラーが発生する場合
```bash
# データベースのリセット
php artisan migrate:fresh --seed
```

### Stripe決済エラーが発生する場合
```bash
# .envファイルのSTRIPE_KEYとSTRIPE_SECRETが正しく設定されているか確認
# キャッシュクリア
php artisan config:clear
```

### 画像が表示されない場合
```bash
# ストレージリンクの再作成
php artisan storage:link

# パーミッションの確認
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

## 開発者向け情報

### テストの実行
```bash
php artisan test
```

### ルーティング確認
```bash
php artisan route:list
```

### キャッシュクリア
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## ダミーデータ

シーディングにより以下のデータが自動登録されます：
- 商品データ：10件
- カテゴリーデータ：8件
- 商品状態データ：4件
- テストユーザー：1件

---

## 既知の制限事項

- メール認証機能は未実装です
- レスポンシブ対応は PC（1400-1540px）のみ対応しています