<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatchmentGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'grado_id',
        'name',
        'description',
        'max',
        'min',
        'status_active'
    ];

    const COLUMN_COMMENTS = [
        'id' => 'ID del grupo de captación',
        'grado_id' => 'Grado del Plan de Estudio',
        'name' => 'Nombre',
        'description' => 'Descripción',
        'max' => 'Cantidad máxima de participantes',
        'min' => 'Cantidad mínima de participantes',
        'status_active' => '¿El grupo de captación está activo?',
    ];

    public function catchments() { return $this->hasMany(Catchment::class,'group_id'); }
    public function activities() { return $this->hasMany(CatchmentActivity::class,'group_id'); }
    public function grado() { return $this->belongsTo(Grado::class,'grado_id'); }

    public static function list_group() /* usada para llenar los objetos de formularios select*/
    {
        return CatchmentGroup::all()->pluck('name','id');
    }

    public static function list_grado_group() /* usada para llenar los objetos de formularios select*/
    {
        $grados = Grado::all(); //dd($grados);
        $datas_grados = collect();
        foreach ($grados as $grado) {
            $pestudio = $grado->pestudio;
            if ($pestudio->status_inscripcion_active) {
                $name = $pestudio->code.'-'.$grado->name;
                $datas_grados->put($name, $grado->catchment_groups()->pluck('name', 'id'));
            }
        }
        return $datas_grados;
    }
}