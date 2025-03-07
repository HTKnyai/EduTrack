<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $fillable = ['user_id', 'start_time', 'end_time', 'duration', 'goals', 'learnings', 'questions'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
