<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qa extends Model
{
    protected $fillable = ['user_id', 'target_id', 'contents', 'anonymize'];

    // 質問を投稿したユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 回答の対象となる質問（親質問）
    public function target()
    {
        return $this->belongsTo(Qa::class, 'target_id');
    }

    // 質問に対する回答（子質問）
    public function replies()
    {
        return $this->hasMany(Qa::class, 'target_id');
    }
    
    public function allReplies()
    {
        return $this->hasMany(Qa::class, 'target_id');
    }
}
