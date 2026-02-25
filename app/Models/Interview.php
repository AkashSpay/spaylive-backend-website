<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Interview extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'candidate_id',
        'date',
        'start_time',
        'end_time',
        'type',
        'join_link',
    ];

    /**
     * Cast attributes to proper types
     */
    protected $casts = [
        'date' => 'date:Y-m-d',
        'start_time' => 'string',
        'end_time' => 'string',
    ];

    /**
     * ============================
     * RELATIONSHIPS
     * ============================
     */

    /**
     * Interview belongs to Candidate
     */
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * ============================
     * ACCESSORS (Optional but Useful)
     * ============================
     */

    /**
     * Get formatted start datetime
     */
    public function getStartDateTimeAttribute()
    {
        return Carbon::parse($this->date . ' ' . $this->start_time);
    }

    /**
     * Get formatted end datetime
     */
    public function getEndDateTimeAttribute()
    {
        if (!$this->end_time) {
            return null;
        }

        return Carbon::parse($this->date . ' ' . $this->end_time);
    }

    /**
     * Check if interview is upcoming
     */
    public function getIsUpcomingAttribute()
    {
        return $this->start_date_time > now();
    }

    /**
     * ============================
     * QUERY SCOPES (Professional Use)
     * ============================
     */

    /**
     * Scope: Only upcoming interviews
     */
    public function scopeUpcoming($query)
    {
        return $query->whereDate('date', '>=', now()->toDateString());
    }

    /**
     * Scope: Only today's interviews
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', now()->toDateString());
    }
}