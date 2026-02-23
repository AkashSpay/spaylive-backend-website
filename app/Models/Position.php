<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = [
        'department_id',
        'name',
        'location',
        'job_type',
        'experience',
        'salary_range',
        'skills',
        'responsibility',
        'requirements',
        'status'
    ];

    protected $casts = [
        'skills' => 'array',
        'responsibility' => 'array',
        'requirements' => 'array',
        //'status' => 'boolean'
    ];

    // Relationship with department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Relationship with candidates
    public function candidates()
    {
        return $this->hasMany(Candidate::class, 'position_id');
    }

    // Accessor to get applications count
    public function getApplicationsCountAttribute()
    {
        return $this->candidates()->count();
    }

}


