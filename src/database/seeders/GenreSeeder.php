<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 初期データの定義（シンプルな文字列リスト）
        $initialGenreNames = ['寿司', '焼肉', '居酒屋', 'ラーメン', 'カフェ', 'イタリアン', '中華'];

        // 配列を変換して一括挿入
        $initialGenres = array_map(function ($name) {
            return ['name' => $name];
        }, $initialGenreNames);

        Genre::insert($initialGenres);
    }
}
