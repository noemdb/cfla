<?php
namespace App\Models\app\Learning\Functions\Lesson;

use App\Models\app\Learning\Instrument;
use App\Models\app\Learning\Lesson;
use App\Models\app\Learning\Pedagogical;
use App\Models\app\Learning\Teaching;
use App\Models\app\Profesor\Pevaluacion;

trait LessonEvents
{
    public function lessonCreated(Lesson $lesson)
    {
        // Enviar una notificación al usuario que creó la lección.
    }

    public function lessonUpdated(Lesson $lesson)
    {
        // Enviar una notificación al usuario que actualizó la lección.
    }

    public function lessonDeleted(Lesson $lesson)
    {
        // Enviar una notificación al usuario que actualizó la lección. Observers
    }


}
