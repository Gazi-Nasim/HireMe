<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployerJob extends Model
{
    // protected $fillable = ['user_id', 'title', 'status', 'description','salary_range','location'];
    protected $guarded = ['id'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
