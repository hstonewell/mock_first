# Atte(アット・勤怠管理システム)
勤怠管理アプリケーションです。
会員登録後、勤務開始/終了および休憩開始/終了を記録することができます。

![alt](atte_top.png)

## 作成した目的
勉強中のフレームワーク（Laravel）のアウトプット

## 機能一覧
ログイン機能、勤務/休憩時間管理、日付別勤怠一覧表示、ユーザー別勤怠一覧表示

## 使用技術(実行環境)
- PHP7.4.9
- Laravel8.83.27
- MySQL8.0.26

## テーブル設計
![alt](table.png)

## ER図
![alt](er.png)

## 環境構築
**Dockerビルド**
1. `git clone git@github.com:hstonewell/mock_first.git`
2. DockerDesktopアプリを立ち上げる
3. `docker-compose up -d --build`

**Laravel環境構築**
1. `docker-compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成
4. .envに以下の環境変数を追加
``` text
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
5. アプリケーションキーの作成
``` bash
php artisan key:generate
```

6. マイグレーションの実行
``` bash
php artisan migrate
```

7. シーディングの実行
``` bash
php artisan db:seed
```

## URL
- 開発環境：http://localhost/
- phpMyAdmin:：http://localhost:8080/
