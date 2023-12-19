# Rese（リーズ）

## Outline

PHP のフレームワーク Laravel で作成された Web アプリケーション（飲食店予約サービス）です。<br />
アプリケーションの詳細は Notion でまとめておりますので、[そちら](https://h-yamasita.notion.site/Rese-d233d1ed442f41aa9402e8e2fc0822af?pvs=4) をご覧ください。

## Architecture

![](./img/architecture.drawio.svg)

## Requirement

-   nginx: latest
-   php: 8.0.0
-   MySQL: 8.0.26
-   Laravel9
-   vue@3.3.10

## Setup Instructions

1.  リポジトリをクローンします。

    ```bash
    git clone git@github.com:yahiro0110/LaraBreezeDockerKit.git
    ```

2.  環境変数用のファイルを用意します。

    ```bash
    cp ./src/.env.example ./src/.env
    ```

3.  .env ファイル内のデータベース接続設定を次のように書きかえてください。

    ```markdown
    DB_CONNECTION=mysql
    DB_HOST=db
    DB_PORT=3306
    DB_DATABASE=mysql_db
    DB_USERNAME=admin
    DB_PASSWORD=secret
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

    # npmのインストール
    npm install

    #　開発サーバの実行
    npm run dev
    ```

6.  以下の URL にアクセスし、ログイン画面を表示します。

-   ログイン画面 (マネージャー用) http://localhost/login/manager

    -   メールアドレス：manager@example.com
    -   パスワード：password-manager

-   ログイン画面 (スタッフ用) http://localhost/login

    -   メールアドレス：staff@example.com
    -   パスワード：password-staff

7. もしアカウントを新規で登録したい場合は以下の URL にアクセスしてください。

-   新規登録画面 (マネージャー用) http://localhost/register/manager
-   新規登録画面 (スタッフ用) http://localhost/register

## Changelog

<details>
<summary>更新内容はこちら</summary>

### 1.0.0 （2023/09/25）

-   初回登録

</details>
