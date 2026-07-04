<?php
namespace App\Models\app\Poll\Functions;

///home/nuser/code/s2223/app/Models/app/Poll/Funtions/PollGroupTrait.php

use App\Models\app\Pescolar\Lapso;
use App\User;
use Carbon\Carbon;

trait PollGroupTrait
{
    public function getAttendeesAttribute()
    {
        $poll_group = $this->poll_group;
        $attendees  = collect();
        $now        = Carbon::now()->format('Y-m-d');
        $lapso      = Lapso::current();

        switch ($poll_group->code) {
            case 'GR1':
                $representants =
                User::OrderBy('users.id')
                    ->select('users.id as id', 'representants.ci_representant as ci', 'representants.email as email', 'estudiants.dir_address as dir_address', 'representants.name as fullname', 'rols.rol as rol', 'rols.area as area')
                    ->join('representants', 'users.id', '=', 'representants.user_id')
                    ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
                    ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                    ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                    ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                    ->join('rols', 'users.id', '=', 'rols.user_id')

                    ->where('grados.id', '<>', 11)
                    ->where('rols.area', 'REPRESENTANTE')
                    ->Where('rols.finicial', '<=', $now)
                    ->Where('rols.ffinal', '>=', $now)
                    ->where('representants.status_active', 'true')
                    ->where('estudiants.status_active', 'true')
                    ->where('seccions.status_inscription_affects', 'true')

                    ->groupBy('representants.id')
                    ->orderby('representants.ci_representant', 'asc');

                $arr_ci        = explode(',', $this->ci_list);
                $representants = (! empty($this->ci_list)) ? $representants->whereIn('representants.ci_representant', $arr_ci) : $representants;

                $representants = $representants->get();
                $attendees     = $attendees->concat($representants);

                break;

            case 'GR2':
                $representants =
                User::OrderBy('users.id')
                    ->select('users.id as id', 'representants.ci_representant as ci', 'representants.email as email', 'estudiants.dir_address as dir_address', 'representants.name as fullname', 'rols.rol as rol', 'rols.area as area')
                    ->join('representants', 'users.id', '=', 'representants.user_id')
                    ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
                    ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                    ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                    ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                    ->join('rols', 'users.id', '=', 'rols.user_id')

                    ->where('rols.area', 'REPRESENTANTE')

                // ->Where('rols.finicial', '<=', $now)
                // ->Where('rols.ffinal', '>=', $now)

                    ->where('representants.status_active', 'true')
                    ->where('estudiants.status_active', 'true')
                    ->where('seccions.status_inscription_affects', 'true')

                    ->groupBy('representants.id')
                    ->orderby('representants.ci_representant', 'asc');

                $arr_ci        = explode(',', $this->ci_list);
                $representants = (! empty($this->ci_list)) ? $representants->whereIn('representants.ci_representant', $arr_ci) : $representants;

                $representants = $representants->get();
                $attendees     = $attendees->concat($representants);
                //dd($attendees->take(10));

                break;

            case 'GR5':

                $autoridads =
                User::OrderBy('users.id')
                    ->select('users.id as id', 'users.number_id as ci', 'autoridads.ci as autoridad_ci', 'users.email as email', 'profiles.dir_address as dir_address', 'rols.rol as rol', 'rols.area as area')
                    ->selectRaw("CONCAT(autoridads.name,' ',autoridads.lastname) as fullname")

                    ->join('autoridads', 'users.id', '=', 'autoridads.user_id')
                    ->join('rols', 'users.id', '=', 'rols.user_id')
                    ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')

                    ->whereIn('rols.area', ['AUTORIDAD', 'DIRECCION', 'ACADEMICO', 'CONOCIMIENTO', 'BIENESTAR'])
                    ->whereIn('rols.rol', ['DIRECTOR', 'COORDINADOR'])

                // ->Where('autoridads.finicial', '<=', $now)
                // ->Where('autoridads.ffinal', '>=', $now)

                    ->groupBy('users.id')
                    ->orderby('users.username', 'asc')
                    ->get(); //dd($autoridads);
                $list_ci_autoridad = $autoridads->pluck('autoridad_ci')->toArray();

                $list_ci_representant = [];
                $representants        = collect();
                if ($this->status_representant == "false") {
                    $representants =
                    User::OrderBy('users.id')
                        ->select('users.id as id', 'representants.ci_representant as ci', 'representants.email as email', 'estudiants.dir_address as dir_address', 'representants.name as fullname', 'rols.rol as rol', 'rols.area as area')
                        ->join('representants', 'users.id', '=', 'representants.user_id')
                        ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
                        ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                        ->join('rols', 'users.id', '=', 'rols.user_id')
                        ->where('rols.area', 'REPRESENTANTE')
                        ->Where('finicial', '<=', $now)
                        ->Where('ffinal', '>=', $now)
                        ->whereNotIn('representants.ci_representant', $list_ci_autoridad)
                        ->where('representants.status_active', 'true')
                        ->where('estudiants.status_active', 'true')
                        ->groupBy('representants.id')
                        ->orderby('representants.ci_representant', 'asc')
                        ->get();
                    dd($representants);
                    $list_ci_representant = $representants->pluck('ci')->toArray();
                }

                $estudiants = collect();
                if ($this->status_estudiant == "true") {
                    $estudiants =
                    User::OrderBy('users.id')
                        ->select('users.id as id', 'estudiants.ci_estudiant as ci', 'estudiants.gsemail as email', 'estudiants.dir_address as dir_address', 'rols.rol as rol', 'rols.area as area')
                        ->selectRaw("CONCAT(estudiants.name,' ',estudiants.lastname) as fullname")
                        ->join('rols', 'users.id', '=', 'rols.user_id')
                        ->join('estudiants', 'users.id', '=', 'estudiants.user_id')
                        ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                        ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                        ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                        ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                        ->where('planpagos.status_inscription_affects', 'true')
                        ->where('seccions.status_active', 'true')
                        ->where('rols.area', 'ESTUDIANTIL')
                    // ->Where('rols.finicial', '<=', $now)
                    // ->Where('rols.ffinal', '>=', $now)
                        ->where('estudiants.status_active', 'true')
                        ->groupBy('estudiants.id')
                        ->orderby('estudiants.ci_estudiant', 'asc')
                        ->get(); //dd($estudiants);
                    $list_ci_estudiant = $estudiants->pluck('ci')->toArray();
                }

                $profesors =
                User::OrderBy('users.id')
                    ->select('users.id as id', 'profesors.ci_profesor as ci', 'profesors.gsemail as email', 'profesors.dir_address as dir_address', 'rols.rol as rol', 'rols.area as area')
                    ->selectRaw("CONCAT(profesors.name,' ',profesors.lastname) as fullname")
                    ->join('profesors', 'users.id', '=', 'profesors.user_id')
                    ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
                    ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')
                    ->join('rols', 'users.id', '=', 'rols.user_id')
                    ->whereNotIn('profesors.ci_profesor', $list_ci_representant)
                    ->whereNotIn('profesors.ci_profesor', $list_ci_autoridad)
                    ->Where('rols.finicial', '<=', $now)
                    ->Where('rols.ffinal', '>=', $now)
                    ->Where('lapsos.finicial', '<=', $now)
                    ->Where('lapsos.ffinal', '>=', $now)
                    ->where('profesors.status_active', 'true')
                    ->wherenull('pevaluacions.deleted_at')
                    ->groupBy('profesors.id')
                    ->orderby('profesors.ci_profesor', 'asc')
                    ->get();                                                //dd($profesors,$list_ci_autoridad);
                $list_ci_profesor = $profesors->pluck('ci')->toArray(); //dd($list_ci_profesor);

                $workers =
                User::OrderBy('users.id')
                    ->select('users.id as id', 'users.number_id as ci', 'users.email as email', 'profiles.dir_address as dir_address', 'rols.rol as rol', 'rols.area as area', 'users.number_id')
                    ->selectRaw("CONCAT(profiles.firstname,' ',profiles.lastname) as fullname")
                    ->join('rols', 'users.id', '=', 'rols.user_id')
                    ->join('profiles', 'users.id', '=', 'profiles.user_id')
                    ->join('assit_attendances', 'users.work_id', '=', 'assit_attendances.work_id')

                    ->whereNotIn('users.number_id', $list_ci_representant)
                    ->whereNotIn('users.number_id', $list_ci_profesor)
                    ->whereNotIn('users.number_id', $list_ci_autoridad)

                    ->where('rols.area', '<>', 'REPRESENTANTE')
                    ->where('rols.area', '<>', 'PROFESORADO')
                    ->where('rols.area', '<>', 'ESTUDIANTIL')
                    ->where('rols.rol', '<>', 'INIVITADO')

                    ->whereNotNull('users.work_id')

                    ->Where('rols.finicial', '<=', $now)
                    ->Where('rols.ffinal', '>=', $now)

                    ->Where('assit_attendances.date', '>=', $lapso->finicial)

                    ->groupBy('users.id')
                    ->orderby('profiles.card_number', 'asc')
                    ->get();                                                   //dd($workers);
                $list_ci_worker = $workers->pluck('number_id')->toArray(); //dd($list_ci_worker);

                $guests =
                User::OrderBy('users.id')
                    ->select('users.id as id', 'users.number_id as ci', 'users.email as email', 'profiles.dir_address as dir_address', 'rols.rol as rol', 'rols.area as area')
                    ->selectRaw("CONCAT(profiles.firstname,' ',profiles.lastname) as fullname")
                    ->join('rols', 'users.id', '=', 'rols.user_id')
                    ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                    ->where('rols.rol', 'INIVITADO')
                    ->where('users.is_active', 'enable')

                // ->Where('rols.finicial', '<=', $now)
                // ->Where('rols.ffinal', '>=', $now)

                    ->whereNotIn('users.number_id', $list_ci_representant)
                    ->whereNotIn('users.number_id', $list_ci_profesor)
                    ->whereNotIn('users.number_id', $list_ci_worker)
                    ->whereNotIn('users.number_id', $list_ci_autoridad)
                    ->get(); //dd($guests,$list_ci_worker);

                $attendees = $attendees->concat($representants)->concat($estudiants)->concat($profesors)->concat($workers)->concat($autoridads)->concat($guests);
                //dd($representants,$estudiants,$profesors,$workers,$autoridads,$guests);
                break;

            case 'GR3':

                $estudiants = collect();
                $estudiants =
                User::OrderBy('users.id')
                    ->select('users.id as id', 'estudiants.ci_estudiant as ci', 'estudiants.gsemail as email', 'estudiants.dir_address as dir_address', 'rols.rol as rol', 'rols.area as area')
                    ->selectRaw("CONCAT(estudiants.name,' ',estudiants.lastname) as fullname")
                    ->join('rols', 'users.id', '=', 'rols.user_id')
                    ->join('estudiants', 'users.id', '=', 'estudiants.user_id')
                    ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                    ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                    ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                    ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                    ->where('planpagos.status_inscription_affects', 'true')
                    ->where('seccions.status_active', 'true')
                    ->where('rols.area', 'ESTUDIANTIL')
                    ->where('estudiants.status_active', 'true')
                    ->groupBy('estudiants.id')
                    ->orderby('estudiants.ci_estudiant', 'asc')
                    ->get();
                $list_ci_estudiant = $estudiants->pluck('ci')->toArray();

                $attendees = $attendees->concat($estudiants);

                break;
        }
        return $attendees;
    }
}

/*
'10859570','12936044','13986116','12938907','18613563','13797958','14710401','17507532'

12936044
13986116
14710401
18613563
12938907
13797958
17507532

10859570,12936044,13986116,12938907,18613563,13797958,14710401,17507532

*/
