<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['teacher_id', 'title', 'file_path', 'dls'];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
