<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render(
            'Users/Index',
            [
                'users' => User::select('id', 'name', 'email')->paginate(10),
            ]
        );
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
