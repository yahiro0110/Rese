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

            if ($userData['name'] === 'マネージャ') {
                // マネージャーには全てのロールを割り当てる
                $user->roles()->attach($roles->pluck('id')->toArray());
            } else {
                // スタッフには「利用者」のロールを割り当てる
                $user->roles()->attach($roles->find(3));
            }
        }

        // ファクトリーを使った追加データの生成
        User::factory()->count(58)->create()->each(function ($user) use ($roles) {
            $user->roles()->attach(
                $roles->random(rand(1, $roles->count()))->pluck('id')->toArray()
            );
        });
    }
}
