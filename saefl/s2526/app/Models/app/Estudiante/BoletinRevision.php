<?php

namespace App\Models\app\Estudiante;

use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Pensum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoletinRevision extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'estudiant_id',
        'pensum_id',
        'profesor_id',
        'numero',
        'nota',
        'description',
        'observations',
        'status_active',
        'type'
    ];

    const TYPES_LIST = [
        'REVISION' => 'REVISIÓN',
        'EQUIVALENCIA' => 'EQUIVALENCIA',
        'TRASLADO' => 'TRASLADO',
        'OTRO' => 'OTRO',
    ];

    const COLUMN_COMMENTS = [
        'estudiant_id' => 'Estudiante',
        'pensum_id' => 'Pensum',
        'profesor_id' => 'Profesor',
        'numero' => 'Número de Revisión',
        'nota' => 'Nota',
        'description' => 'Descripción',
        'observations' => 'Observaciones',
        'status_active' => 'Estado',
        'type' => 'Tipo',
    ];

    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant');
    }
    public function pensum()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Pensum');
    }
    public function profesor()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Profesor');
    }

    public function getLiteralAttribute()
    {
        $pensum = Pensum::find($this->pensum_id);
        $pestudio = $pensum->pestudio;

        $literal = Baremo::getLiteral($pestudio->id, $this->nota);

        return $literal;
    }
}
