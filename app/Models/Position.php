<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;
    protected $fillable = ['name','location','job_type','department_id','responsibility',
        'requirements'];

    protected $casts = [
    'responsibility' => 'array',
    'requirements' => 'array',
];
}
