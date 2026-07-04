<?php

namespace App\Models\app\Inicial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eiprojectreview extends Model
{
    use HasFactory;

    protected $fillable = [
        'eiprojectk_id',
        'posibles_temas_interes',
        'eleccion_tema_nombre',
        'que_sabe',
        'que_desean_aprender',
        'que_necesitamos',
        'quienes_nos_pueden_apoyar',
        'order',
        'estrategias',
    ];

    const COLUMN_COMMENTS = [
        'eiprojectk_id' => 'Proyecto de Aula',
        'posibles_temas_interes' => 'Posibles temas de interés',
        'eleccion_tema_nombre' => 'Elección del tema y nombre del proyecto',
        'que_sabe' => 'Qué saben los estudiantes',
        'que_desean_aprender' => 'Qué desean aprender los estudiantes',
        'que_necesitamos' => 'Qué necesitamos para el proyecto',
        'quienes_nos_pueden_apoyar' => 'Quiénes nos pueden apoyar',
        'order' => 'Orden',
        'estrategias' => 'Estrategias',
    ];



    public function eiprojectk() { return $this->belongsTo(Eiprojectk::class, 'eiprojectk_id');}

}
