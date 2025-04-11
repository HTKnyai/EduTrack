<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Qa extends Model
{
    protected $fillable = ['user_id', 'target_id', 'contents', 'anonymize'];

    // 投稿者情報
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 親質問
    public function target()
    {
        return $this->belongsTo(Qa::class, 'target_id');
    }

    // 直接の回答
    public function replies()
    {
        return $this->hasMany(Qa::class, 'target_id');
    }

    //全ての子質問を取得
    public function allReplies()
    {
        return $this->replies()->with('allReplies');
    }
}
