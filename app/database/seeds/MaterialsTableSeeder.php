<?php

use Illuminate\Database\Seeder;
use App\User;
use Carbon\Carbon; //追記　現在時刻を入れる

class MaterialsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('materials')->insert([
            [
                'teacher_id' => 1,
                'title' => '微分積分の基礎',
                'file_path' => 'uploads/materials/calculus_basics.pdf',
                'dls' => 15,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'teacher_id' => 2,
                'title' => '物理学の基礎',
                'file_path' => 'uploads/materials/physics_basics.pdf',
                'dls' => 30,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'teacher_id' => 3,
                'title' => '英語の文法入門',
                'file_path' => 'uploads/materials/english_grammar.pdf',
                'dls' => 8,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
        ]);
    }
}
