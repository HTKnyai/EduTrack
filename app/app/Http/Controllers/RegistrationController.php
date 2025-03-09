<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Journal;
use App\Qa;
use Carbon\Carbon;

class RegistrationController extends Controller
{

    public function storeJournal(Request $request)
    {
        $validated = $request->validate([
            'goals' => 'required|string|max:255',
            'learnings' => 'required|string|max:255',
            'questions' => 'nullable|string|max:255',
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
            'duration' => 'required|integer|min:1',
        ]);
    
        Journal::create([
            'user_id' => auth()->id(),
            'start_time' => Carbon::createFromFormat('Y-m-d H:i:s', $validated['start_time']),
            'end_time' => Carbon::createFromFormat('Y-m-d H:i:s', $validated['end_time']),
            'duration' => $validated['duration'],
            'goals' => $validated['goals'],
            'learnings' => $validated['learnings'],
            'questions' => $validated['questions'],
        ]);
    
        return redirect('/journals')->with('success', '学習ジャーナルが追加されました！');
    }
    

    // Q&A新規登録
    public function storeQa(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'contents' => 'required|string|max:500',
            'target_id' => 'nullable|integer|min:0', // `min:0` を追加（新規質問は0）
        ]);

        if (!Auth::check()) {
            return redirect('/login')->with('error', 'ログインしてください');
        }

        // Q&A データ登録
        Qa::create([
            'user_id' => Auth::id(), // ログイン中のユーザー
            'target_id' => $validated['target_id'] ?? 0, // 未入力時は新規質問として `0`
            'contents' => $validated['contents'],
            'anonymize' => $request->filled('anonymize') ? 1 : 0, // チェックボックスの有無
        ]);

        return redirect('/qas')->with('success', '質問が投稿されました！');
    }
    /*
    public function storeQa(Request $request)
    {
        $qa = Qa::create($request->all());
        return redirect('/qas')->with('success', '質問を投稿しました！');
    }
    */
    
    // 教材新規登録
    public function storeMaterial(Request $request)
    {
        // バリデーション
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,txt|max:2048', // 許可するファイル形式とサイズ制限
        ]);

        // ファイルの保存
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('materials', 'public'); // storage/app/public/materials に保存
        } else {
            return back()->with('error', 'ファイルのアップロードに失敗しました');
        }

        // データベースに保存
        Material::create([
            'teacher_id' => auth()->id(), // 認証済みユーザーをアップロード者として設定
            'title' => $request->title,
            'file_path' => 'storage/' . $filePath, // 公開ディレクトリからアクセスできるようにする
            'dls' => 0, // 初期のダウンロード数は0
        ]);

        return back()->with('success', '教材をアップロードしました');
    }
}
