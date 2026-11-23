<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License


# COACHTECH お問い合わせフォーム

## 概要
お問い合わせの送信・確認から、管理者による問い合わせ一覧の確認・検索・エクスポート、タグ管理、および Contact API による連携までを行う Web アプリケーションです。

### 主な実装機能
- **お問い合わせフォーム機能**: 入力画面、確認画面、送信処理、フォームリクエストバリデーション
- **管理画面（`/admin`）**: お問い合わせ一覧の表示、絞り込み検索、詳細確認、エクスポート機能
- **タグ管理機能**: タグの作成・編集・削除（多対多リレーションによる紐付け）
- **Contact API**: お問い合わせ関連の API エンドポイントおよび Feature / Unit テスト

---

## ER図

```mermaid
erDiagram
    CONTACTS ||--o{ CONTACT_TAGS : "has"
    TAGS ||--o{ CONTACT_TAGS : "has"
    CONTACTS {
        bigint id PK
        string last_name
        string first_name
        tinyint gender
        string email
        string tel
        string address
        string building
        text detail
        timestamp created_at
        timestamp updated_at
    }
    TAGS {
        bigint id PK
        string name
        timestamp created_at
        timestamp updated_at
    }
    CONTACT_TAGS {
        bigint id PK
        bigint contact_id FK
        bigint tag_id FK
        timestamp created_at
        timestamp updated_at
    }
使用技術
フレームワーク / 言語: Laravel 10 / PHP

フロントエンド: Blade / Tailwind CSS / Vite / JavaScript

データベース: MySQL（Docker Compose / Sail 環境）

インフラ・開発環境: Docker / Laravel Sail / phpMyAdmin

テスト: PHPUnit（Unit / Feature テスト）

開発環境URL
アプリケーション: http://localhost（または http://100.115.92.201）

phpMyAdmin: http://localhost:8080

環境構築手順リポジトリのクローンBashgit clone [https://github.com/k2z2y3/contact-form-app](https://github.com/k2z2y3/contact-form-app)
cd contact-form-app
環境変数ファイルの作成Bashcp .env.example .env
必要に応じて .env のデータベース設定や APP_URL を調整してください。依存パッケージのインストール＆コンテナ起動Bashcomposer install
./vendor/bin/sail up -d
アプリケーションキーの生成・マイグレーション・シーディングBash./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
フロントエンドアセットのビルドBash./vendor/bin/sail npm install
./vendor/bin/sail npm run build
テストの実行Bash./vendor/bin/sail artisan test
APIエンドポイント一覧メソッドパス概要GET/api/user認証済みユーザー情報の取得（auth:sanctum）GET/api/v1/contactsお問い合わせ一覧の取得POST/api/v1/contactsお問い合わせの新規作成GET/api/v1/contacts/{contact}指定したお問い合わせの詳細取得PUT / PATCH/api/v1/contacts/{contact}指定したお問い合わせの更新DELETE/api/v1/contacts/{contact}指定したお問い合わせの削除作成者

作成者　伊藤はるか


The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
