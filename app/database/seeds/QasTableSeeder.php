<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Qa;
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
                // 3つの質問を投稿
                $question1 = Qa::create([
                    'user_id' => 2,  // 学生が投稿
                    'target_id' => 0, // 新規質問
                    'contents' => '三角関数の証明が苦手です。コツはありますか？',
                    'anonymize' => false
                ]);
        
                $question2 = Qa::create([
                    'user_id' => 3,
                    'target_id' => 0,
                    'contents' => '物理の運動方程式が分かりません。',
                    'anonymize' => false
                ]);
        
                $question3 = Qa::create([
                    'user_id' => 4,
                    'target_id' => 0,
                    'contents' => '英語のリスニングを上達させるには？',
                    'anonymize' => true
                ]);
        
                // 各質問に対して回答を作成
                Qa::create([
                    'user_id' => 1, // 教師が回答
                    'target_id' => $question1->id,
                    'contents' => '基本公式を使う練習を重ねましょう！',
                    'anonymize' => false
                ]);
        
                Qa::create([
                    'user_id' => 1,
                    'target_id' => $question2->id,
                    'contents' => '具体例を使って理解すると分かりやすいですよ。',
                    'anonymize' => false
                ]);
        
                Qa::create([
                    'user_id' => 5,
                    'target_id' => $question3->id,
                    'contents' => '毎日少しずつ聞く習慣をつけるのが重要です。',
                    'anonymize' => true
                ]);

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
