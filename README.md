# [CT] お問い合わせフォーム

## 概要

## ER図
```mermaid
erDiagram
    users {
        bigint id PK
        varchar(255) name
        varchar(255) email "UNIQUE"
        timestamp email_verified_at "nullable"
        varchar(255) password
        varchar(100) remember_token "nullable"
        timestamp created_at
        timestamp updated_at
    }
    
    categories {
        bigint id PK
        varchar(255) content
        timestamp created_at
        timestamp updated_at
    }
    
    contacts {
        bigint id PK
        bigint category_id FK "ON DELETE CASCADE"
        varchar(255) first_name
        varchar(255) last_name
        tinyint gender "1:男性, 2:女性, 3:その他"
        varchar(255) email
        varchar(11) tel "10~11桁ハイフンなし"
        varchar(255) address
        varchar(255) building "nullable"
        varchar(120) detail
        timestamp created_at
        timestamp updated_at
    }
    
    tags {
        bigint id PK
        varchar(50) name
        timestamp created_at
        timestamp updated_at
    }
    
    contact_tag {
        bigint id PK
        bigint contact_id FK "ON DELETE CASCADE"
        bigint tag_id FK "ON DELETE CASCADE"
        timestamp created_at
        timestamp updated_at
    }
    
    categories ||--o{ contacts : "has many"
    contacts ||--o{ contact_tag : "has many"
    tags ||--o{ contact_tag : "has many"
```
※ `contact_tag`テーブルは複合制約`UNIQUE(contact_id, tag_id)`が付きます

## 環境構築手順

## 使用技術

## APIエンドポイント一覧
| メソッド   | URI                          | 概要                             | 認証 |
|:-------|:-----------------------------|:-------------------------------|:---|
| GET    | `/api/v1/contacts`           | お問い合わせ一覧取得<br/>（検索・ページネーション付き） | 不要 |
| GET    | `/api/v1/contacts/{contact}` | お問い合わせ詳細取得<br/>（カテゴリ・タグ含む）     | 不要 |
| POST   | `/api/v1/contacts`           | お問い合わせ新規作成                     | 不要 |
| PUT    | `/api/v1/contacts/{contact}` | お問い合わせ詳細更新                     | 不要 |
| DELETE | `/api/v1/contacts/{contact}` | お問い合わせ詳細削除                     | 不要 |
[注意事項] すべてのリクエストにおいて、ヘッダーに `Accept: application/json` を含めてください。

## 開発環境URL
`http://localhost:80`

## 作成者
