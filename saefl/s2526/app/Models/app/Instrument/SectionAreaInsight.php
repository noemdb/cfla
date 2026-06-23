<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SectionAreaInsight extends Model
{
    use HasFactory;

    /**
     * Nombre explícito de la tabla
     */
    protected $table = 'section_area_insights';

    /**
     * Atributos asignables en masa
     */
    protected $fillable = [
        'section_area_result_id',
        'type',
        'description',
        'frequency',
    ];

    /**
     * Casts de tipos
     */
    protected $casts = [
        'frequency' => 'integer',
    ];

    /**
     * =========================
     * RELACIONES
     * =========================
     */

    /**
     * Resultado de área al que pertenece este insight
     */
    public function areaResult()
    {
        return $this->belongsTo(
            SectionAreaResult::class,
            'section_area_result_id'
        );
    }
}
