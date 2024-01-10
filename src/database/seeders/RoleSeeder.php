<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 初期データの定義（シンプルな文字列リスト）
        $initialRoleNames = ['admin', 'manager', 'user'];

        // 配列を変換して一括挿入
        $initialRoles = array_map(function ($name) {
            return ['name' => $name];
        }, $initialRoleNames);

        Role::insert($initialRoles);
    }
}
