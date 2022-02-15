# FileShare

個人で利用するファイル共有アプリ

## 詳細

画像、動画のみブラウザ上でプレビューできる  
Vue.js や Laravel の学習を兼ねて作成した

### 主な機能

- ファイル(形式は問わない)のアップロード
  - ドラッグアンドドロップ対応
  - 100MB 以上のファイルは分割してアップロードする
- フォルダの作成
- 各コンテンツの削除, 名前の変更, ロック
- ファイルのダウンロード
- 画像, 動画ファイルのプレビュー

## 使用言語

### フロントエンド

Vue.js

### バックエンド

Laravel8

## 使用方法

```
git clone https://github.com/n0s3nsE/FileShare.git
cd FileShare
```

### フロントエンド

.env を編集する

```
cd frontend
cp .env.example .env
//.env
VUE_APP_API_BASE_URL='http://{host}/api'
```

起動

```
yarn
yarn run serve
```

### バックエンド

```
cd backend
composer install
php artisan migrate
php artisan serve
```
