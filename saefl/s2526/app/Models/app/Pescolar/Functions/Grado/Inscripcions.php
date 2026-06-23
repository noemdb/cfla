<?php
namespace App\Models\app\Pescolar\Functions\Grado;

use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Pescolar\Pestudio;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

trait Inscripcions {

    public function inschistories()
    {
        $inscripcions = Grado::select('grados.name',DB::raw('count(estudiants.id) as value'))
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inschistories', 'seccions.id', '=', 'inschistories.seccion_id')
            ->join('estudiants', 'estudiants.id', '=', 'inschistories.estudiant_id')
            ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->Where('grados.id', '=', $this->id)
            ->Where('grados.id', '<>', 11)
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->whereNull('inscripcions.id')
            ->groupby('grados.id')
            ->first();
        return ($inscripcions) ? $inscripcions : 0;
    }

    //funciones para los chart
    public function inscritos()
    {
        $inscripcions = Estudiant::select('estudiants.*','grados.name',DB::raw('count(grados.id) as value'))
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('grados', 'seccions.grado_id', '=', 'grados.id')
            //->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            //->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id') 
            //->where('planpagos.status_inscription_affects','true')
            ->Where('grados.id', '=', $this->id)
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('grados.id')
            ->first();

        return ($inscripcions) ? $inscripcions : 0;
    }

    public function retirados()
    {
        $inscripcions = Estudiant::select('grados.name',DB::raw('count(grados.id) as value'))
            ->join('retiros', 'estudiants.id', '=', 'retiros.estudiant_id')
            ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->leftjoin('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->leftjoin('grados', 'seccions.grado_id', '=', 'grados.id')
            ->Where('retiros.tipo', '=', 'control')
            ->Where('grados.id', '=', $this->id)
            ->groupby('grados.id')
            ->first();
        return ($inscripcions) ? $inscripcions : 0;
    }
    public function varones()
    {
        $inscripcions = Grado::select('grados.name',DB::raw('count(grados.id) as value'))
            ->join('pestudios', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            //->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            //->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            //->where('planpagos.status_inscription_affects','true')
            ->Where('grados.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->where('estudiants.gender','Masculino')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('grados.id')
            ->first();
            // ->value;
        return ($inscripcions) ? $inscripcions : 0;
    }

    public function hembras()
    {
        $inscripcions = Grado::select('grados.name',DB::raw('count(grados.id) as value'))
            ->join('pestudios', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            //->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            //->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            //->where('planpagos.status_inscription_affects','true')
            ->Where('grados.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->where('estudiants.gender','Femenino')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('grados.id')
            ->first();
        return ($inscripcions) ? $inscripcions : 0;
    }

    //funciones para los chart
    public function a_inscritos()
    {
        $administrativas = Grado::select('grados.name',DB::raw('count(administrativas.id) as value'))
            ->join('pestudios', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->Where('grados.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->where('planpagos.status_inscription_affects','true')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('grados.id')
            ->first();
        return ($administrativas) ? $administrativas : 0;
    }
    public function a_varones()
    {
        $inscripcions = Grado::select('grados.name',DB::raw('count(grados.id) as value'))
            ->join('pestudios', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->Where('grados.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->where('planpagos.status_inscription_affects','true')
            ->where('planpagos.status_active','true')
            ->where('estudiants.gender','Masculino')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('grados.id')
            ->first();
            // ->value;
        return ($inscripcions) ? $inscripcions : 0;
    }
    public function a_hembras()
    {
        $inscripcions = Grado::select('grados.name',DB::raw('count(grados.id) as value'))
            ->join('pestudios', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->Where('grados.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->where('planpagos.status_inscription_affects','true')
            ->where('planpagos.status_active','true')
            ->where('estudiants.gender','Femenino')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('grados.id')
            ->first();
            // ->value;
        return ($inscripcions) ? $inscripcions : 0;
    }
    public function a_retirados()
    {
        $inscripcions = Pestudio::select('grados.name',DB::raw('count(grados.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('retiros', 'estudiants.id', '=', 'retiros.estudiant_id')
            ->Where('retiros.tipo', '=', 'admon')
            ->Where('grados.id', '=', $this->id)
            ->wherenull('inscripcions.deleted_at')
            ->groupby('pestudios.id')
            ->first();
        return ($inscripcions) ? $inscripcions : 0;
    }

    public function getArrEstudiantGender($gender,Carbon $dateEnd)
    {
        // dd($dateEnd);
        $dateStart  = $dateEnd->copy()->startOfMonth(); //dd($dateStart);
        $estudiants = Estudiant::select('estudiants.*')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('grados', 'seccions.grado_id', '=', 'grados.id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->Where('estudiants.gender', $gender)
            ->Where('grados.id', $this->id)
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            // ->where('inscripcions.deleted_at','<=',$dateStart)
            ->where('inscripcions.created_at','<=',$dateEnd)
            ->wherenull('inscripcions.deleted_at')
            ->wherenull('seccions.deleted_at')
            ->wherenull('grados.deleted_at')
            ->where('planpagos.status_inscription_affects','true')
            ->where('planpagos.status_active','true')
            ->get();

        $data = Array();
        foreach ($estudiants as $estudiant) {
            $age = $estudiant->getAgeDate($dateEnd); //dd($age);
            $data[$age] = (array_key_exists($age,$data)) ? ($data[$age] + 1) : 1 ;
        }

        //dd($data);

        // $data = collect([
        //     '16'=>'12',
        //     '17'=>'14',
        //     '18'=>'16',
        // ]);

        return $data;
    }

    public function others()
    {
        $inscripcions = Pestudio::select('pestudios.name',DB::raw('count(estudiants.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->Where('grados.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('planpagos.status_inscription_affects','true')
            ->where('planpagos.status_active','true')
            ->where('estudiants.status_active','true')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('grados.id')
            // ->where('estudiants.gender','<>','Femenino')
            ->wherenull('estudiants.gender')
            ->first();
        return ($inscripcions) ? $inscripcions : 0;
    }

}
