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
    /*---------- 学習ジャーナル ----------*/

    // ジャーナル共通バリデーション
    private function validateJournal(Request $request)
    {
        return $request->validate([
            'goals' => 'required|string|max:255',
            'learnings' => 'required|string|max:255',
            'questions' => 'nullable|string|max:255',
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
            'duration' => 'required|integer|min:0',
        ]);
    }

    public function storeJournal(Request $request)
    {
        try {
            $validated = $this->validateJournal($request);
    
            // フォーマットを明示的に修正
            $validated['start_time'] = Carbon::parse($validated['start_time'])->format('Y-m-d H:i:s');
            $validated['end_time'] = Carbon::parse($validated['end_time'])->format('Y-m-d H:i:s');
    
            // データを保存
            $journal = Journal::create(array_merge([
                'user_id' => auth()->id(),
            ], $validated));
    
            return response()->json(['success' => true, 'journal' => $journal]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'バリデーションエラー: ' . json_encode($e->errors()),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'サーバーエラー: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateStudentJournal(Request $request, $id)
    {
        $journal = Journal::findOrFail($id);
        $validated = $this->validateJournal($request);

        $journal->update([
            'start_time' => Carbon::parse($validated['start_time']),
            'end_time' => Carbon::parse($validated['end_time']),
            'duration' => Carbon::parse($validated['end_time'])->diffInSeconds(Carbon::parse($validated['start_time'])),
            ...$validated
        ]);

        return redirect()->route('students.journals', $journal->user_id)->with('success', '学習ジャーナルが更新されました');
    }

    public function destroyStudentJournal($id)
    {
        Journal::findOrFail($id)->delete();
        return redirect()->back()->with('success', '学習ジャーナルが削除されました');
    }

    /*---------- Q&A ----------*/

    // Q&A共通バリデーション
    private function validateQa(Request $request)
    {
        return $request->validate([
            'contents' => 'required|string|max:500',
            'target_id' => 'nullable|integer|min:0',
        ]);
    }

    public function storeQa(Request $request)
    {
        $validated = $this->validateQa($request);

        Qa::create([
            'user_id' => Auth::id(),
            'target_id' => $validated['target_id'] ?? 0,
            'contents' => $validated['contents'],
            'anonymize' => $request->filled('anonymize') ? 1 : 0,
        ]);

        return redirect('/qas')->with('success', '質問が投稿されました！');
    }

    public function updateQa(Request $request, $id)
    {
        $qa = Qa::findOrFail($id);
        if ($qa->user_id !== Auth::id()) {
            return redirect()->route('qas_index')->with('error', '編集権限がありません');
        }

        $qa->update(['contents' => $request->validate(['contents' => 'required|string|max:500'])['contents']]);

        return redirect()->route('qas_index')->with('success', '質問が更新されました');
    }

    public function destroyQa($id)
    {
        $qa = Qa::findOrFail($id);
        if ($qa->user_id !== Auth::id()) {
            return redirect()->route('qas_index')->with('error', '削除権限がありません');
        }

        $qa->delete();
        return redirect()->route('qas_index')->with('success', '質問が削除されました');
    }

    /*---------- 教材管理 ----------*/

    // 教材共通バリデーション
    private function validateMaterial(Request $request, $isUpdate = false)
    {
        $rules = [
            'title' => 'required|string|max:255',
        ];

        if (!$isUpdate || $request->hasFile('file')) {
            $rules['file'] = 'required|file|mimes:pdf,doc,docx,ppt,pptx,txt|max:2048';
        }

        return $request->validate($rules);
    }

    private function handleFileUpload(Request $request, $oldFilePath = null)
    {
        if ($request->hasFile('file')) {
            if ($oldFilePath) {
                Storage::disk('public')->delete($oldFilePath);
            }

            $fileName = $request->file('file')->getClientOriginalName();
            return $request->file('file')->storeAs('materials', $fileName, 'public');
        }

        return $oldFilePath;
    }

    public function storeMaterial(Request $request)
    {
        $validated = $this->validateMaterial($request);

        $filePath = $this->handleFileUpload($request);

        Material::create([
            'teacher_id' => auth()->id(),
            'title' => $validated['title'],
            'file_path' => 'storage/' . $filePath,
            'dls' => 0,
        ]);

        return back()->with('success', '教材をアップロードしました');
    }

    public function updateMaterial(Request $request, $id)
    {
        $material = Material::findOrFail($id);
        $validated = $this->validateMaterial($request, true);

        $filePath = $this->handleFileUpload($request, $material->file_path);

        $material->update([
            'title' => $validated['title'],
            'file_path' => 'storage/' . $filePath,
        ]);

        return redirect()->route('materials.index')->with('success', '教材が更新されました');
    }

    public function destroyMaterial($id)
    {
        $material = Material::findOrFail($id);

        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return back()->with('success', '教材を削除しました');
    }

    public function downloadMaterial($id)
    {
        $material = Material::findOrFail($id);
        $material->increment('dls');

        $filePath = storage_path('app/public/' . str_replace('storage/', '', $material->file_path));
        return response()->download($filePath, basename($material->file_path));
    }
}