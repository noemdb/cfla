<?php

namespace App\Models\app\Pescolar;

use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Boletin;
use Illuminate\Database\Eloquent\Model;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;

use App\Models\app\Pescolar\Functions\Profesor\Lists;
use App\Models\app\Pescolar\Functions\Profesor\Indicators;

use App\Models\app\Incident\Incident;
use App\Models\app\Incident\IncidentAgreement;
use App\Models\app\Pescolar\Functions\Profesor\CommunityActions;
use App\Models\app\Pescolar\Functions\Profesor\Evaluacions;
use App\Models\app\Pescolar\Functions\Profesor\Statics;
use App\Models\app\Profesor\Activity;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\SocialAction\CommunityAction;
use App\Models\app\SocialAction\CommunityHour;

class Profesor extends Model
{
    use SoftDeletes;
    use Lists;
    use Statics;
    use Indicators;
    use Evaluacions;
    // use CommunityActions;

    protected $fillable = [
        'user_id',
        'ti_teacher',
        'ci_profesor',
        'lastname',
        'name',
        'gender',
        'date_birth',
        'city_birth',
        'town_hall_birth',
        'state_birth',
        'country_birth',
        'dir_address',
        'phone',
        'cellphone',
        'whatsapp',
        'email',
        'gsemail',
        'gspassword',
        'status_census_taker',
        'status_active'
    ];

    const COLUMN_COMMENTS = [
        'ti_teacher' => 'Tipo de facilitador',
        'user_id' => 'Nombre de usuario',
        'ci_profesor' => 'Cédula de identidad, Id temporal o pasaporte',
        'lastname' => 'Apellidos',
        'name' => 'Nombres',
        'gender' => 'Genero',
        'date_birth' => 'Fecha de nacimiento',
        'city_birth' => 'Lugar de nacimiento',
        'town_hall_birth' => 'Municipio de nacimiento',
        'state_birth' => 'Estado de nacimiento',
        'country_birth' => 'País de nacimiento',
        'dir_address' => 'Dirección de residencia',
        'phone' => 'Número de teléfono fijo',
        'cellphone' => 'Número de teléfono celular',
        'whatsapp' => 'N.WhatsApp',
        'email' => 'Correo electrónico personal',
        'gsemail' => 'Dirección de correo electrónico GSuite',
        'gspassword' => 'Contraseña Correo electrónico de GSuite',
        'status_census_taker' => 'Registrador de Censos Escolares',
        'status_active' => 'Activo',
    ];

    /*INI relaciones entre modelos*/
        public function pevaluacions()
        {
            return $this->hasMany('App\Models\app\Profesor\Pevaluacion');
        }
        public function profesor_gestables()
        {
            return $this->hasMany('App\Models\app\Profesor\ProfesorGestable');
        }
        public function user()
        {
            return $this->belongsTo('App\User');
        }
        public function profesor_guias()
        {
            return $this->hasMany('App\Models\app\Pescolar\ProfesorGuia');
        }
        public function incidents()
        {
            return $this->hasMany(Incident::class);
        }

        public function pensums()
        {
            return $this->belongsToMany(Pensum::class, 'pevaluacions', 'profesor_id', 'pensum_id')
                ->select('pensums.*')
                ->selectRaw("CONCAT(grados.code_sm, ' - ', asignaturas.name) as asignatura_name")
                ->join('grados', 'grados.id', '=', 'pensums.grado_id')
                ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
                ->whereNull('pevaluacions.deleted_at')
                ->groupBy('pensums.id');
        }
    /*FIN relaciones entre modelos*/

    public function getisLessonAttribute()
    {
        $pevaluacions =
            Pevaluacion::query()
            ->select('pevaluacions.*')
            ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('seccions', 'grados.id', '=', 'seccions.grado_id')

            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
            ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')

            ->where('profesors.id', $this->id)

            ->wherenull('pevaluacions.deleted_at')
            ->where('seccions.status_active', "true")
            ->where('seccions.status_inscription_affects', 'true')
            ->where('grados.status_active', "true")
            ->groupby('pevaluacions.id')
            ->get();

        return ($pevaluacions->isNotEmpty()) ? true : false;
    }

    public function getPevaluacionsForLapso($lapso_id = null)
    {
        $pevaluacions =
            Pevaluacion::query()
            ->select('pevaluacions.*', 'asignaturas.name as asignatura_name', 'grados.name as grado_name')
            ->selectRaw("CONCAT(grados.name, ' ', seccions.name, ' - ', asignaturas.name) as grado_asignatura_name")
            ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
            ->where('profesors.id', $this->id)
            ->wherenull('pevaluacions.deleted_at')
            ->where('seccions.status_active', "true")
            ->where('seccions.status_inscription_affects', 'true')
            ->where('grados.status_active', "true")
            ->groupby('pevaluacions.id');

        $pevaluacions = ($lapso_id) ? $pevaluacions->where('pevaluacions.lapso_id', $lapso_id) : $pevaluacions;

        $pevaluacions = $pevaluacions->get(); //dd($pevaluacions,$this->id,$lapso_id);

        return $pevaluacions;
    }

    public function getActivities($lapso_id = null, $seccion_id = null, $grado_id = null)
    {
        $activities =
            Activity::query()
            ->select('activities.*')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
            ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
            ->where('profesors.id', $this->id)
            ->whereNull('pevaluacions.deleted_at')
            ->where('seccions.status_active', "true")
            ->where('seccions.status_inscription_affects', 'true')
            ->where('grados.status_active', "true")
            ->groupby('activities.topic');

        $activities = ($lapso_id) ? $activities->where('pevaluacions.lapso_id', $lapso_id) : $activities;
        $activities = ($seccion_id) ? $activities->where('pevaluacions.seccion_id', $seccion_id) : $activities;
        $activities = ($grado_id) ? $activities->where('grados.id', $grado_id) : $activities;

        $activities = $activities->get(); //dd($activities,$this->name,$lapso_id);

        return $activities;
    }

    public function getActivitiesForLapso($lapso_id = null)
    {
        $activities =
            Activity::query()
            ->select('activities.*')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
            ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
            ->where('profesors.id', $this->id)
            ->wherenull('pevaluacions.deleted_at')
            ->where('seccions.status_active', "true")
            ->where('seccions.status_inscription_affects', 'true')
            ->where('grados.status_active', "true")
            ->groupby('pevaluacions.id');

        $activities = ($lapso_id) ? $activities->where('pevaluacions.lapso_id', $lapso_id) : $activities;

        $activities = $activities->get(); //dd($activities,$this->name,$lapso_id);

        return $activities;
    }

    public function getGradoForTutorLapso($lapso_id = null)
    {
        $lapso = ($lapso_id) ? Lapso::findOrFail($$lapso_id) : Lapso::current();
        $grado = Grado::select('grados.*')
            ->join('profesor_guias', 'grados.id', '=', 'profesor_guias.grado_id')
            ->join('profesors', 'profesors.id', '=', 'profesor_guias.profesor_id')
            ->join('lapsos', 'lapsos.id', '=', 'profesor_guias.lapso_id')

            ->where('profesors.id', $this->id)
            ->where('lapsos.id', $lapso->id)

            ->where('grados.status_active', 'true')

            ->groupBy('grados.id')

            ->first(); //dd($grado,$lapso,$this,$this->profesor_guias);
        return $grado;
    }

    // En app/Models/Profesor.php
        public function getAllGradosAsTutor($lapso_id = null)
        {
            $lapso = ($lapso_id) ? Lapso::findOrFail($lapso_id) : Lapso::current();
            
            return Grado::select('grados.*')
                ->join('profesor_guias', 'grados.id', '=', 'profesor_guias.grado_id')
                ->join('profesors', 'profesors.id', '=', 'profesor_guias.profesor_id')
                ->join('lapsos', 'lapsos.id', '=', 'profesor_guias.lapso_id')
                ->where('profesors.id', $this->id)
                ->where('lapsos.id', $lapso->id)
                ->where('grados.status_active', true)
                ->groupBy('grados.id')
                ->get();
        }

    public function getIsProfesorGuiaAttribute()
    {
        return ($this->profesor_guias->count() > 0) ? true : false;
    }

    public function getGuiaEstudiantsAttribute()
    {
        $estudiants = Estudiant::select('estudiants.*')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('profesor_guias', 'seccions.id', '=', 'profesor_guias.seccion_id')
            ->join('profesors', 'profesors.id', '=', 'profesor_guias.profesor_id')

            ->where('profesor_guias.profesor_id', $this->id)
            ->where('estudiants.status_active', 'true')
            ->where('seccions.status_active', 'true')

            ->wherenull('seccions.deleted_at')
            ->wherenull('profesors.deleted_at')
            ->wherenull('profesor_guias.deleted_at')
            ->wherenull('estudiants.deleted_at')
            ->wherenull('inscripcions.deleted_at')
            ->groupBy('estudiants.id')

            ->get();
        return $estudiants;
    }

    public function getDataIndicatorsAttribute()
    {
        $lapsos = Lapso::all();

        $lapso_active = Lapso::current();

        $datas = collect([]);
        $indicators = collect([]);
        foreach ($lapsos as $lapso) {
            $obj_pevaluacion = new Pevaluacion();
            unset($pevaluacions, $count_pevaluacions, $count_evaluacions);

            $pevaluacions = Pevaluacion::where('profesor_id', $this->id)->where('lapso_id', $lapso->id)->get();
            $count_pevaluacions = ($pevaluacions->IsNotEmpty()) ? $pevaluacions->count() : 0;
            $count_evaluacions = $obj_pevaluacion->count_evaluacion_prof_lapso($this->id, $lapso->id);

            $goal_notas = $this->goal_notas_load($lapso->id); //dd($goal_notas);

            $real_notas = $this->real_notas_load($lapso->id); //if($lapso->id == 1) dd($goal_notas,$real_notas);

            $porc_notas_load = ($goal_notas > 0) ? round((100 * ($real_notas / $goal_notas)), 1) : 0;

            $promedio = $this->getPromedio($lapso->id, 2);
            $porc_aprobados = $this->getPorcAprobados($lapso->id, 1);

            $indicador = collect([
                'id' => $lapso->id,
                'lapso_id' => $lapso->id,
                'name' => $lapso->name,
                'code' => $lapso->code,
                'count_pevaluacions' => $count_pevaluacions,
                'count_evaluacions' => $count_evaluacions,
                'porc_notas_load' => $porc_notas_load,
                'promedio' => $promedio,
                'porc_aplazados' => '50',
                'porc_aprobados' => $porc_aprobados,
            ]);

            $indicators->push($indicador);
        }

        $datas->put('lapsos', $lapsos);
        $datas->put('lapso_active', $lapso_active);
        $datas->put('indicators', $indicators);

        // return json_encode($datas);
        return $datas;
    }

    public function getCiFull($separete = '-')
    {
        switch ($this->country_birth) {
            case 'VENEZUELA':
                $nacionalidad = 'V';
                break;
            case '':
                $nacionalidad = 'V';
                break;
            default:
                $nacionalidad = 'E';
                break;
        }
        // return $nacionalidad.$separete.$this->ci_profesor;
        return $this->ci_profesor;
    }

    public function getFullNameAttribute()
    {
        return $this->lastname . ' ' . $this->name;
    }

    public function getSmNameAttribute()
    {
        $arr_name = explode(" ", $this->name);
        $arr_lastname = explode(" ", $this->lastname);
        $user = strtolower($arr_name[0][0] . $arr_lastname[0]);
        return $user;
    }

    public function getMdNameAttribute()
    {
        $arr_name = explode(" ", $this->name);
        $arr_lastname = explode(" ", $this->lastname);
        $user = strtolower($arr_name[0] . ' ' . $arr_lastname[0]);
        return $user;
    }

    public function scopeActive($query, $flag)
    {
        return $query->where('profesors.status_active', $flag);
    }

    public function scopeAsignado($query, $flag)
    {
        $query = $query->select('profesors.*')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->where('profesors.status_active', $flag)
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('profesors.id');

        return $query;
    }

    /*************************************************************************************************/

    public function getCreateUserAttribute()
    {
        $arr_name = explode(" ", $this->name);
        $arr_lastname = explode(" ", $this->lastname);
        $str_ci = substr($this->ci_profesor, -2, 2);

        $username = strtolower($arr_name[0][0] . $arr_lastname[0] . $str_ci);
        $email = (!empty($this->email)) ? strtolower($this->email) : $username . '@saefl.test';
        $this_id = $this->id;
        $password = bcrypt($username);

        $user = $this->user;

        if (empty($user)) {
            $id = DB::table('users')->insertGetId([
                'username' => $username,
                'password' => $password,
                'email' => $email,
                'is_active' => 'enable',
                'created_at' => Carbon::now(),
                'remember_token' => Str::random(60),
            ]);
            DB::table('profiles')->insert([
                'firstname' => $this->name,
                'lastname' => $this->lastname,
                'url_img' => "images/avatar/user_default.png",
                'user_id' => $id,
                'created_at' => Carbon::now(),
            ]);
            DB::table('rols')->insert([
                'area' => "PROFESORADO",
                'rol' => "PROFESOR",
                'descripcion' => "Profesor de la institución",
                'finicial' => Carbon::now()->year . "0901",
                'ffinal' => (Carbon::now()->year + 1) . "0831",
                'user_id' => $id,
                'created_at' => Carbon::now(),
            ]);
            $update = Profesor::where('id', $this->id)->update(['user_id' => $id]);
            return true;
        }
    }



    public function getPensums()
    {
        return Pensum::select('pensums.*')
            ->selectRaw("CONCAT(grados.code_sm, ' - ', asignaturas.name) as asignatura_name")
            ->join('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->where('pevaluacions.profesor_id', $this->id)
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('pensums.id')
            ->get();
    }

    public function getPensumsName()
    {
        $pensums = Pensum::select('pensums.*')
            ->selectRaw("CONCAT(grados.name, ' - ', asignaturas.name) as asignatura_name")
            ->join('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->where('pevaluacions.profesor_id', $this->id)
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('pensums.id')
            ->get();

        return $pensums;
    }

    public function getPeducativosAttribute()
    {
        return Peducativo::select('peducativos.*')
            ->selectRaw('count(pevaluacions.id) as count')
            ->join('pestudios', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->join('pensums', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->join('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->where('pevaluacions.profesor_id', $this->id)
            ->wherenull('pevaluacions.deleted_at')
            ->GroupBy('peducativos.id')
            ->orderBy('count', 'desc')
            ->get();
    }

    public function getPestudiosAttribute()
    {
        return Pestudio::select('pestudios.*')
            ->selectRaw('count(pevaluacions.id) as count')
            ->join('pensums', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->join('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->where('pevaluacions.profesor_id', $this->id)
            ->wherenull('pevaluacions.deleted_at')
            ->GroupBy('pestudios.id')
            ->orderBy('count', 'desc')
            ->get();
    }

    public function getPestudioAttribute()
    {
        $pestudio = Pestudio::select('pestudios.*')
            ->selectRaw('count(pevaluacions.id) as count')
            ->join('pensums', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->join('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->where('pevaluacions.profesor_id', $this->id)
            ->wherenull('pevaluacions.deleted_at')
            ->GroupBy('pestudios.id')
            ->orderBy('count', 'desc')
            ->first(); //dd($pestudio);
        return $pestudio;
    }

    public function getCargaAcademicaAttribute()
    {
        $pensums = Pensum::select('pensums.*')
            ->join('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->where('pevaluacions.profesor_id', $this->id)
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('pensums.asignatura_id')
            ->get();
        return $pensums;
    }

    public function getSeccionsAttribute()
    {
        $seccions = Seccion::select('seccions.*')
            ->join('pevaluacions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->wherenull('pevaluacions.deleted_at')
            ->where('pevaluacions.profesor_id', $this->id)
            ->groupby('seccions.id')
            ->get();

        return $seccions;
    }

    public function getGradoGuiaAttribute()
    {
        return Grado::select('grados.*', 'lapsos.name as lapso_name')
            ->join('profesor_guias', 'grados.id', '=', 'profesor_guias.grado_id')
            ->join('lapsos', 'lapsos.id', '=', 'profesor_guias.lapso_id')
            ->where('profesor_guias.profesor_id', $this->id)
            ->orderBy('profesor_guias.created_at', 'desc')
            ->first();
    }

    public function getSeccionGuiasAttribute()
    {
        $seccions = Seccion::select('seccions.*', 'lapsos.name as lapso_name')
            ->join('profesor_guias', 'seccions.id', '=', 'profesor_guias.seccion_id')
            ->join('lapsos', 'lapsos.id', '=', 'profesor_guias.lapso_id')
            ->where('profesor_guias.profesor_id', $this->id)
            ->groupBy('seccions.id')
            ->get();

        return $seccions;
    }

    public function getStatusDeleteAttribute()
    {
        return (empty($this->pevaluacions->count())) ? true : false;
    }

    public function getEstudiantsAttribute()
    {
        $estudiants = Estudiant::select(
            'estudiants.*',
            DB::raw('count(boletins.id) as value')
        )
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('estudiants', 'estudiants.id', '=', 'boletins.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('pevaluacions.profesor_id', $this->id)
            // ->where('pevaluacions.lapso_id',$lapso_id)
            ->where('estudiants.status_active', 'true')
            // ->whereDate('inscripcions.created_at', '>=', $lapso->finicial)
            // ->whereDate('inscripcions.created_at', '<=', $lapso->ffinal)
            ->wherenotnull('boletins.nota')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('estudiants.deleted_at')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('pevaluacions.profesor_id');
        // ->first();

        if (!empty($lapso_id)) {
            $lapso = Lapso::findOrFail($lapso_id);
            $estudiants = $lapso->where('pevaluacions.lapso_id', $lapso_id)
                ->whereDate('inscripcions.created_at', '>=', $lapso->finicial)
                ->whereDate('inscripcions.created_at', '<=', $lapso->ffinal);
        }

        $estudiants = $estudiants->first();

        return ($estudiants) ? $estudiants->value : 0;
    }

    public function getIncidentAgreementsAttribute()
    {
        $incident_agreements = IncidentAgreement::select('incident_agreements.*')
            ->join('incidents', 'incidents.id', '=', 'incident_agreements.incident_id')
            ->join('profesors', 'profesors.id', '=', 'incidents.profesor_id')
            ->where('profesors.id', $this->id)
            ->orderBy('incident_agreements.created_at', 'desc')
            ->get();
        return $incident_agreements;
    }

    public function getIncidentsAnnouncementsAttribute()
    {
        return $this->incidents->where('status_announcement', true);
    }

    public function getIncidentsRange($finicial = null, $ffinal = null)
    {
        $incidents = Incident::select('incidents.*')
            ->join('profesors', 'profesors.id', '=', 'incidents.profesor_id')
            ->where('profesors.id', $this->id)
            ->orderBy('incidents.created_at', 'desc');

        $incidents = ($finicial) ? $incidents->whereDate('incidents.created_at', '>=', $finicial)  : $incidents;
        $incidents = ($ffinal) ? $incidents->whereDate('incidents.created_at', '<=', $ffinal)  : $incidents;

        $incidents = $incidents->get(); //dd($finicial,$ffinal,$incidents);
        return $incidents;
    }

    public function getIncidentsDay($date)
    {
        $incidents = Incident::where('profesor_id', $this->id)->whereDate('created_at', $date)->get();
        return $incidents;
    }

    public function getHoursCompletedAttribute()
    {
        $grado = $this->grado_guia;
        return CommunityHour::query()
            ->select('community_hours.*')
            ->join('community_actions', 'community_actions.id', '=', 'community_hours.community_action_id')
            ->join('grados', 'grados.id', '=', 'community_actions.grado_id')
            ->where('grados.id', $grado->id)
            ->sum('community_hours.duration');
    }

    public function getCommunityActionsAttribute()
    {
        return CommunityAction::query()
            ->select('community_actions.*')
            // ->join('community_hours', 'community_actions.id', '=', 'community_hours.community_action_id')
            ->where('community_actions.user_id', $this->user_id)
            ->groupBy('community_actions.id')
            ->get();
    }

    public function getSocialGradosAttribute()
    {
        $grado = $this->grado_guia;
        return Grado::query()
            ->select('grados.*')
            ->join('community_actions', 'grados.id', '=', 'community_actions.grado_id')
            ->join('community_hours', 'community_actions.id', '=', 'community_hours.community_action_id')
            ->where('community_actions.user_id', $this->user_id)
            ->where('grados.status_active', "true")
            ->groupBy('grados.id')
            ->get();
    }

    public function getCiFullF2(string $separator = ' '): string
    {
        $nationality = $this->country_birth === 'VENEZUELA' ? 'V' : 'E';
        return sprintf('%s%s%s', $nationality, $separator, $this->formatted_ci_custom);
    }

    public function getFormattedCiCustomAttribute(): string
    {
        $cleanCi = ltrim(str_replace('.', '', $this->ci_profesor), '0');
        $length = strlen($cleanCi);

        $parts = match(true) {
            $length <= 7 => [
                substr(str_pad($cleanCi, 7, '0', STR_PAD_LEFT), 0, 1),
                substr(str_pad($cleanCi, 7, '0', STR_PAD_LEFT), 1, 3),
                substr(str_pad($cleanCi, 7, '0', STR_PAD_LEFT), 4, 3)
            ],
            default => [
                substr(str_pad($cleanCi, 8, '0', STR_PAD_LEFT), 0, 2),
                substr(str_pad($cleanCi, 8, '0', STR_PAD_LEFT), 2, 3),
                substr(str_pad($cleanCi, 8, '0', STR_PAD_LEFT), 5, 3)
            ]
        };

        return implode('.', $parts);
    }
    
}
