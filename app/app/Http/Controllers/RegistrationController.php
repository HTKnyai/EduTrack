<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    // 学習ジャーナル新規登録
    public function storeJournal(Request $request)
    {
        $journal = Journal::create($request->all());
        return redirect('/journals')->with('success', '学習ジャーナルを追加しました！');
    }

    // Q&A新規登録
    public function storeQa(Request $request)
    {
        $qa = Qa::create($request->all());
        return redirect('/qas')->with('success', '質問を投稿しました！');
    }

    // 教材新規登録
    public function storeMaterial(Request $request)
    {
        $material = Material::create($request->all());
        return redirect('/materials')->with('success', '教材を追加しました！');
    }
}
