<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * ユーザーに関連する役割のコレクションを取得します。
     *
     * このメソッドは多対多のリレーションシップを表し、関連するRoleモデルのインスタンスのコレクションを返します。
     * ユーザーと役割は `role_user` 中間テーブルを介して関連付けられています。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * ユーザーに関連するロールのIDと名前のみを取得するローカルスコープ
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRolesOnlyIdAndName($query)
    {
        return $query->with(['roles' => function ($query) {
            $query->select('id', 'name');
        }]);
    }

    public function scopeSearchKey($query, $input = null)
    {
        if (!empty($input)) {
            $likeInput = '%' . $input . '%';
            if ($this::where('name', 'LIKE', $likeInput)->orwhere('email', 'LIKE', $likeInput)->exists()) {
                return $query->where('name', 'LIKE', $likeInput)->orwhere('email', 'LIKE', $likeInput);
            }
        }
    }

    /**
     * ロールによるフィルタリングのローカルスコープ
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $roles
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRoles($query, $roles)
    {
        if (is_array($roles) && count($roles) > 0) {
            $roles = Arr::flatten($roles);
            return $query->whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('name', $roles);
            });
        }

        return $query;
    }
}
