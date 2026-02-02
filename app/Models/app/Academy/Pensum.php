<?php

namespace App\Models\app\Academy;

use App\Models\app\Instrument\DiagQuestion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pensum extends Model
{
    use HasFactory;

    protected $fillable = [
        'pestudio_id',
        'grado_id',
        'asignatura_id',
        'status_component',
        'status_active_diagnostic',
        'observations',
    ];

    protected $casts = [
        'status_active_diagnostic' => 'boolean',
    ];

    const COLUMN_COMMENTS = [
        'pestudio_id' => 'Plan Estudio',
        'grado_id' => 'Grado',
        'asignatura_id' => 'Asignatura',
        'status_component' => 'Contiene componentes de Formación?',
        'observations' => 'Observación',
        'status_active_diagnostic' => 'Activo para diagnostico',
    ];

    public function diagQuestions()
    {
        return $this->hasMany(DiagQuestion::class, 'pensum_id');
    }

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'asignatura_id');
    }

    public function pestudio()
    {
        return $this->belongsTo(Pestudio::class, 'pestudio_id');
    }

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'grado_id');
    }
}
