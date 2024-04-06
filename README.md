# Rese（リーズ）

## Outline

PHP のフレームワーク Laravel および Vue.js で作成された Web アプリケーション（飲食店予約サービス）です。<br />
アプリケーションの詳細は Notion でまとめておりますので、[そちら](https://h-yamasita.notion.site/Rese-d233d1ed442f41aa9402e8e2fc0822af?pvs=4) をご覧ください。

## Requirement

-   nginx: latest
-   php: 8.0.0
-   MySQL: 8.0.26
-   Laravel9
-   vue@3.3.10

## Setup Instructions

1.  リポジトリをクローンします。

    ```bash
    git clone https://github.com/yahiro0110/Rese.git
    ```

2.  環境変数用のファイルを用意します。

    ```bash
    cp ./src/.env.example ./src/.env
    ```

3.  .env ファイル内のデータベース接続設定およびメール設定を次のように書きかえてください。<br />
    なお本アプリの地図表示には Google Map API ([Maps Embed API](https://developers.google.com/maps/documentation/embed/get-started?hl=ja)) を使用しております。<br />
    そのため、.env ファイル内の VITE_GOOGLE_MAPS_API_KEY には別途取得したキーをご記入ください。

    ```markdown
    DB_CONNECTION=mysql
    DB_HOST=db
    DB_PORT=3306
    DB_DATABASE=Rese_db
    DB_USERNAME=admin
    DB_PASSWORD=password123

    MAIL_MAILER=smtp
    MAIL_HOST=mail
    MAIL_PORT=1025
    MAIL_USERNAME=null
    MAIL_PASSWORD=null
    MAIL_ENCRYPTION=null
    MAIL_FROM_ADDRESS="rese@example.com"
    MAIL_FROM_NAME="${APP_NAME}"

    VITE_GOOGLE_MAPS_API_KEY=API キー
    ```

4.  Docker コンテナを起動します。

    ```bash
    docker-compose up -d --build
    ```

5.  PHP コンテナへログインし、Laravel アプリケーションの準備をします。

    ```bash
    # PHPコンテナへのログイン
    docker-compose exec php bash

    # Laravelアプリケーションの依存関係をインストール
    composer update

    # アプリケーションキーの生成
    php artisan key:generate

    # データベーステーブルの作成
    php artisan migrate

    # 初期データの投入
    php artisan db:seed

    # npmのインストール
    npm install

    #　開発サーバの実行
    npm run dev
    ```

6.  以下の URL にアクセスし、トップページを表示します。

    http://localhost/

    -   ログイン用アカウントの情報(Email, Password)
        -   管理者
            -   Email: admin@example.com
            -   Password: admin123
        -   店舗代表者
            -   Email: manager@example.com
            -   Password: manager123
        -   一般ユーザー A
            -   Email: usera@example.com
            -   Password: usera123
        -   一般ユーザー B
            -   Email: userb@example.com
            -   Password: userb123

## Connection

1. PhpMyAdmin

    http://localhost:8080/

    DB 関連の情報を閲覧できます。

2. MailHog

    http://localhost:8025/

    ローカルでの開発環境に MailHog を使用しています。<br />
    本アプリでアカウントを新規登録した場合、アカウントを使用可能にするためには認証メールのリンクをクリックする必要があります。
    認証メールは MailHog に送られますので、上記の URL からアクセスし、Inbox をご確認ください。
