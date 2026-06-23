<?php

namespace App\Models\app\Estudiante;

use App\Models\app\Estudiant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Retiro extends Model
{
    // protected $guarded = ['id','deleted_at','created_at','updated_at'];
    protected $fillable = [
        'estudiant_id','user_id','tipo','status_admon','status_control','observations'
    ];

    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant','estudiant_id');
    }
    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    public function getEstudiantAttribute()
    {
        return Estudiant::withTrashed()->where('id',$this->estudiant_id)->first();
    }

    public static function getEstudiants()
    {
        return DB::table('estudiants')
            ->select('estudiants.*')
            ->join('retiros', 'estudiants.id', '=', 'retiros.estudiant_id')
            ->join('registro_pagos', 'estudiants.id', '=', 'registro_pagos.estudiant_id')
            ->GroupBy('estudiants.id')
            ->get();
    }

}
