<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoadUserData
{
    /**
     * Handle an incoming request.
     *
     * 認証済みのユーザーのロールを含むユーザーデータをロードし、
     * Inertia.jsを介してフロントエンドに共有する。なお、未認証の場合は何もしない。
     *
     * @param  \Illuminate\Http\Request  $request  リクエストインスタンス
     * @param  \Closure  $next  次のミドルウェアを実行するクロージャ
     * @return mixed  レスポンスオブジェクトまたはリダイレクト
     */
    public function handle(Request $request, Closure $next)
    {
        // ユーザーが認証されているかチェック
        if (Auth::check()) {
            // 認証されたユーザーを取得し、そのロールをロードする
            $user = Auth::user()->load(['roles' => function ($query) {
                $query->select('id', 'name');
            }]);

            // Inertia.jsを使用してフロントエンドにログインユーザーのデータを共有する
            Inertia::share('loginUser', $user);
        }

        // 次のミドルウェアへリクエストを進める
        return $next($request);
    }
}
