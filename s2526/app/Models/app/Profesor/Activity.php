<?php

namespace App\Models\app\Profesor;

use App\Models\app\Pescolar\Asignatura;
use App\Models\app\Pescolar\Grado;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = ['pevaluacion_id','finicial','ffinal','topic','thematic','references','teaching','learning','description','observations','comments','status'];

    protected $casts = [
        'status' => 'boolean', 
    ];

    const COLUMN_COMMENTS = [
        'pevaluacion_id' => 'Plan de Evaluación',
        'finicial' => 'Fecha Inicial',
        'ffinal' => 'Fecha Final',
        'topic' => 'Tema generador y Énfasis',
        'thematic' => 'Tejido temático / Tema Indispensable',
        'references' => 'Referentes teórico prácticos y éticos',
        'teaching' => 'Enseñanza/Actividad Globalizada',
        'learning' => 'Aprendizaje',
        'description' => 'Actividad Evaluativa',
        'observations' => 'ODS / Sistematización',
        'comments' => 'Comentarios',
        'status' => 'Aprobación',
        'achievement' => 'Indicadores de logro',
        'name' => 'Nombre',
        'weighting' => 'Ponderación',
        'status_quantitative_weighting' => 'El indicador es ponderado (cuantitativo)',
    ];

    public function achievements()
    {
        return $this->hasMany('App\Models\app\Profesor\Achievement');
    }

    public function pevaluacion()
    {
        return $this->belongsTo('App\Models\app\Profesor\Pevaluacion');
    }

    public static function getForPestudioId($pestudio_id): Collection
    {
        return Activity::query()
        ->select('activities.*')
        ->join('pevaluacions', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
        ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
        ->where('pestudios.id',$pestudio_id)
        ->get();
    }

    public function getGrado()
    {
        return Grado::select('grados.*')
        ->join('pensums', 'grados.id', '=', 'pensums.grado_id')
        ->join('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->join('activities', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
        ->where('activities.id',$this->id)
        ->groupBy('activities.id')
        ->first()
        ;
    }

    public function getAsignatura()
    {
        return Asignatura::select('asignaturas.*')
        ->join('pensums', 'asignaturas.id', '=', 'pensums.asignatura_id')
        ->join('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->join('activities', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
        ->where('activities.id',$this->id)
        ->groupBy('activities.id')
        ->first()
        ;
    }

    public function getStatusResumeAttribute()
    {
        return (! empty($this->description) ) ? true : false;
    }

    /**
     * Cuenta las palabras del campo `teaching` cuya longitud sea mayor a $num letras.
     *
     * @param  int  $num  Longitud mínima (exclusiva). Por defecto 3.
     * @return int        Cantidad de palabras con más de $num letras.
     */
    public function teachingWordsMayorCount(int $num = 3): int
    {
        if (empty($this->teaching)) {
            return 0;
        }

        // Normalizar: quitar caracteres no alfabéticos que no sean espacios
        $texto = preg_replace('/[^\p{L}\s]/u', '', $this->teaching);

        // Separar por espacios (uno o más)
        $palabras = preg_split('/\s+/u', trim($texto), -1, PREG_SPLIT_NO_EMPTY);

        return count(array_filter($palabras, fn(string $p) => mb_strlen($p) > $num));
    }

    /**
     * Retorna el campo `activities_avr` del Pestudio asociado a esta actividad.
     * Cadena de relaciones: activity → pevaluacion → pensum → pestudio.
     *
     * @return int|null
     */
    public function getActivitiesAvrAttribute(): ?int
    {
        return optional(
            optional(
                optional($this->pevaluacion)->pensum
            )->pestudio
        )->activities_avr;
    }
}


/*

'pevaluacion_id','finicial','ffinal','topic','thematic','references','teaching','learning','description','observations','comments','status'

pevaluacion_id
finicial
ffinal
topic
thematic
references
teaching
learning
description
observations
comments
status

*/