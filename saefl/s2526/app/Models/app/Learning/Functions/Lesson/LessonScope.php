<?php
namespace App\Models\app\Learning\Functions\Lesson;

use App\Models\app\Learning\Instrument;
use App\Models\app\Learning\Pedagogical;
use App\Models\app\Learning\Teaching;
use App\Models\app\Profesor\Pevaluacion;

trait LessonScope
{
    public function scopeStatus()
    {
        return $this->where('status', true);
    }
    public function scopeActive()
    {
        return $this->where('active', true);
    }
    public function scopeByAuthor($author_id)
    {
        return $this->where('author_id', $author_id);
    }
}
