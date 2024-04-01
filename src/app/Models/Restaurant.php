<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Restaurant extends Model
{
    use HasFactory;

    // --------------------------------------------------------------------------------
    // モデル属性とリレーションシップ
    // --------------------------------------------------------------------------------

    /**
     * マスアサインメントで使用可能な属性。
     *
     * この属性リストを通じて、createやupdateメソッドなどで一括して割り当て可能なモデルの属性を定義する。
     * - `name` : 店舗の名前
     * - `tel` : 店舗の電話番号
     * - `email` : 店舗のメールアドレス
     * - `postal` : 店舗の郵便番号７桁
     * - `address` : 店舗の住所
     * - `description` : 店舗の説明
     * - `restaurant_image : 店舗の画像パス
     * - `prefecture_id` : 店舗の地域ID
     * - `genre_id` : 店舗のジャンルID
     * - `user_id` : 店舗代表者ID
     *
     * @var array
     */
    protected $fillable = ['name', 'tel', 'email', 'postal', 'address', 'description', 'restaurant_image', 'prefecture_id', 'genre_id', 'user_id'];

    /**
     * 店舗に関連する地域を取得する。
     *
     * このメソッドは一対多のリレーションシップを表し、関連するPrefectureモデルのインスタンスを返す。
     * 店舗は `prefectures` テーブルを介して地域と関連付けられている。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prefecture()
    {
        return $this->belongsTo(Prefecture::class);
    }

    /**
     * 店舗に関連するジャンルを取得する。
     *
     * このメソッドは一対多のリレーションシップを表し、関連するGenreモデルのインスタンスを返す。
     * 店舗は `genres` テーブルを介してジャンルと関連付けられている。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    /**
     * 店舗に関連する予約情報を取得する。
     *
     * このメソッドは一対多のリレーションシップを表し、関連するScheduleモデルのインスタンスのコレクションを返す。
     * 店舗は `schedules` テーブルを介して予約情報と関連付けられている。
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schedules()
    {
        return $this->hasMany(schedule::class);
    }

    /**
     * 店舗に関連するユーザーのコレクションを取得する。
     *
     * このメソッドは多対多のリレーションシップを表し、関連するUserモデルのインスタンスのコレクションを返す。
     * ユーザーと店舗は `restaurant_user` 中間テーブルを介して関連付けられている。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'restaurant_user');
    }

    /**
     * 店舗に関連するレビュー情報を取得する。
     *
     * このメソッドは一対多のリレーションシップを表し、関連するReviewモデルのインスタンスのコレクションを返す。
     * 店舗は `reviews` テーブルを介して予約情報と関連付けられる。
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // --------------------------------------------------------------------------------
    // クエリスコープとカスタムメソッド
    // --------------------------------------------------------------------------------

    /**
     * 指定されたユーザーIDに基づいてクエリを絞り込むローカルスコープ。
     * クエリにユーザーIDの条件を追加し、そのユーザーに関連するレコードのみを取得する。
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Eloquentクエリビルダインスタンス
     * @param int $userId 絞り込むユーザーのID
     * @return \Illuminate\Database\Eloquent\Builder 指定されたユーザーIDに基づいて絞り込まれたクエリ
     */
    public function scopeOfUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * ログインしているユーザーと関連付けられたレストランにマークを付けるローカルスコープ。
     * 関連したレストランがあれば、 `user_attached` に `1` を設定し、なければ `0` を設定する。
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Eloquentクエリビルダインスタンス
     * @param int $userId ログインユーザーのID
     * @return \Illuminate\Database\Eloquent\Builder ユーザーに関連付けられているレストランの数を含むクエリ
     */
    public function scopeWithUserAttached($query, $userId)
    {
        return $query->addSelect([
            'user_attached' => DB::table('restaurant_user')
                ->whereColumn('restaurant_user.restaurant_id', 'restaurants.id')
                ->where('user_id', $userId)
                ->selectRaw('count(user_id)')
                ->limit(1)
        ]);
    }
}
