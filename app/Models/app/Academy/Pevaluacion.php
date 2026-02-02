<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pevaluacion extends Model
{
    use HasFactory;

    protected $fillable = ['profesor_id', 'lapso_id', 'seccion_id', 'pensum_id', 'grupo_estable_id', 'status_baremo', 'status_official', 'status_note_report', 'nota_type', 'escala_id', 'objetivo', 'description', 'observations', 'category', 'deleted_at'];

    const COLUMN_COMMENTS = [
        'profesor_id' => 'Profesor',
        'lapso_id' => 'Momento',
        'seccion_id' => 'Sección',
        'pensum_id' => 'Área de Formación',
        'grupo_estable_id' => 'Grupo Estable',
        'status_baremo' => 'Baremo',
        'status_official' => 'En documentos oficiales',
        'nota_type' => 'Tipo de noata',
        'escala_id' => 'Escala',
        'objetivo' => 'Objetivo',
        'description' => 'Descripción',
        'observations' => 'Observaciones',
        'category' => 'Category',
        'deleted_at' => 'Fecha de Eliminación',
        'grado_id' => 'Grado/Año',
        'pestudio_id' => 'Plan Estudio',
        'status_note_report' => 'En Informe de Notas',
    ];

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }

    public function lapso()
    {
        return $this->belongsTo(Lapso::class, 'lapso_id');
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    public function pensum()
    {
        return $this->belongsTo(Pensum::class, 'pensum_id');
    }

    public function grado()
    {
        return $this->hasOneThrough(
            Grado::class,
            Seccion::class,
            'id',           // Foreign key on Seccion (intermediate) table
            'id',           // Foreign key on Grado (related) table
            'seccion_id',   // Local key on Pevaluacion (starting) table
            'grado_id'      // Local key on Seccion (intermediate) table
        );
    }

    public function pestudio()
    {
        return $this->hasOneThrough(
            Pestudio::class,
            Pensum::class,
            'id',           // Foreign key on Pensum (intermediate) table
            'id',           // Foreign key on Pestudio (related) table
            'pensum_id',    // Local key on Pevaluacion (starting) table
            'pestudio_id'   // Local key on Pensum (intermediate) table
        );
    }
}
