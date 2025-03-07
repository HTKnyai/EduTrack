<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Journal;
use Carbon\Carbon; //追記　現在時刻を入れる

class JournalsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $subjects = ['数学', '物理', '英語', '化学', '歴史'];

        for ($i = 0; $i < 7; $i++) {
            foreach ($users as $user) {
                $date = Carbon::now()->subDays($i);

                Journal::create([
                    'user_id' => $user->id,
                    'start_time' => $date->copy()->setTime(19, rand(0, 59)),
                    'end_time' => $date->copy()->setTime(20, rand(0, 59)),
                    'duration' => rand(30, 90),
                    'goals' => $subjects[array_rand($subjects)] . 'の復習',
                    'learnings' => '公式を理解するために演習問題を解いた',
                    'questions' => 'もっと効率よく暗記する方法はあるか？'
                ]);
            }
        }

        $startTime = Carbon::now()->subMinutes(10); // 10分前に学習開始
        $endTime = Carbon::now(); // 今学習終了
        $duration = $endTime->diffInSeconds($startTime); // 差分を秒数で取得

        DB::table('journals')->insert([
            'user_id' => 1,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration' => $duration,
            'goals'=>'まずは10分',
            'learnings'=>'三角比の公式',
            'questions'=>'公式の暗記法',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now(),
        ]);
        
    }
}
