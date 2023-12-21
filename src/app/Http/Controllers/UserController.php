<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request  $request)
    {
        // リクエストに'clear'が含まれている場合、セッションの検索条件をクリアする
        if ($request->has('clear')) {
            session()->forget(['searchKey', 'selectedRoles']);
        }

        // リクエストに基づいて検索セッションを更新する
        $this->updateSearchSession($request);

        // Userモデルを使用して、リクエストに基づいたユーザー検索とフィルタリングを実行する
        $users = User::searchAndFilter($request);

        // 検索結果の総数を含むメッセージとステータス情報をセッションのフラッシュデータに追加する
        session()->flash('message', "{$users->total()}件ヒットしました。");
        session()->flash('status', 'info');

        // Inertiaを使用して、ユーザーデータとロールデータを含むビューをレンダリングする
        // また、セッションのフラッシュデータをpropsとしてビューに渡す
        return Inertia::render(
            'Users/Index',
            [
                'users' => $users,
                'roles' => Role::select('id', 'name')->get(),
                'flash' => [
                    'message' => session('message'),
                    'status' => session('status')
                ],
            ]
        );
    }

    /**
     * リクエストに基づいて検索セッションを更新します。
     *
     * リクエストに 'search' または 'roles' パラメータが含まれている場合、これらの値をセッションに保存します。
     * もしリクエストにこれらのパラメータが含まれていない場合は、以前のセッション値をリクエストにマージします。
     * これにより、ユーザーの検索状態を維持します。
     *
     * @param Request $request ユーザーからのリクエスト。
     * @return void このメソッドは戻り値を持ちません。
     */
    private function updateSearchSession(Request $request)
    {
        if ($request->filled('search') || $request->filled('roles')) {
            // リクエストから検索条件を取得してセッションに保存
            session([
                'searchKey' => $request->input('search'),
                'selectedRoles' => $request->input('roles'),
            ]);
        } else {
            // セッションから検索条件を取得してリクエストにマージ
            $searchKey = session('searchKey', '');
            $selectedRoles = session('selectedRoles', []);
            $request->merge([
                'search' => $searchKey,
                'roles' => $selectedRoles,
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return Inertia::render(
            'Users/Show',
            [
                'user' => User::withRolesOnlyIdAndName()->findOrFail($user->id),
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return Inertia::render(
            'Users/Edit',
            [
                'user' => User::withRolesOnlyIdAndName()->findOrFail($user->id),
                'roles' => Role::select('id', 'name')->get(),
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        DB::transaction(function () use ($request, $user) {
            // ユーザーの基本情報を更新する
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // roles 配列を取得して、そのIDを使用して役割を更新する
            $roles = $request->roles;
            $roleIds = array_keys(array_filter($roles));
            $user->roles()->sync($roleIds);
        });

        return to_route('users.show', compact('user'))->with([
            'message' => 'ユーザ情報を更新しました。',
            'status' => 'success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();

            return to_route('users.index')->with([
                'message' => 'ユーザを削除しました。',
                'status' => 'danger',
            ]);
        } catch (\Exception $e) {
            return to_route('users.index')->with([
                'message' => 'ユーザの削除に失敗しました。',
                'status' => 'warning',
            ]);
        }
    }
}
