<?php

namespace App\Models\app\Learning;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use App\Models\app\Learning\Functions\Lesson\LessonRelations;
use App\Models\app\Learning\Functions\Lesson\LessonScope;
use App\Models\app\Learning\Functions\Lesson\LessonStatic;
use App\Models\app\Pescolar\AreaConocimiento;
use App\Models\app\Profesor\Pevaluacion;

class Lesson extends Model
{
    use HasFactory;
    use LessonRelations;
    use LessonScope;
    use LessonStatic;

    protected $fillable = [
        'evaluacion_id',
        'order',
        'title',
        'status',
        'finished',
        'comments',
        'content',
        'teaching',
        'learning',
        'evidence',
        'requireds',
        'description',
        'objectives',
        'activity_type',
        'duration',
        'reprogrammed',
        'observations',
        'active',
        'level',
        'planned',
        'pedagogical_id',
        'reprogrammed_by',
        'author_id',
        'modified_by',
    ];

    const COLUMN_COMMENTS = [
        'lapso_id' => 'Momento',
        'profesor_id' => 'Profesor',
        'pevaluacion_id' => 'Plan de Evaluación',
        'evaluacion_id' => 'Referentes teórico-práctico',
        'order' => 'Orden de la lección dentro del Plan de Evaluación',
        'title' => 'Título de la lección',
        'description' => 'Descripción detallada',
        'objectives' => 'Objetivos específicos',
        'activity_type' => 'Tipo de actividad', //['Teórica', 'Práctica', 'Laboratorio','Proyecto final','Exhibición']
        'duration' => 'Duración (minutos)',
        'requireds' => 'Materiales requeridos',
        'levefinishedl' => 'Nivel de dificultad',
        'planned' => 'Fecha',
        'reprogrammed' => 'Fecha reprogramada',
        'status' => '¿Está finalizada?',
        'finished' => 'Fecha de finalización',
        'active' => '¿Está activa?',
        'comments' => 'Comentarios al finalizar',
        'content' => 'Contenido',
        'teaching' => 'Enseñanza',
        'learning' => 'Aprendizaje',
        'evidence' => 'Evidencia fotográfica',
        'observations' => 'Observaciones',
        'pedagogical_id' => 'ID de instrumentos pedagógicos a utilizar',
        'reprogrammed_by' => 'Usuario que reprogramó la lección',
        'author_id' => 'Autor de la lección',
        'modified_by' => 'Autor de la última modificación',
    ]; 
       

    public function getFormattedPlannedDate(): string
    {
        return $this->planned ? Carbon::parse($this->planned)->format('d/m/Y') : '';
    }

    public function getFormattedReprogrammedDate(): string
    {
        return $this->reprogrammed ? Carbon::parse($this->reprogrammed)->format('d/m/Y') : '';
    }
    
    public function getFormattedStatus(): string
    {
        return $this->status ? 'Finalizada' : 'No finalizada';
    }

    public function getPedagogicalName(): string
    {
        return $this->pedagogical ? $this->pedagogical->teaching->name : '';
    }
    
    public function getAuthorName(): string
    {
        return $this->author ? User::find($this->author_id)->username : '';
    }    
    
    public function getModifier(): string
    {
        return $this->author ? User::find($this->modified_by)->username : '';
    }
    

    public function getAllLessons(): Collection
    {
        return Lesson::all();
    }

    public function getLessonsByEvaluacion(): Collection
    {
        return Lesson::where('evaluacion_id', $this->evaluacion_id)->get();
    }
        
    public function getLessonsByAuthor(): Collection
    {
        return Lesson::where('author_id', $this->author_id)->get();
    }

    public function getPevaluacionsAttribute(): Collection
    {
        $pevaluacions = Pevaluacion::select('pevaluacions.*')
        ->join('evaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
        ->join('lessons', 'evaluacions.id', '=', 'lessons.evaluacion_id')
        ->where('lessons.id',$this->id)
        ->get();        
        return $pevaluacions;
    }

    public function getPevaluacionAttribute()
    {
        return ($this->pevaluacions->isNotEmpty()) ? $this->pevaluacions->first() : null;
    }

    public function getPevaluacionIdAttribute()
    {
        return ($this->pevaluacion) ? $this->pevaluacion->id : null;
    }

    public function getEvidenceUrlAttribute()
    {
        return ($this->evidence) ? asset('storage/lessons/'.$this->evidence) : null;
    }

    public function getAreaConocimientoLeaderId($leader_id)
    {
        return AreaConocimiento::query()
        ->select('area_conocimientos.*')
        ->join('campo_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')
        ->join('asignaturas', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
        ->join('pensums', 'asignaturas.id', '=', 'pensums.asignatura_id')
        ->join('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->join('evaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
        ->join('lessons', 'evaluacions.id', '=', 'lessons.evaluacion_id')
        ->where('lessons.id',$this->id)
        ->where('area_conocimientos.leader_id',$leader_id)
        // ->groupBy('area_conocimientos.id')
        ->first();
    }

    

    
    

/*
'pevaluacion_id',
'references',
'orden',
'title',
'status',
'comments',
'evidence',
'requireds',
'description',
'objectives',
'activity_type',
'duration',
'reprogrammed',
'observations',
'active',
'level',
'planned',
'pedagogical_id',
'reprogrammed_by',
'author_id',
'modified_by',

pevaluacion_id
references
orden
title
status
comments
evidence
requireds
description
objectives
activity_type
duration
reprogrammed
observations
active
level
planned
pedagogical_id
reprogrammed_by
author_id
modified_by

*/






    
}
