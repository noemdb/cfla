<?php

namespace App\Models\app\Academy\Lms;

use App\Models\User;
use App\Models\app\Academy\Activity;
use Illuminate\Database\Eloquent\Model;

class LmsActivityProgress extends Model
{
    protected $table = 'lms_activity_progress';

    protected $fillable = [
        'activity_id',
        'student_id',
        'status',
        'completion_pct',
        'time_spent_secs',
        'first_access_at',
        'last_access_at',
        'completed_at',
    ];

    protected $casts = [
        'completion_pct' => 'decimal:2',
        'time_spent_secs' => 'integer',
        'first_access_at' => 'datetime',
        'last_access_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
