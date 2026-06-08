<?php

namespace App\Models\app\Incident;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'corrective_id',
        'description',
        'status_selected',
    ];

    public function incident() { return $this->belongsTo(Incident::class,'incident_id'); }
    public function incident_corrective() { return $this->belongsTo(IncidentCorrective::class,'corrective_id'); }

    const COLUMN_COMMENTS = [
        'incident_id' => 'Incidencia asociada.',
        'corrective_id' => 'Correctivo.',
        'description' => 'Descripción.',
        'status_selected' => 'Seleccionado.',
    ];
}
