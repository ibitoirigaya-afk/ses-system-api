# SES System API

SES/BP営業管理システムのバックエンドAPIです。
Laravel API + PostgreSQL + Docker Compose で構成しています。

## 使用技術

* PHP
* Laravel
* PostgreSQL
* Docker / Docker Compose
* PHPUnit

## 主な機能

* スキル管理API
* 案件管理API
* 要員管理API
* 提案履歴管理API
* 稼働実績管理API
* 論理削除・復元
* Featureテスト

## セットアップ

### 1. 環境変数ファイルを作成

```bash
cp .env.example .env
```

### 2. Dockerを起動

```bash
docker compose up --build
```

バックグラウンドで起動する場合：

```bash
docker compose up -d
```

### 3. アプリケーションキーを作成

```bash
docker compose exec app php artisan key:generate
```

### 4. マイグレーション実行

```bash
docker compose exec app php artisan migrate
```

## 起動URL

```txt
http://127.0.0.1:8000
```

API例：

```txt
http://127.0.0.1:8000/api/skills
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

### Dockerをビルドして起動

```bash
make build
```

### マイグレーション実行

```bash
make migrate
```

### DBを作り直してマイグレーション実行

```bash
make fresh
```

### Featureテスト実行

```bash
make test
```

### ルート一覧確認

```bash
make route
```

### Laravelのキャッシュ削除

```bash
make cache-clear
```

## API一覧

### Skills

```txt
GET    /api/skills
POST   /api/skills
GET    /api/skills/{skill}
PUT    /api/skills/{skill}
DELETE /api/skills/{skill}
```

### Projects

```txt
GET    /api/projects
POST   /api/projects
GET    /api/projects/{project}
PUT    /api/projects/{project}
DELETE /api/projects/{project}
PATCH  /api/projects/{project}/restore
```

### Engineers

```txt
GET    /api/engineers
POST   /api/engineers
GET    /api/engineers/{engineer}
PUT    /api/engineers/{engineer}
DELETE /api/engineers/{engineer}
PATCH  /api/engineers/{engineer}/restore
```

### Proposal Histories

```txt
GET    /api/proposal-histories
POST   /api/proposal-histories
GET    /api/proposal-histories/{proposal_history}
PUT    /api/proposal-histories/{proposal_history}
DELETE /api/proposal-histories/{proposal_history}
PATCH  /api/proposal-histories/{proposal_history}/restore
```

### Work Records

```txt
GET    /api/work-records
POST   /api/work-records
GET    /api/work-records/{work_record}
PUT    /api/work-records/{work_record}
DELETE /api/work-records/{work_record}
PATCH  /api/work-records/{work_record}/restore
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

## 関連フロントエンド

React側リポジトリ：

```txt
ses-system-react
```
