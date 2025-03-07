<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Journal;
use App\Material;
use App\Qa;
use App\User;

class DisplayController extends Controller
{
    public function index()
    {
        return view('welcome'); 
    }
    // 学習ジャーナル一覧表示
    public function journals()
    {
        $journals = Journal::with('user')->get();
        return view('journals_create', compact('journals'));
    }

    // 質問掲示板一覧表示
    public function qas()
    {
        $qas = Qa::with('user', 'target')->get();
        return view('qas_create', compact('qas'));
    }

    // 教材一覧表示
    public function materials()
    {
        $materials = Material::with('teacher')->get();
        return view('materials_create', compact('materials'));
    }
}
