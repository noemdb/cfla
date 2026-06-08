<?php
namespace App\Models\app\Estudiante\Functions\Estudiant;

use App\Models\app\Estudiante\GrupoEstable;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Planpago\Cuentaxpagar;
use Illuminate\Support\Facades\DB;

trait Inscripcions
{

    public static function getMunicipios($state="YARACUY",$operator="=")
    {
        $municipios =
            DB::table('estudiants')
                ->select('estudiants.town_hall_birth','estudiants.state_birth')
                // ->selectRaw('count(town_hall_birth) as count_town_hall_birth')
                ->selectRaw('count(estudiants.id) as count_id')
                ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
                ->GroupBy('town_hall_birth')
                ->OrderBy('count_id','desc')
                // ->WhereNotNull('town_hall_birth')
                ->where('estudiants.town_hall_birth','<>','NULL')
                ->where('estudiants.state_birth','<>','NULL')
                ;

        $municipios = (!empty($state)) ? $municipios->Where('state_birth',$operator,$state) : $municipios ;

        $municipios = $municipios->get();

        return $municipios ;

    }

    public static function getMunicipiosValues($town_hall_birth,$state="YARACUY",$operator="=")
    {
        $pestudios_values =
            DB::table('estudiants')
                ->select('estudiants.town_hall_birth')
                ->selectRaw('count(estudiants.id) as count_id')
                ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
                // ->where('estudiants.town_hall_birth',$town_hall_birth)
                ->where('estudiants.state_birth','<>','NULL')
                ->where('estudiants.town_hall_birth','<>','NULL')
                // ->WhereNotNull('town_hall_birth')
                ->GroupBy('pestudios.id')
                ->OrderBy('pestudios.id');

        $pestudios_values = (!empty($town_hall_birth)) ? $pestudios_values->Where('town_hall_birth',$town_hall_birth) : $pestudios_values ;

        $pestudios_values = (!empty($state)) ? $pestudios_values->Where('state_birth',$operator,$state) : $pestudios_values ;

        $pestudios_values = $pestudios_values->get()->pluck('count_id')->toArray();

        return $pestudios_values ;
    }

    public function getInscripcion()
    {
        if (! empty($this->inscripcion)) {
            $inscripcion = $this->inscripcion;
            if (! empty($inscripcion->seccion)) {
                $seccion = $inscripcion->seccion;
                return ($seccion->status_active=="true") ? $inscripcion : null;
            }
        }
    }

    public function getSeccionAttribute()
    {
        if (! empty($this->inscripcion)) {
            $inscripcion = $this->inscripcion;
            if (! empty($inscripcion->seccion)) {
                $seccion = $inscripcion->seccion;
                return ($seccion->status_active=="true") ? $seccion : null;
            }
        }
    }

    public function getGradoAttribute()
    {
        $inscripcion = $this->inscripcion;
        if (! empty($inscripcion)) {
            $seccion = $this->seccion;
            if (! empty($seccion)) {
                if ($seccion->status_active=="true") {
                    $grado = $seccion->grado;
                    return $grado;
                }
            }
        }
    }

    public function getFullInscripcionAttribute()
    {
        $inscripcion = $this->inscripcion;
        if (! empty($inscripcion)) {
            $seccion = $inscripcion->seccion;
            if (! empty($seccion)) {
                if ($seccion->status_active=="true") {
                        $grado = $seccion->grado;
                    if (! empty($grado)) {
                        return "{$grado->name} {$seccion->name}";
                    }
                }
            }
        }
    }

    // public function gradoSeccion()
    // {
    //     $inscripcion = $this->inscripcion;
    //     if (! empty($inscripcion)) {
    //         $seccion = $inscripcion->seccion;
    //         if (! empty($seccion)) {
    //             if ($seccion->status_active=="true") {
    //                     $grado = $seccion->grado;
    //                 if (! empty($grado)) {
    //                     return "{$grado->name} {$seccion->name}";
    //                 }
    //             }
    //         }
    //     }
    // }

    public function getEnableInscriptionAttribute()
    {
        $id = $this->id;
        $planpago_id = ( !empty($this->administrativa->planpago_id) ) ? $this->administrativa->planpago_id : '0' ;
        $cta_x_pagars =
            Cuentaxpagar::where('planpago_id',$planpago_id)
                ->Where('cuentaxpagars.status_inscription','true')
                ->Where('cuentaxpagars.type','GENERAL')
                ->orWhere(function($q) use ($id){
                    $q->where('cuentaxpagars.type','INDIVIDUAL')
                    ->where('cuentaxpagars.estudiant_id',$id);
                })
                ->get();
        $total=0;
        foreach ($cta_x_pagars as $cta_x_pagar) {
            $total = $total + $cta_x_pagar->TotalMontoConceptosXPagar($this->id);
        }
        return ($total==0) ? true:false;
    }

    public function getGradoPromocionAttribute()
    {
        $grado_id = $this->grado->id;

        $grado = Grado::find($grado_id + 1);

        return ($grado) ? $grado : null;
    }

    public function getProfesorGuiaAttribute()
    {

        $profesor = Profesor::select('profesors.*')
            ->join('profesor_guias', 'profesors.id', '=', 'profesor_guias.profesor_id')
            ->join('seccions', 'seccions.id', '=', 'profesor_guias.seccion_id')
            ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('estudiants.id',$this->id)
            ->groupBy('profesors.id')
            ->OrderBy('profesor_guias.created_at')
            ->first();
        return (!empty($profesor)) ? $profesor:null;
    }

    public function isProfesorGuia($profesor_id,$lapso_id=null)
    {
        // dd($profesor_id,$lapso_id);
        $profesor = Profesor::select('profesors.*')
            ->join('profesor_guias', 'profesors.id', '=', 'profesor_guias.profesor_id')
            ->join('seccions', 'seccions.id', '=', 'profesor_guias.seccion_id')
            ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('estudiants.id',$this->id)
            ->where('profesors.id',$profesor_id);

        $profesor = ($lapso_id) ? $profesor->where('profesor_guias.lapso_id',$lapso_id) : $profesor ;

        $profesor = $profesor->first();

        return ($profesor) ? true : false ;
    }

    public function getProfesorGuia($lapso_id)
    {
        $profesor = Profesor::select('profesors.*')
            ->join('profesor_guias', 'profesors.id', '=', 'profesor_guias.profesor_id')
            ->join('seccions', 'seccions.id', '=', 'profesor_guias.seccion_id')
            ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('estudiants.id',$this->id)
            ->where('profesor_guias.lapso_id',$lapso_id)
            ->groupBy('profesors.id')
            ->OrderBy('profesor_guias.created_at')
            ->first();
        return (!empty($profesor)) ? $profesor : null ;
    }

    public function getEntidadFederalAttribute()
    {
        $state_birth = trim($this->state_birth);
        switch ($state_birth) {
            case 'AMAZONAS': $ef = 'AM'; break;
            case 'ANZOATEGUI': $ef = 'AN'; break;
            case 'APURE': $ef = 'AP'; break;
            case 'ARAGUA': $ef = 'AR'; break;
            case 'BARINAS': $ef = 'BA'; break;
            case 'BOLIVAR': $ef = 'BO'; break;
            case 'CARABOBO': $ef = 'CA'; break;
            case 'COJEDES': $ef = 'CO'; break;
            case 'DELTA AMACURO': $ef = 'DA'; break;
            case 'DISTRITO CAPITAL': $ef = 'DC'; break;
            case 'FALCON': $ef = 'FA'; break;
            case 'GUARICO': $ef = 'GU'; break;
            case 'VARGAS': $ef = 'VA'; break;
            case 'LARA': $ef = 'LA'; break;
            case 'MERIDA': $ef = 'ME'; break;
            case 'MIRANDA': $ef = 'MI'; break;
            case 'MONAGAS': $ef = 'MO'; break;
            case 'NUEVA ESPARTA': $ef = 'NE'; break;
            case 'PORTUGUESA': $ef = 'PO'; break;
            case 'SUCRE': $ef = 'SU'; break;
            case 'TACHIRA': $ef = 'TA'; break;
            case 'TRUJILLO': $ef = 'TR'; break;
            case 'YARACUY': $ef = 'YA'; break;
            case 'ZULIA': $ef = 'ZU'; break;
            default: $ef = 'EX'; break;
        }
        return $ef;
    }

    public function getGrupoEstableAttribute()
    {
        $grupo_estable = GrupoEstable::select('grupo_estables.*')
        ->join('inscripcions', 'grupo_estables.id', '=', 'inscripcions.grupo_estable_id')
        ->where('inscripcions.estudiant_id',$this->id)
        ->first();
        return $grupo_estable ;
    }

    public function getGrupoEstableIdAttribute()
    {
        return ($this->grupo_estable) ? $this->grupo_estable->id : null ;
    }

    public function getCiFullAttribute()
    {
        switch ($this->country_birth) {
            case 'VENEZUELA': $nacionalidad = 'V'; break;
            default: $nacionalidad = 'EX'; break;
        }
        return $nacionalidad.$this->ci_estudiant;
    }

    public function getCiFull($separete='-')
    {
        switch ($this->country_birth) {
            case 'VENEZUELA': $nacionalidad = 'V'; break;
            default: $nacionalidad = 'E'; break;
        }
        // return $nacionalidad.$separete.$this->ci_estudiant;
        return $this->ci_estudiant;
    }

    public function getCiFullF2($separete='-')
    {
        switch ($this->country_birth) {
            case 'VENEZUELA': $nacionalidad = 'V'; break;
            default: $nacionalidad = 'E'; break;
        }
        return $nacionalidad. ' ' .$this->formatted_ci;
    }

    public function getFormattedCiAttribute()
    {
        // Eliminar cualquier carácter que no sea número
        $cleanCi = preg_replace('/\D/', '', $this->ci_estudiant);

        // Si tiene más de 8 dígitos, tomar los últimos 8
        if (strlen($cleanCi) > 8) {
            $cleanCi = substr($cleanCi, -8);
        } else {
            $cleanCi = str_pad($cleanCi, 8, '0', STR_PAD_LEFT);
        }

        // Formato XX.XXX.XXX
        return substr($cleanCi, 0, 2) . '.' . substr($cleanCi, 2, 3) . '.' . substr($cleanCi, 5, 3);
    }

}
