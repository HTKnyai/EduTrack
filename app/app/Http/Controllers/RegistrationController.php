<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Storage;
use App\Journal;
use App\Qa;
use App\Material;
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
    
    public function storeMaterial(Request $request)
    {
        // バリデーション
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,txt|max:2048',
        ]);
    
        // ファイルを保存（オリジナルのファイル名を維持）
        if ($request->hasFile('file')) {
            $fileName = $request->file('file')->getClientOriginalName(); // 元のファイル名
            $filePath = $request->file('file')->storeAs('materials', $fileName, 'public'); // ファイル名を維持して保存
        } else {
            return back()->with('error', 'ファイルのアップロードに失敗しました');
        }
    
        // データベースに保存
        Material::create([
            'teacher_id' => auth()->id(),
            'title' => $request->title,
            'file_path' => 'storage/materials/' . $fileName, // パスを適切に修正
            'dls' => 0,
        ]);
    
        return back()->with('success', '教材をアップロードしました');
    }
    
    // 教材更新
    public function updateMaterial(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,txt|max:2048',
        ]);
    
        $material = Material::findOrFail($id);
    
        // 教材のタイトルを更新
        $material->title = $request->title;
    
        // 新しいファイルがある場合は更新
        if ($request->hasFile('file')) {
            // 既存のファイルを削除
            if ($material->file_path) {
                Storage::delete(str_replace('storage/', 'public/', $material->file_path));
            }
            // 新しいファイルを保存
            $filePath = $request->file('file')->store('materials', 'public');
            $material->file_path = 'storage/' . $filePath;
        }
    
        $material->save();
    
        return back()->with('success', '教材を更新しました');
    }
    
    // 教材削除
    public function destroyMaterial($id)
    {
        $material = Material::findOrFail($id);
    
        // ファイル削除
        if ($material->file_path) {
            Storage::delete(str_replace('storage/', 'public/', $material->file_path));
        }
    
        $material->delete();
    
        return back()->with('success', '教材を削除しました');
    }

    public function downloadMaterial($id)
    {
        $material = Material::findOrFail($id);
        $filePath = str_replace('storage/', 'public/', $material->file_path); // 正しいパスに修正
    
        if (Storage::exists($filePath)) {
            // ダウンロード数をカウント
            $material->increment('dls');
    
            // オリジナルのファイル名でダウンロード
            $originalFileName = basename($material->file_path); // ファイル名取得
            return Storage::download($filePath, $originalFileName);
        }
    
        return back()->with('error', 'ファイルが見つかりません');
    }
}
