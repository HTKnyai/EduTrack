<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qa extends Model
{
    protected $fillable = ['user_id', 'target_id', 'contents', 'anonymize'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔹 `target_id` が `0` の質問（親）
    public function parent()
    {
        return $this->belongsTo(Qa::class, 'target_id');
    }

    // 🔹 `target_id` を持つ回答（子）
    public function replies()
    {
        return $this->hasMany(Qa::class, 'target_id');
    }

    // 🔹 **再帰的に全ての子要素を取得する**
    public function allReplies()
    {
        return $this->replies()->with('allReplies');
    }
}

/*    
public function target()
    {
        return $this->belongsTo(Qa::class, 'target_id');
    } 
*/