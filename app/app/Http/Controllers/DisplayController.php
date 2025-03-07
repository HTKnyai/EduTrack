<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Journal;
use App\Material;
use App\Qa;
use App\User;
use Carbon\Carbon;

class DisplayController extends Controller
{
    public function index()
    {
        // 学習ジャーナルのデータ（直近7日分）
        $journals = Journal::where('start_time', '>=', Carbon::now()->subDays(7))
                        ->orderBy('start_time')
                        ->get();
        // 前日の学習記録
        $yesterday = Carbon::yesterday();
        $yesterdayJournal = Journal::whereDate('start_time', $yesterday)
                            ->orderBy('start_time', 'desc')
                            ->first();
  
        // 直近のQ&A 5件
        $qas = Qa::with('user')->latest()->take(5)->get();

        // 直近の教材 5件
        $materials = Material::with('teacher')->latest()->take(5)->get();

        return view('dashboard', compact('journals', 'qas', 'materials', 'yesterdayJournal'));
    }

    // 学習ジャーナル一覧表示
    public function journals()
    {
        $journals = Journal::with('user')->get();
        return view('journals_create', compact('journals'));
    }

    public function weeklyData()
    {
    $weeklyData = Journal::where('created_at', '>=', now()->subDays(7))
        ->selectRaw('DATE(created_at) as date, SUM(duration) as total_duration')
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

    $labels = $weeklyData->pluck('date')->toArray();
    $durations = $weeklyData->pluck('total_duration')->map(fn($d) => round($d / 60, 1))->toArray(); // 分単位

    return response()->json([
        'labels' => $labels,
        'durations' => $durations,
    ]);
    }

    // 質問掲示板一覧表示
    public function qas_index()
    {
        $qas = Qa::with(['user', 'target', 'replies'])->get();
        return view('qas_index', compact('qas'));
    }
    /*
    public function qas()
    {
        $qas = Qa::with('user', 'target')->get();
        return view('qas_create', compact('qas'));
    }
    */

    // 教材一覧表示
    public function materials()
    {
        $materials = Material::with('teacher')->get();
        return view('materials_create', compact('materials'));
    }

    public function journals_index()
    {
        $journals = Journal::with('user')->get();
        return view('journals_index', compact('journals'));
    }
    /*不使用
    public function qas_index() 
    {
        $qas = Qa::with('user', 'target')->get();
        return view('qas_index', compact('qas'));
    }
   */
    public function materials_index() 
    {
        $materials = Material::with('teacher')->get();
        return view('materials_index', compact('materials'));
    }
    
}
