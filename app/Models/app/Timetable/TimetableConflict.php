<?php

namespace App\Models\app\Timetable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableConflict extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'timetable_conflicts';

    protected $fillable = ['calendar_id', 'slot_id', 'type', 'details', 'resolved'];

    protected $casts = [
        'details' => 'array',
        'resolved' => 'boolean',
    ];

    public function calendar()
    {
        return $this->belongsTo(TimetableCalendar::class, 'calendar_id');
    }

    public function slot()
    {
        return $this->belongsTo(TimetableSlot::class, 'slot_id');
    }
}
