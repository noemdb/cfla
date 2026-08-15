<?php

namespace App\Models\app\Timetable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableShift extends Model
{
    use HasFactory;

    protected $table = 'timetable_shifts';

    protected $fillable = ['code', 'name', 'start_time', 'end_time'];

    protected $casts = [
        'start_time' => 'string',
        'end_time' => 'string',
    ];

    const CODE_MORNING = 'M';

    const CODE_AFTERNOON = 'T';

    public function periods()
    {
        return $this->hasMany(TimetablePeriod::class, 'shift_id');
    }
}
