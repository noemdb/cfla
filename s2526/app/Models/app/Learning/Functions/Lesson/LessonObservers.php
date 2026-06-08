<?php
namespace App\Models\app\Learning\Functions\Lesson;

use App\Models\app\Learning\Instrument;
use App\Models\app\Learning\Lesson;
use App\Models\app\Learning\Pedagogical;
use App\Models\app\Learning\Teaching;
use App\Models\app\Profesor\Pevaluacion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait LessonObservers
{
    public function creating(Model $model)
    {
        $model->author_id = Auth::user()->id;
    }

    public function updating(Model $model)
    {
        $model->modified_by = Auth::user()->id;
    }

}
