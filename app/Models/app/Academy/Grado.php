<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Grado extends Model
{
    use HasFactory;

    protected $fillable = [
        'pestudio_id', 'name', 'code', 'code_sm', 'description', 'status_active'
    ];

    const COLUMN_COMMENTS = [
        'pestudio_id' => 'Plan Estudio',
        'name' => 'Nombre',
        'code' => 'Código',
        'code_sm' => 'Código reducido',
        'description' => 'Descripción',
        'status_active' => 'Estado'
    ];

    public function pestudio()
    {
        return $this->belongsTo(Pestudio::class, 'pestudio_id');
    }
    public function seccions()
    {
        return $this->hasMany(Seccion::class);
    }

    //scope
    public function scopeActive($query, $flag) {
        return $query->where('status_active', $flag);
    }

    public static function list_inscripcion_grado() /* usada para llenar los objetos de formularios select*/
    {
        return DB::table('grados')->select('grados.*')
        ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
        ->where('pestudios.status_inscripcion_active',true)
        ->pluck('grados.name','grados.id');
    }

    public static function list_grado() /* usada para llenar los objetos de formularios select*/
    {
        return Grado::active('true')->pluck('name','id');
    }

    public static function list_pestudio_grado() /* usada para llenar los objetos de formularios select*/
    {
        $pestudios = Pestudio::active('true')->get(); //dd($pestudios);
        $datas_grados = collect();
        foreach ($pestudios as $pestudio) {
            $datas_grados->put($pestudio->code.'-'.$pestudio->name, $pestudio->getGradosActive()->pluck('name', 'id'));
        }
        return $datas_grados;
    }

    public static function list_grado_iu() /* usada para llenar los objetos de formularios select*/
    {
        $datas = [];
        $pestudios = Pestudio::active('true')->get();
        foreach ($pestudios as $pestudio) {
            $grados = $pestudio->getGradosActive(); //dd($grados);
            foreach ($grados as $grado) {
                $datas [] = [
                    'id'=> $grado->id,
                    'name'=> $grado->name,
                    'description'=> $pestudio->name,
                ];
                // $datas->put($arr);
            }            
        }
        return $datas;
    }

    public static function list_grado_iu2() /* usada para llenar los objetos de formularios select*/
    {
        $datas = []; //dd($datas);
        $peducativos = Peducativo::active('true')->get(); //dd($peducativos);
        foreach ($peducativos as $peducativo) {
            $grados = $peducativo->grados; //dd($grados);
            foreach ($grados as $grado) {
                $pestudio = $grado->pestudio;
                $datas [] = [
                    'id'=> $grado->id,
                    'name'=> $grado->name,
                    'description'=> $pestudio->name,
                ];
                // $datas->put($arr);
            }            
        }
        return $datas;
    }
}
