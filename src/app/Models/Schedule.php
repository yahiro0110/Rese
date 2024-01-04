<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    // --------------------------------------------------------------------------------
    // モデル属性とリレーションシップ
    // --------------------------------------------------------------------------------

    /**
     * マスアサインメントで使用可能な属性。
     *
     * この属性リストを通じて、createやupdateメソッドなどで一括して割り当て可能なモデルの属性を定義する。
     * - `date` : 店舗の予約日付
     * - `time` : 店舗の予約時間
     * - `members` : 店舗の予約人数
     * - `restaurant_id` : 店舗ID
     * - `user_id` : 予約者ID
     *
     * @var array
     */
    protected $fillable = ['date', 'time', 'members', 'restaurant_id', 'user_id'];
}
