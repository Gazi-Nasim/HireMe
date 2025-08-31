<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = 'applications';
    protected $fillable = ['job_id', 'applicant_id', 'cv', 'status'];

    public function job()
    {
        return $this->belongsTo(EmployerJob::class, 'job_id');
    }

}
