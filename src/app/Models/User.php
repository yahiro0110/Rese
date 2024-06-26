<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    // --------------------------------------------------------------------------------
    // モデル属性とリレーションシップ
    // --------------------------------------------------------------------------------

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
     * ユーザーに関連する役割のコレクションを取得する。
     *
     * このメソッドは多対多のリレーションシップを表し、関連するRoleモデルのインスタンスのコレクションを返す。
     * ユーザーと役割は `role_user` 中間テーブルを介して関連付けられる。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * ユーザーに関連する店舗のコレクションを取得する。
     *
     * このメソッドは多対多のリレーションシップを表し、関連するRestaurantモデルのインスタンスのコレクションを返す。
     * ユーザーと店舗は `restaurant_user` 中間テーブルを介して関連付けられる。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'restaurant_user');
    }

    /**
     * ユーザーに関連する予約情報を取得する。
     *
     * このメソッドは一対多のリレーションシップを表し、関連するScheduleモデルのインスタンスのコレクションを返す。
     * ユーザは `schedules` テーブルを介して予約情報と関連付けられる。
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * ユーザーに関連するレビュー情報を取得する。
     *
     * このメソッドは一対多のリレーションシップを表し、関連するReviewモデルのインスタンスのコレクションを返す。
     * ユーザは `reviews` テーブルを介して予約情報と関連付けられる。
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
     * ユーザーのクエリにロールのIDと名前のみを含めるローカルスコープ。
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Eloquentのクエリビルダーインスタンス
     * @return \Illuminate\Database\Eloquent\Builder 更新されたクエリビルダーインスタンス
     */
    public function scopeWithRolesOnlyIdAndName($query)
    {
        return $query->with(['roles' => function ($query) {
            $query->select('id', 'name');
        }]);
    }

    /**
     * 検索キーワードに基づいてユーザーをフィルタリングするローカルスコープ。
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Eloquentのクエリビルダーインスタンス
     * @param string|null $input 検索キーワード
     * @return \Illuminate\Database\Eloquent\Builder 更新されたクエリビルダーインスタンス
     */
    public function scopeSearchKey($query, $input = null)
    {
        if (!empty($input)) {
            $likeInput = '%' . $input . '%';
            return $query->where(function ($filterQuery) use ($likeInput) {
                $filterQuery->where('name', 'LIKE', $likeInput)
                    ->orWhere('email', 'LIKE', $likeInput);
            });
        }

        return $query;
    }

    /**
     * ロールによるフィルタリングを適用するローカルスコープ。
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Eloquentのクエリビルダーインスタンス
     * @param array|string $roles フィルタリングに使用するロール
     * @return \Illuminate\Database\Eloquent\Builder 更新されたクエリビルダーインスタンス
     */
    public function scopeWithRoles($query, $roles)
    {
        if (is_array($roles) && count($roles) > 0) {
            $roles = Arr::flatten($roles);
            foreach ($roles as $role) {
                $query->whereHas('roles', function ($filterQuery) use ($role) {
                    $filterQuery->where('name', $role);
                });
            }
        }

        return $query;
    }

    // --------------------------------------------------------------------------------
    // 静的ヘルパーメソッド
    // --------------------------------------------------------------------------------

    /**
     * 検索キーワードとロールに基づいてユーザーを検索しフィルタリングするメソッド。
     *
     * @param \Illuminate\Http\Request $request リクエストデータ
     * @return \Illuminate\Pagination\LengthAwarePaginator ページネーションされたユーザーのコレクション
     */
    public static function searchAndFilter($request)
    {
        return self::searchKey($request->search)
            ->WithRoles($request->roles)
            ->select('id', 'name', 'email')
            ->paginate(10)
            ->withQueryString();
    }
}
