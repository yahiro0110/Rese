<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    // --------------------------------------------------------------------------------
    // モデル属性とリレーションシップ
    // --------------------------------------------------------------------------------

    /**
     * マスアサインメントで使用可能な属性。
     *
     * この属性リストを通じて、createやupdateメソッドなどで一括して割り当て可能なモデルの属性を定義する。
     * - `user_id` : 投稿者ID
     * - `restaurant_id` : 店舗ID
     * - `rating` : 評価数
     * - `title` : タイトル
     * - `comment` : レビュー内容
     *
     * @var array
     */
    protected $fillable = ['user_id', 'restaurant_id', 'rating', 'title', 'comment'];

    /**
     * このモデルが所属するユーザを取得する。
     *
     * このメソッドは一対多のリレーションシップの逆向きを表し、所属するUserモデルのインスタンスを返す。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * このモデルが所属する店舗を取得する。
     *
     * このメソッドは一対多のリレーションシップの逆向きを表し、所属するRestaurantモデルのインスタンスを返す。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * レビューに関連するレビュー画像を取得する。
     *
     * このメソッドは一対多のリレーションシップを表し、関連するReviewImageモデルのインスタンスのコレクションを返す。
     * ユーザは `review_images` テーブルを介してレビュー画像と関連付けられる。
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviewImages()
    {
        return $this->hasMany(ReviewImage::class);
    }
}
