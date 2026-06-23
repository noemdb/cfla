<?php

namespace App\Models\app\Profesor\Pevaluacion;

use Illuminate\Database\Eloquent\Model;

class Edescriptiva extends Model
{
    protected $fillable = [
        'estudiant_id','lapso_id','name','description','observations'
    ];

    const COLUMN_COMMENTS = [
        'estudiant_id' => 'Estudiante',
        'lapso_id' => 'Lapso',
        'name' => 'Nombre',
        'description' => 'Descripción',
        'observations' => 'Observación',
        'gaceta' => 'De acuerdo a lo establecido en la Resolución N° 266 de fecha 20 de Diciembre de 1999, publicado en la Gaceta Oficial N° 5428 Extraordinario de fecha 5 de Enero del 2000, sobre el Régimen de Evaluación de la primera y Segunda Etapas de Educación Básica. Según lo establecido en el artículo 14, literal C, se elabora el presente informe descriptivo y analítico de los resultados de la evaluación:',
    ];

    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant');
    }
    public function lapso()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Lapso');
    }
}
