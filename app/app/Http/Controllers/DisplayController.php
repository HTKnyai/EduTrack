<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
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
        $user = auth()->user();
    
        if ($user->role === 0) { // 👈 生徒のみデータ取得
            // 直近7日間の学習データ（日ごとに合計）
            $weeklyData = Journal::where('user_id', $user->id)
                ->where('start_time', '>=', Carbon::now()->subDays(7))
                ->selectRaw('DATE(start_time) as date, SUM(duration) as total_duration')
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
    
            // 前日の学習記録（複数ある場合も合計）
            $yesterday = Carbon::yesterday();
            $yesterdayJournal = Journal::where('user_id', $user->id)
                ->whereDate('start_time', $yesterday)
                ->selectRaw('SUM(duration) as total_duration, GROUP_CONCAT(learnings SEPARATOR ", ") as learnings, GROUP_CONCAT(questions SEPARATOR ", ") as questions')
                ->first();
        } else {
            // 教師の場合は生徒のデータは不要
            $weeklyData = collect([]);
            $yesterdayJournal = null;
        }
    
        // **質問のみ（target_id = 0）を直近5件取得**
        $qas = Qa::with('user')
            ->where('target_id', 0)
            ->latest()
            ->take(5)
            ->get();
    
        // 直近の教材 5件
        $materials = Material::with('teacher')->latest()->take(5)->get();
    
        return view('dashboard', compact('weeklyData', 'qas', 'materials', 'yesterdayJournal'));
    }

// 学習ジャーナル一覧表示
public function journals()
{
    $journals = Journal::with('user')->orderBy('start_time', 'desc')->get();

    // 直近1週間分のデータを取得し、日ごとの合計学習時間を計算
    $oneWeekAgo = Carbon::now()->subDays(7)->startOfDay();
    $weeklyData = Journal::where('start_time', '>=', $oneWeekAgo)
        ->select(DB::raw('DATE(start_time) as date'), DB::raw('SUM(duration) as total_duration'))
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

    // データをビューへ渡す
    return view('journals_index', compact('journals', 'weeklyData'));
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
    public function qas_index(Request $request)
    {
        $query = Qa::with(['user', 'target', 'replies']);
    
        // 🔍 キーワード検索（質問・回答の内容）
        if ($request->filled('keyword')) {
            $query->where('contents', 'like', '%' . $request->keyword . '%');
        }
    
        // 🔍 投稿者名検索（匿名を除外する）
        if ($request->filled('user')) { // 🔹 フォームの name="user" に対応
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user . '%');
            })->where('anonymize', '=', 0); // 🔹 匿名投稿を確実に除外
        }
    
        // 🔍 日付検索（開始日 & 終了日）
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
    
        // ✅ 検索結果を取得（新しい投稿が上にくるように）
        $qas = $query->orderBy('created_at', 'desc')->paginate(10);
    
        return view('qas_index', compact('qas'));
    }
    

    // 教材一覧表示
    public function materials()
    {
        $materials = Material::with('teacher')->get();
        return view('materials_create', compact('materials'));
    }

    public function journals_index(Request $request)
    {
        // ログイン中のユーザーID
        $userId = auth()->id();
    
        // 自分のジャーナルのみ取得
        $query = Journal::where('user_id', $userId);
    
        // ✅ 日付フィルター適用
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('start_time', [$request->start_date, $request->end_date]);
        }
    
        // ✅ キーワード検索（学習内容・目標・疑問のいずれか）
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('learnings', 'like', "%$keyword%")
                  ->orWhere('goals', 'like', "%$keyword%")
                  ->orWhere('questions', 'like', "%$keyword%");
            });
        }
    
        // ✅ ページネーション適用 (10件ずつ)
        $journals = $query->orderBy('start_time', 'desc')->paginate(10);
    
        // ✅ 直近1週間分のデータを取得し、日ごとの合計学習時間を計算
        $oneWeekAgo = Carbon::now()->subDays(7)->startOfDay();
        $weeklyData = Journal::where('user_id', $userId) // 👈 自分のデータのみ
            ->where('start_time', '>=', $oneWeekAgo)
            ->selectRaw('DATE(start_time) as date, SUM(duration) as total_duration')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    
        return view('journals_index', compact('journals', 'weeklyData'));
    }

    /*不使用
    public function qas_index() 
    {
        $qas = Qa::with('user', 'target')->get();
        return view('qas_index', compact('qas'));
    }
   */
  public function materials_index(Request $request) 
  {
      $query = Material::with('teacher');
  
      // 🔍 キーワード検索（タイトル）
      if ($request->filled('keyword')) {
          $query->where('title', 'like', '%' . $request->keyword . '%');
      }
  
      // 📅 期間検索（作成日）
      if ($request->filled('start_date') && $request->filled('end_date')) {
          $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
      }
  
      // 👤 投稿者検索（教師名）
      if ($request->filled('teacher')) {
          $query->whereHas('teacher', function ($q) use ($request) {
              $q->where('name', 'like', '%' . $request->teacher . '%');
          });
      }
  
      // ✅ ページネーション適用（10件ずつ表示）
      $materials = $query->orderBy('created_at', 'desc')->paginate(10);
  
      return view('materials_index', compact('materials'));
  }
    
    public function journals_create()
    {
        $journals = Journal::with('user')->orderBy('start_time', 'desc')->get();

        // 直近1週間分のデータを取得し、日ごとの合計学習時間を計算
        $oneWeekAgo = Carbon::now()->subDays(7)->startOfDay();
        $weeklyData = Journal::where('start_time', '>=', $oneWeekAgo)
            ->selectRaw('DATE(start_time) as date, SUM(duration) as total_duration')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('journals_create', compact('journals', 'weeklyData'));
    }

public function indexManagement(Request $request)
{
    // 検索処理
    $query = User::where('role', 0); // 生徒のみ

    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    $students = $query->get();

    // 生徒ごとのデータ取得
    $studentData = $students->map(function ($student) {
        // 平均学習時間（過去7日間）
        $averageDuration = Journal::where('user_id', $student->id)
            ->where('start_time', '>=', Carbon::now()->subDays(7))
            ->avg('duration');

        // 昨日の学習時間合計 & 学習記録
        $yesterdayJournal = Journal::where('user_id', $student->id)
            ->whereDate('start_time', Carbon::yesterday())
            ->orderBy('start_time', 'desc')
            ->first();

        return [
            'id' => $student->id,
            'name' => $student->name,
            'averageDuration' => round($averageDuration / 60, 1) . ' 分',
            'yesterdayDuration' => round(optional($yesterdayJournal)->duration / 60, 1) . ' 分',
            'yesterdayGoals' => optional($yesterdayJournal)->goals ?? 'なし',
            'yesterdayLearnings' => optional($yesterdayJournal)->learnings ?? 'なし',
            'yesterdayQuestions' => optional($yesterdayJournal)->questions ?? 'なし',
        ];
    });

    return view('students_index', compact('studentData'));
}

public function showStudentJournals($id, Request $request)
{
    $student = User::findOrFail($id);
    $query = Journal::where('user_id', $id)->orderBy('start_time', 'desc');

    // 検索条件
    if ($request->filled('date')) {
        $query->whereDate('start_time', $request->date);
    }
    if ($request->filled('goal')) {
        $query->where('goals', 'like', '%' . $request->goal . '%');
    }
    if ($request->filled('learning')) {
        $query->where('learnings', 'like', '%' . $request->learning . '%');
    }
    if ($request->filled('question')) {
        $query->where('questions', 'like', '%' . $request->question . '%');
    }

    $journals = $query->paginate(10); // ページネーションを追加

    return view('students_journals', compact('student', 'journals'));
}

}
