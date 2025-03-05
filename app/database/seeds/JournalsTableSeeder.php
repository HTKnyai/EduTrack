<?php

use Illuminate\Database\Seeder;

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
