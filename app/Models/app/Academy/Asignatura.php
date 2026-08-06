<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asignatura extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pestudio_id', 'code', 'code_sm', 'name', 'tescala', 'order',
        'hour_t_week', 'hour_p_week', 'unid_credit', 'approved_credit_unir',
        'enable_academic_index', 'enable_lost_regulation', 'enable_official_doc',
        'enable_repairable', 'enable_grupo_estable',
        'observations', 'prelacions',
    ];

    public function pestudio()
    {
        return $this->belongsTo(Pestudio::class, 'pestudio_id');
    }

    public function pensums()
    {
        return $this->hasMany(Pensum::class, 'asignatura_id');
    }

    public function areasConocimiento()
    {
        return $this->belongsToMany(
            AreaConocimiento::class,
            'campo_conocimientos',
            'asignatura_id',
            'area_conocimiento_id'
        );
    }

    public function scopeActive($query, $flag = 'true')
    {
        return $query->where('asignaturas.status_active', $flag);
    }

    /**
     * D2 · Color por materia.
     *
     * Devuelve una clave de paleta estable (sky|emerald|amber|indigo|purple|
     * orange|rose|teal|slate) a partir del nombre de la asignatura. Estable =
     * la misma asignatura siempre pinta el mismo color (mismo render, claro y
     * oscuro); cubre cualquier nombre (sin columna de color editable).
     *
     * Normaliza mayúsculas/acentos, busca en un mapa semántico ordenado
     * (primera coincidencia gana) y, si no hay coincidencia, cae a un hash
     * determinista (crc32) sobre la paleta. null/vacío → slate (neutro).
     */
    public static function colorKey(?string $name): string
    {
        if ($name === null || $name === '') {
            return 'slate';
        }

        $normalized = mb_strtoupper($name);
        $normalized = strtr($normalized, [
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'Ñ' => 'N', 'Ü' => 'U',
        ]);
        $normalized = preg_replace('/\s+/', ' ', trim($normalized));

        // Tras normalizar, un nombre de solo espacios también es "vacío".
        if ($normalized === '') {
            return 'slate';
        }

        // Orden CRÍTICO: 'INGLES' antes de 'LENGUA' porque
        // 'INGLES Y OTRAS LENGUAS EXTRANJERAS' contiene 'LENGUAS' → 'LENGUA'.
        $map = [
            'MATEMATIC' => 'sky', 'INGLES' => 'indigo', 'LENGUA' => 'emerald',
            'CASTELLAN' => 'emerald', 'CIENC' => 'amber',
            'FISICA' => 'orange', 'DEPORTE' => 'orange',
            'ESTETIC' => 'purple', 'ARTE' => 'purple', 'MUSIC' => 'purple',
            'FORMACION' => 'rose', 'RELIGION' => 'rose', 'HUMANO' => 'rose',
            'CRISTIAN' => 'rose',
        ];

        foreach ($map as $needle => $key) {
            if (str_contains($normalized, $needle)) {
                return $key;
            }
        }

        $palette = ['sky', 'emerald', 'amber', 'indigo', 'purple', 'orange', 'rose', 'teal'];

        return $palette[crc32($normalized) % count($palette)];
    }

    public function getFullNameAttribute()
    {
        return '['.$this->code.'] '.$this->name;
    }
}
