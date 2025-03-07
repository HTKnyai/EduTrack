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

    public function target()
    {
        return $this->belongsTo(Qa::class, 'target_id');
    }
}
