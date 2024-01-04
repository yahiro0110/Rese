<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * 店舗に関連する地域を取得します。
     *
     * このメソッドは一対多のリレーションシップを表し、関連するPrefectureモデルのインスタンスを返します。
     * 店舗は `prefectures` テーブルを介して地域と関連付けられています。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prefecture()
    {
        return $this->belongsTo(Prefecture::class);
    }

    /**
     * 店舗に関連するジャンルを取得します。
     *
     * このメソッドは一対多のリレーションシップを表し、関連するGenreモデルのインスタンスを返します。
     * 店舗は `genres` テーブルを介してジャンルと関連付けられています。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function scopeOfUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * 店舗に関連する予約情報を取得する。
     *
     * このメソッドは一対多のリレーションシップを表し、関連するScheduleモデルのインスタンスのコレクションを返す。
     * 店舗は `schedules` テーブルを介して予約情報と関連付けられる。
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schedules()
    {
        return $this->hasMany(schedule::class);
    }
}
