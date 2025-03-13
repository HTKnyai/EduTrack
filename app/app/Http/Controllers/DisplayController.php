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
    /* ========== 📌 ダッシュボード関連 ========== */
    
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

    /* ========== 📌 学習ジャーナル関連 ========== */

    public function journals_index(Request $request)
    {
        $query = Journal::where('user_id', auth()->id());

        if ($request->filled('start_date')) {
            $query->whereDate('start_time', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('start_time', '<=', $request->end_date);
        }
        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('goals', 'like', "%{$request->keyword}%")
                  ->orWhere('learnings', 'like', "%{$request->keyword}%")
                  ->orWhere('questions', 'like', "%{$request->keyword}%");
            });
        }

        $journals = $query->orderBy('start_time', 'desc')->paginate(10);
        $weeklyData = $this->getUserWeeklyData(auth()->id());

        if ($request->ajax()) {
            return response()->json([
                'html' => view('journals_list', compact('journals'))->render(),
                'pagination' => (string) $journals->links(),
                'weeklyData' => $weeklyData,
            ]);
        }

        return view('journals_index', compact('journals', 'weeklyData'));
    }

    public function weeklyData()
    {
        return response()->json([
            'labels' => $this->getUserWeeklyData(auth()->id())->pluck('date')->toArray(),
            'durations' => $this->getUserWeeklyData(auth()->id())->pluck('total_duration')->map(fn($d) => round($d / 60, 1))->toArray(),
        ]);
    }

    /* ========== 📌 Q&A関連 ========== */

    public function qas_index(Request $request)
    {
        $query = Qa::with(['user', 'target', 'replies']);

        if ($request->filled('keyword')) {
            $query->where('contents', 'like', '%' . $request->keyword . '%')
                  ->orWhereHas('replies', function ($q) use ($request) {
                      $q->where('contents', 'like', '%' . $request->keyword . '%');
                  });
        }

        if ($request->filled('user')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user . '%');
            })->where('anonymize', '=', 0);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        return view('qas_index', ['qas' => $query->orderBy('created_at', 'desc')->paginate(10)]);
    }

    /* ========== 📌 教材管理関連 ========== */

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

    /* ========== 📌 生徒管理関連 ========== */

    public function indexManagement(Request $request)
    {
        $query = User::where('role', 0);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return view('students_index', ['studentData' => $query->get()->map(fn($student) => [
            'id' => $student->id,
            'name' => $student->name,
            'averageDuration' => round($this->getAverageStudyTime($student->id) / 60, 1) . ' 分',
            'yesterdayDuration' => round(optional($this->getYesterdayJournal($student->id))->duration / 60, 1) . ' 分',
            'yesterdayGoals' => optional($this->getYesterdayJournal($student->id))->goals ?? 'なし',
            'yesterdayLearnings' => optional($this->getYesterdayJournal($student->id))->learnings ?? 'なし',
            'yesterdayQuestions' => optional($this->getYesterdayJournal($student->id))->questions ?? 'なし',
        ])]);
    }

    /* ========== 📌 共通メソッド ========== */

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
            ->selectRaw('SUM(duration) as total_duration, GROUP_CONCAT(learnings SEPARATOR ", ") as learnings, GROUP_CONCAT(questions SEPARATOR ", ") as questions')
            ->first();
    }

    private function getRecentQas($limit = 5)
    {
        return Qa::with('user')->where('target_id', 0)->latest()->take($limit)->get();
    }

    private function getRecentMaterials($limit = 5)
    {
        return Material::with('teacher')->latest()->take($limit)->get();
    }

    private function getAverageStudyTime($userId)
    {
        return Journal::where('user_id', $userId)->where('start_time', '>=', Carbon::now()->subDays(7))->avg('duration');
    }
}