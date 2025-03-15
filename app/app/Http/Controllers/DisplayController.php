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
    /* ========== ダッシュボード関連 ========== */
    
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 0) { // 生徒の場合
            $weeklyData = $this->getUserWeeklyData($user->id);
            $yesterdayJournal = $this->getYesterdayJournal($user->id);
        } else { // 教師の場合　
            $weeklyData = collect([]);
            $yesterdayJournal = null;
        }

        return view('dashboard', [
            'weeklyData' => $weeklyData,
            'qas' => $this->getRecentQas(),
            'materials' => $this->getRecentMaterials(),
            'yesterdayJournal' => $yesterdayJournal,
        ]);
    }

    /* ========== 学習ジャーナル関連 ========== */

    public function journals_index(Request $request)
    {
        $query = Journal::where('user_id', auth()->id());

        if ($request->filled('start_date')) {
            $query->whereDate('start_time', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('start_time', '<=', $request->end_date);
        }
        if ($request->filled('keyword')) { //ここの検索は学習目標・
            $query->where(function ($q) use ($request) {
                $q->where('goals', 'like', "%{$request->keyword}%")
                  ->orWhere('learnings', 'like', "%{$request->keyword}%")
                  ->orWhere('questions', 'like', "%{$request->keyword}%");
            });
        }

        $journals = $query->orderBy('start_time', 'desc')->paginate(10);//基本的なページネーション処理
        $weeklyData = $this->getUserWeeklyData(auth()->id());

        if ($request->ajax()) {
            return response()->json([
                'html' => view('journals_list', compact('journals'))->render(), //journals_list(view)のHTMLを文字列として返す
                'pagination' => (string) $journals->links(),
                'weeklyData' => $weeklyData,
            ]);
        }

        return view('journals_index', compact('journals', 'weeklyData'));
    }

    /*　
    public function weeklyData()
    {
        return response()->json([
            'labels' => $this->getUserWeeklyData(auth()->id())->pluck('date')->toArray(),
            'durations' => $this->getUserWeeklyData(auth()->id())->pluck('total_duration')->map(fn($d) => round($d / 60, 1))->toArray(),
        ]);
    }
    */

    /* ========== Q&A関連 ========== */

    public function qas_index(Request $request)
    {
        $query = Qa::with(['user', 'target', 'replies']); // 一度に取得するリレーション
    
        if ($request->filled('keyword')) {
            $keyword = '%' . $request->keyword . '%';
    
            $query->where(function ($q) use ($keyword) {
                $q->where('contents', 'like', $keyword);
                $this->applyNestedReplySearch($q, $keyword);
            });
        }
    
        if ($request->filled('user')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user . '%');
            })->where('anonymize', '=', 0);
        }
    
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
    
        return view('qas_index', ['qas' => $query->orderBy('created_at', 'desc')->paginate(10)]);
    }
    
    /**
     * 🔄 再帰的に `replies` の階層を検索対象に含める
     */
    private function applyNestedReplySearch($query, $keyword, $depth = 3)
    {
        if ($depth <= 0) return; // 深さ制限（無限ループ防止）
    
        $query->orWhereHas('replies', function ($q) use ($keyword, $depth) {
            $q->where('contents', 'like', $keyword);
            $this->applyNestedReplySearch($q, $keyword, $depth - 1); // 再帰的に適用
        });
    }

    /* ========== 教材管理関連 ========== */

    public function materials_index(Request $request) 
    {
        $query = Material::with('teacher');

        if ($request->filled('keyword')) {
            $query->where('title', 'like', '%' . $request->keyword . '%');
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('teacher')) {
            $query->whereHas('teacher', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->teacher . '%');
            });
        }

        return view('materials_index', ['materials' => $query->orderBy('created_at', 'desc')->paginate(10)]);
    }

    /* ========== 生徒管理関連 ========== */

    public function indexManagement(Request $request) //生徒一覧表示
    {
        $query = User::where('role', 0);
    
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
    
        $students = $query->get()->map(function ($student) use ($request) {
            $yesterdayJournal = $this->getYesterdayJournal($student->id);
    
            // 検索条件を適用
            if ($request->filled('goal') && (!str_contains($yesterdayJournal->goals ?? '', $request->goal))) {
                return null;
            }
            if ($request->filled('learning') && (!str_contains($yesterdayJournal->learnings ?? '', $request->learning))) {
                return null;
            }
            if ($request->filled('question') && (!str_contains($yesterdayJournal->questions ?? '', $request->question))) {
                return null;
            }
    
            return [
                'id' => $student->id,
                'name' => $student->name,
                'averageDuration' => round($this->getAverageStudyTime($student->id) / 60, 1) . ' 分',
                'yesterdayDuration' => round(optional($yesterdayJournal)->total_duration / 60, 1) . ' 分',
                'yesterdayGoals' => optional($yesterdayJournal)->goals ?? 'なし',
                'yesterdayLearnings' => optional($yesterdayJournal)->learnings ?? 'なし',
                'yesterdayQuestions' => optional($yesterdayJournal)->questions ?? 'なし',
            ];
        })->filter(); // nullデータを削除
    
        return view('students_index', ['studentData' => $students]);
    }

    public function showStudentJournals($id, Request $request) //特定生徒の表示
    {
        $student = User::findOrFail($id); //failなら404
        $query = Journal::where('user_id', $id)->orderBy('start_time', 'desc');
    
        if ($request->filled('date')) {
            $query->whereDate('start_time', $request->date);
        }
        if ($request->filled('goal')) {
            $query->where('goals', 'LIKE', "%{$request->goal}%");
        }
        if ($request->filled('learning')) {
            $query->where('learnings', 'LIKE', "%{$request->learning}%");
        }
        if ($request->filled('question')) {
            $query->where('questions', 'LIKE', "%{$request->question}%");
        }
        $journals = $query->paginate(10);
    
        return view('students_journals', compact('student', 'journals'));
    }

    /* ========== 共通メソッド ========== */

    private function getUserWeeklyData($userId)
    {
        return Journal::where('user_id', $userId)
            ->where('start_time', '>=', Carbon::now()->subDays(7))
            ->selectRaw('DATE(start_time) as date, SUM(duration) as total_duration')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    private function getYesterdayJournal($userId)
    {
        return Journal::where('user_id', $userId)
            ->whereDate('start_time', Carbon::yesterday())
            ->selectRaw('SUM(duration) as total_duration, GROUP_CONCAT(DISTINCT goals SEPARATOR ", ") as goals, GROUP_CONCAT(DISTINCT learnings SEPARATOR ", ") as learnings, GROUP_CONCAT(DISTINCT questions SEPARATOR ", ") as questions')
            ->first();
    }

    private function getRecentQas($limit = 5)
    {
        return Qa::with('user')
            ->where('target_id', 0)
            ->latest()
            ->take($limit)
            ->get();
    }

    private function getRecentMaterials($limit = 5)
    {
        return Material::with('teacher')
            ->latest()
            ->take($limit)
            ->get();
    }

    private function getAverageStudyTime($userId)
    {
        return Journal::where('user_id', $userId)
            ->where('start_time', '>=', Carbon::now()->subDays(7))
            ->avg('duration');
    }
}