<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
    'name',
    'email',
    'mobile_no',
    'location',
    'position_id',
    'resume',
    'status',
    'interview_at'
];

protected $casts = [
    'interview_at' => 'datetime'
];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}