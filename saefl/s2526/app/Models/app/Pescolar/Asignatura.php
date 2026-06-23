<?php

namespace App\Models\app\Pescolar;

use App\Models\app\Estudiante\Boletin;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\app\Pescolar\Functions\Asignatura\Lists;

class Asignatura extends Model
{
    // use SoftDeletes;
    // protected $guarded = ['id','deleted_at','created_at','updated_at'];
    use Lists;

    protected $fillable = [
        'pestudio_id','escala_id','code','code_sm','name','order','hour_t_week','hour_p_week','unid_credit','approved_credit_unir','enable_academic_index','enable_lost_regulation','enable_official_doc','enable_repairable','enable_grupo_estable','observations','prelacions'
    ];
    const COLUMN_COMMENTS = [
        'pestudio_id' => 'Plan Estudio',
        'code' => 'Código',
        'code_sm' => 'Código corto',
        'name' => 'Nombre',
        'tescala' => 'Tipo de Evaluación',
        'order' => 'Número de orden de presentación de la asignatura',
        'hour_t_week' => 'Número de horas teóricas dictadas en la semana',
        'hour_p_week' => 'Número de horas prácticas dictadas en la semana',
        'unid_credit' => 'Número de unidades de crédito',
        'approved_credit_unir' => 'Unidades de Crédito Aprobadas',
        'enable_academic_index' => 'Tomada en cuenta para índice o promedio académico',
        'enable_lost_regulation' => 'Sujeta a pérdida por reglamento',
        'enable_official_doc' => 'Mostrar en documentos oficiales',
        'enable_repairable' => 'Reparable',
        'enable_grupo_estable' => 'Contiene grupo estable',
        'observations' => 'Observaciones',
        'prelacions' => 'Prelaciones',
    ];

    public function pestudio()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Pestudio');
    }
    public function escala()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Escala','escala_id');
    }
    public function pensums()
    {
        return $this->hasMany(Pensum::class, 'asignatura_id');
    }
    public function prelacions()
    {
        return $this->hasMany('App\Models\app\Pescolar\Prelacion','asignatura_id');
    }
    public function prelacions_p()
    {
        return $this->hasMany('App\Models\app\Pescolar\Prelacion','asignatura_p_id');
    }
    public function campo_conocimientos()
    {
        return $this->hasMany('App\Models\app\Pescolar\CampoConocimiento');
    }
    public function campoConocimientos()
    {
        return $this->hasMany(CampoConocimiento::class);
    }

    public function getFullNameAttribute()
    {
        return '['.$this->code .'] ' . $this->name;
    }

    public function getPensumAttribute()
    {
        $pensum = Pensum::where('asignatura_id',$this->id)->OrderBy('created_at','desc')->first();
        return $pensum;
    }

    /*******************************************************************************************/


}
