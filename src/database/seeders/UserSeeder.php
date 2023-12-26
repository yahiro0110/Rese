<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 初期データの定義
        $initialUsers = [
            [
                'name' => 'マネージャ',
                'email' => 'manager@example.com',
                'password' => Hash::make('manager123')
            ],
            [
                'name' => 'スタッフ',
                'email' => 'staff@example.com',
                'password' => Hash::make('staff123')
            ]
        ];

        // 各初期データを挿入する
        foreach ($initialUsers as $userData) {

            // ユーザーを作成する
            $user = User::create($userData);

            // ロールデータを取得する
            $roles = Role::all();

            // ユーザにランダムなロールを割り当てる
            $user->roles()->attach(
                $roles->random(rand(1, $roles->count()))->pluck('id')->toArray()
            );
        }

        // ファクトリーを使った追加データの生成
        User::factory()->count(58)->create()->each(function ($user) use ($roles) {
            $user->roles()->attach(
                $roles->random(rand(1, $roles->count()))->pluck('id')->toArray()
            );
        });
    }
}
