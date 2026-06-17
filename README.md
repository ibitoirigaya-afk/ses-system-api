# SES System API

SES/BP営業管理システムのバックエンドAPIです。
Laravel API + PostgreSQL + Docker Compose で構成しています。

Reactフロントエンド `ses-system-react` から利用するAPIサーバーです。

## 使用技術

* PHP
* Laravel
* PostgreSQL
* Docker / Docker Compose
* PHPUnit
* Makefile

## 構成

このシステムは、API側とReact側を別リポジトリで管理しています。

### API側

```txt
ses-system-api
```

役割：

* Laravel API
* PostgreSQL接続
* 認証API
* 案件・要員・スキル・提案履歴・稼働実績のCRUD API
* 論理削除・復元
* Featureテスト
* 初期ユーザーSeeder

起動URL：

```txt
http://localhost:8000
```

API例：

```txt
http://localhost:8000/api/skills
```

### React側

```txt
ses-system-react
```

役割：

* 画面表示
* 入力フォーム
* 一覧・詳細・編集画面
* Laravel API との通信

起動URL：

```txt
http://localhost:5173
```

## 主な機能

* ログインAPI
* 新規登録API
* ログアウトAPI
* ログイン中ユーザー取得API
* スキル管理API
* 案件管理API
* 要員管理API
* 提案履歴管理API
* 稼働実績管理API
* 論理削除
* 復元
* 初期ユーザーSeeder
* Featureテスト

## 認証API

現在は学習・開発用の簡易認証です。

使用API：

```txt
POST /api/login
POST /api/register
GET  /api/me
POST /api/logout
```

### ログイン

```txt
POST /api/login
```

リクエスト例：

```json
{
  "email": "admin@example.com",
  "password": "password"
}
```

レスポンス例：

```json
{
  "user": {
    "id": 1,
    "name": "管理者",
    "email": "admin@example.com",
    "role": "admin"
  }
}
```

### 新規登録

```txt
POST /api/register
```

リクエスト例：

```json
{
  "name": "山田 太郎",
  "email": "yamada@example.com",
  "password": "password",
  "password_confirmation": "password",
  "role": "user"
}
```

新規登録で作成できるロール：

```txt
user
company
```

`admin` は新規登録画面から作成しない方針です。

### ログイン中ユーザー取得

```txt
GET /api/me?user_id=1
```

レスポンス例：

```json
{
  "user": {
    "id": 1,
    "name": "管理者",
    "email": "admin@example.com",
    "role": "admin"
  }
}
```

### ログアウト

```txt
POST /api/logout
```

レスポンス例：

```json
{
  "message": "ログアウトしました。"
}
```

## 初期ログインユーザー

開発用に以下のユーザーをDBへ作成して使用します。

```txt
管理者：admin@example.com / password
要員担当：user@example.com / password
企業担当：company@example.com / password
```

初期ユーザーは `UserSeeder` で作成します。

```bash
make seed
```

または：

```bash
docker compose exec api php artisan db:seed
```

DBを作り直して初期ユーザーも入れ直す場合：

```bash
docker compose exec api php artisan migrate:fresh --seed
```

## ロール

### admin

管理者ユーザーです。

* 案件管理
* 要員管理
* スキル管理
* 提案履歴管理
* 稼働実績管理
* ダッシュボード表示

### user

要員担当ユーザーです。

* 案件一覧確認
* 自分の要員管理
* 提案履歴確認

### company

企業ユーザーです。

* 自社案件管理
* 案件マッチング
* 提案履歴確認

## セットアップ

### 1. 環境変数ファイルを作成

```bash
cp .env.example .env
```

`.env.example` の主なDB設定：

```env
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=ses_system
DB_USERNAME=ses_user
DB_PASSWORD=ses_password
```

### 2. Dockerをビルドして起動

```bash
make build
```

または：

```bash
docker compose up -d --build
```

### 3. コンテナ状態確認

```bash
make ps
```

### 4. アプリケーションキーを作成

```bash
docker compose exec api php artisan key:generate
```

### 5. マイグレーション実行

```bash
make migrate
```

または：

```bash
docker compose exec api php artisan migrate
```

### 6. 初期ユーザー作成

```bash
make seed
```

### 7. 起動確認

```txt
http://localhost:8000
```

API確認：

```bash
curl http://127.0.0.1:8000/api/skills
```

## Docker構成

API側は Laravel API + PostgreSQL を Docker Compose で起動します。

主なコンテナ：

```txt
ses-system-api
ses-system-postgres
```

ポート：

```txt
Laravel API: http://localhost:8000
PostgreSQL: localhost:5433
```

LaravelコンテナからPostgreSQLへ接続する場合：

```env
DB_HOST=postgres
DB_PORT=5432
```

Mac本体からPostgreSQLを見る場合：

```txt
localhost:5433
```

DB接続確認：

```bash
make db-shell
```

注意：

```txt
Mac本体で php artisan serve を起動しない方針です。
8000番は Docker 側の Laravel API が使用します。
```

## Makefile コマンド

### Docker起動

```bash
make up
```

### Docker停止

```bash
make down
```

### Dockerビルド起動

```bash
make build
```

### Docker再起動

```bash
make restart
```

### ログ確認

```bash
make logs
```

### コンテナ状態確認

```bash
make ps
```

### APIコンテナに入る

```bash
make shell
```

### PostgreSQLに入る

```bash
make db-shell
```

### マイグレーション実行

```bash
make migrate
```

### DBを作り直してマイグレーション実行

```bash
make fresh
```

### 初期データ投入

```bash
make seed
```

### Featureテスト実行

```bash
make test
```

### APIルート一覧確認

```bash
make route
```

### Laravelのキャッシュ削除

```bash
make cache-clear
```

## API一覧

### Auth

```txt
POST /api/register
POST /api/login
GET  /api/me
POST /api/logout
```

### Skills

```txt
GET    /api/skills
POST   /api/skills
GET    /api/skills/{skill}
PUT    /api/skills/{skill}
PATCH  /api/skills/{skill}
DELETE /api/skills/{skill}
```

### Projects

```txt
GET    /api/projects
POST   /api/projects
GET    /api/projects/{project}
PUT    /api/projects/{project}
PATCH  /api/projects/{project}
DELETE /api/projects/{project}
PATCH  /api/projects/{project}/restore
```

### Engineers

```txt
GET    /api/engineers
POST   /api/engineers
GET    /api/engineers/{engineer}
PUT    /api/engineers/{engineer}
PATCH  /api/engineers/{engineer}
DELETE /api/engineers/{engineer}
PATCH  /api/engineers/{engineer}/restore
```

### Proposal Histories

```txt
GET    /api/proposal-histories
POST   /api/proposal-histories
GET    /api/proposal-histories/{proposal_history}
PUT    /api/proposal-histories/{proposal_history}
PATCH  /api/proposal-histories/{proposal_history}
DELETE /api/proposal-histories/{proposal_history}
PATCH  /api/proposal-histories/{proposalHistory}/restore
```

### Work Records

```txt
GET    /api/work-records
POST   /api/work-records
GET    /api/work-records/{work_record}
PUT    /api/work-records/{work_record}
PATCH  /api/work-records/{work_record}
DELETE /api/work-records/{work_record}
PATCH  /api/work-records/{workRecord}/restore
```

## 論理削除・復元

以下の機能は論理削除と復元に対応しています。

```txt
Projects
Engineers
Proposal Histories
Work Records
```

削除：

```txt
DELETE /api/{resource}/{id}
```

復元：

```txt
PATCH /api/{resource}/{id}/restore
```

## テスト

Featureテストを用意しています。

```bash
make test
```

テスト対象：

* Skill API
* Project API
* Engineer API
* Proposal History API
* Work Record API

確認内容：

* 一覧取得
* 登録
* バリデーション
* 更新
* 削除
* 復元

## 動作確認コマンド例

### Skills API確認

```bash
curl http://127.0.0.1:8000/api/skills
```

### ログイン確認

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

### 新規登録確認

```bash
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"テスト企業","email":"test-company@example.com","password":"password","password_confirmation":"password","role":"company"}'
```

### ログイン中ユーザー確認

```bash
curl "http://127.0.0.1:8000/api/me?user_id=1"
```

### ログアウト確認

```bash
curl -X POST http://127.0.0.1:8000/api/logout
```

## 開発時の確認コマンド

作業後は以下を実行します。

```bash
make test
git status
```

ルート確認：

```bash
make route
```

## 関連フロントエンド

React側リポジトリ：

```txt
ses-system-react
```

React側では以下をLaravel APIへ接続しています。

```txt
ログイン
新規登録
案件管理
要員管理
スキル管理
提案履歴管理
稼働実績管理
```

## 現在の状態

現在は以下まで完了しています。

```txt
ログインAPI化
新規登録API化
主要CRUD API化
PostgreSQL接続
Docker起動
初期ユーザーSeeder追加
Featureテスト通過
React側との接続確認
```

## 今後の改善候補

* Laravel Sanctum などを使った本格認証
* APIレスポンス形式の統一
* 認可処理の強化
* roleごとのアクセス制御強化
* E2Eテスト追加
* API仕様書の追加
* READMEの更新継続

```
```
