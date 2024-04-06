<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewImage extends Model
{
    use HasFactory;

    // テーブル名を指定
    protected $table = 'review_images';

    // --------------------------------------------------------------------------------
    // モデル属性とリレーションシップ
    // --------------------------------------------------------------------------------

    /**
     * マスアサインメントで使用可能な属性。
     *
     * この属性リストを通じて、createやupdateメソッドなどで一括して割り当て可能なモデルの属性を定義する。
     * - `review_id` : レビューID
     * - `image_path` : 画像パス
     *
     * @var array
     */
    protected $fillable = ['review_id', 'image_path'];

    /**
     * このモデルが所属するReviewモデルのインスタンスを取得する。
     *
     * このメソッドは一対多のリレーションシップの逆向きを表し、所属するReviewモデルのインスタンスを返す。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function review()
    {
        return $this->belongsTo(Review::class);
    }
}
