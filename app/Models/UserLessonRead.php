<?php

namespace App\Models;

use App\Models\app\Academy\Activity;
use Illuminate\Database\Eloquent\Model;

/**
 * Marca de lectura de una lección LMS programada (SCHEDULED) por un usuario.
 *
 * La existencia de una fila (user_id, activity_id) indica que el responsable
 * ya "vio" esa lección: el badge de pendientes cuenta solo las SCHEDULED
 * sin fila en esta tabla (blueprint Opción 5).
 */
class UserLessonRead extends Model
{
    protected $table = 'user_lesson_reads';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'activity_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
