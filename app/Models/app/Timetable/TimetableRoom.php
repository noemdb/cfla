<?php

namespace App\Models\app\Timetable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableRoom extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'timetable_rooms';

    protected $fillable = ['code', 'name', 'capacity', 'type', 'features', 'status_active'];

    protected $casts = [
        'capacity' => 'integer',
        'features' => 'array',
        'status_active' => 'boolean',
    ];

    public function scopeActive($query, $flag = true)
    {
        return $query->where('status_active', $flag);
    }
}
