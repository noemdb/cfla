<?php

namespace App\Models\app\Timetable;

use App\Models\app\Academy\Profesor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableAbsence extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'timetable_absences';

    protected $fillable = ['calendar_id', 'profesor_id', 'date_start', 'date_end', 'reason'];

    protected $casts = [
        'date_start' => 'date:Y-m-d',
        'date_end' => 'date:Y-m-d',
    ];

    public function calendar()
    {
        return $this->belongsTo(TimetableCalendar::class, 'calendar_id');
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }

    public function substitutes()
    {
        return $this->hasMany(TimetableSubstituteAssignment::class, 'absence_id');
    }
}
