<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'job_type',
        'department_id',
        'responsibility',
        'requirements',
        'experience',
        'salary_range',
        'skills',
        'status'
    ];

    protected $casts = [
        'responsibility' => 'array',
        'requirements'   => 'array',
        'skills'         => 'array',
        'status'         => 'boolean', // if using boolean
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}