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
                'name' => '管理者',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => '店舗代表者',
                'email' => 'manager@example.com',
                'password' => Hash::make('manager123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => '一般ユーザーA',
                'email' => 'usera@example.com',
                'password' => Hash::make('usera123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => '一般ユーザーB',
                'email' => 'userb@example.com',
                'password' => Hash::make('userb123'),
                'email_verified_at' => now(),
            ],
        ];

        // 各初期データを挿入する
        foreach ($initialUsers as $userData) {

            // ユーザーを作成する
            $user = User::create($userData);

            // ロールデータを取得する
            $roles = Role::all();

            if ($userData['name'] === '管理者') {
                // 管理者には「管理者」のロールを割り当てる
                $user->roles()->attach($roles->find(1));
            } elseif ($userData['name'] === '店舗代表者') {
                // マネージャーには「店舗代表者」のロールを割り当てる
                $user->roles()->attach($roles->find(2));
            } else {
                // 一般ユーザには「利用者」のロールを割り当てる
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
