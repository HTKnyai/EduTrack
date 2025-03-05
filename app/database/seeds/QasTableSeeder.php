<?php

use Illuminate\Database\Seeder;

use Carbon\Carbon; //追記　現在時刻を入れる

class QasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('qas')->insert([
            [
                'user_id' => 1,
                'target_id' => 2,
                'contents' => '三角比の応用問題を解くコツを教えてください。',
                'anonymize' => false,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'user_id' => 2,
                'target_id' => 1,
                'contents' => 'コサインの定理を使う場面を具体的に教えてください。',
                'anonymize' => true,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => 3,
                'target_id' => 0, // 特定の対象なし
                'contents' => '公式を暗記する良い方法はありますか？',
                'anonymize' => false,
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ],
        ]);
    }
}
