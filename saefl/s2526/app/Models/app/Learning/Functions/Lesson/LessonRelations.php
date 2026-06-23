<?php
namespace App\Models\app\Learning\Functions\Lesson;

use App\Models\app\Learning\Instrument;
use App\Models\app\Learning\Pedagogical;
use App\Models\app\Learning\Teaching;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\User;

trait LessonRelations
{
    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class);
    }
    public function pedagogical()
    {
        return $this->hasOne(Pedagogical::class);
    }
    public function instruments()
    {
        return $this->hasMany(Instrument::class);
    }
    public function teachings()
    {
        return $this->belongsToMany(Teaching::class, 'instruments', 'lesson_id', 'teaching_id');
    }
    public function autor()
    {
        return $this->belongsTo(User::class,'author_id');
    }
}
