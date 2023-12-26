<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * 役割に関連する役割のコレクションを取得します。
     *
     * このメソッドは多対多のリレーションシップを表し、関連するUserモデルのインスタンスのコレクションを返します。
     * ユーザーと役割は `role_user` 中間テーブルを介して関連付けられています。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }
}
