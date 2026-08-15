<?php

namespace App\Models\app\Timetable;

use App\Models\app\Academy\Profesor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableSubstituteAssignment extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'timetable_substitute_assignments';

    protected $fillable = ['absence_id', 'slot_id', 'substitute_profesor_id', 'status', 'notified_at'];

    protected $casts = [
        'notified_at' => 'datetime',
    ];

    public function absence()
    {
        return $this->belongsTo(TimetableAbsence::class, 'absence_id');
    }

    public function slot()
    {
        return $this->belongsTo(TimetableSlot::class, 'slot_id');
    }

    public function substituteProfesor()
    {
        return $this->belongsTo(Profesor::class, 'substitute_profesor_id');
    }
}
