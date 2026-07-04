<?php

namespace App\Models\app\Incident;

use App\User;
use Jenssegers\Date\Date;
use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Profesor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'estudiant_id',
        'profesor_id',
        'duty_id',
        'fault_id',
        'description',
        'observations',
        'status_notify_close',
        'close_observations',
        'status_pedagogical',
        'status_close',
        'taken_actions',
        'status_reiterative',
        'status_notify',
        'date_notify_email',
        'status_notify_agreement',
        'date_notify_agreement_email',
        'status_announcement',
        'date_announcement',
        'hour_announcement',
        'status_active',
        'date_close',
    ];

    protected $dates = [
        'date_notify_email',
        'date_notify_agreement_email',
        'date_close',
        // 'date_announcement',
    ];

    protected $casts = [
        'status_notify_close' => 'boolean',
        'status_pedagogical' => 'boolean',
        'status_close' => 'boolean',
        'status_reiterative' => 'boolean',
        'status_notify' => 'boolean',
        'status_notify_agreement' => 'boolean',
        'status_announcement' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function estudiant()
    {
        return $this->belongsTo(Estudiant::class, 'estudiant_id');
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }

    public function incident_duty()
    {
        return $this->belongsTo(IncidentDuty::class, 'duty_id');
    }

    public function incident_fault()
    {
        return $this->belongsTo(IncidentFault::class, 'fault_id');
    }

    public function incident_agreements()
    {
        return $this->hasMany(IncidentAgreement::class);
    }

    public function incident_actions()
    {
        return $this->hasMany(IncidentAction::class, 'incident_id');
    }


    public function incident_correctives()
    {

        $incident_correctives = IncidentCorrective::select('incident_correctives.*')
            ->join('incident_faults', 'incident_faults.id', '=', 'incident_correctives.fault_id')
            ->join('incidents', 'incident_faults.id', '=', 'incidents.fault_id')
            ->where('incidents.id', $this->id)
            ->get(); //dd($incident_correctives);
        return $incident_correctives;
    }


    const COLUMN_COMMENTS = [
        'user_id' => 'Identificador del usuario que registró el incidente',
        'estudiant_id' => 'Identificador del estudiante involucrado en el incidente',
        'profesor_id' => 'Identificador del profesor involucrado en el incidente',
        'duty_id' => 'Deber que se incumplió',
        'fault_id' => 'Falta incurrida',
        'description' => 'Descripción del incidente',
        'observations' => 'Observaciones sobre el incidente',
        'status_notify_close' => 'Indica si se notificó el cierre del incidente',
        'close_observations' => 'Observaciones sobre el cierre del incidente',
        'status_pedagogical' => 'Indica si se realizó un correctivo pedagógico',
        'status_close' => 'Indica si el incidente está cerrado',
        'taken_actions' => 'Acciones tomadas para resolver el incidente',
        'status_reiterative' => 'Indica si el incidente es reiterativo',
        'status_notify' => 'Indica si se notificó el incidente',
        'date_notify_email' => 'Fecha de notificación del incidente',
        'status_notify_agreement' => 'Indica si se notificó el acuerdo',
        'date_notify_agreement_email' => 'Fecha de notificación del acuerdo',
        'status_announcement' => 'Indica si se convocó a una reunión',
        'date_announcement' => 'Fecha de la reunión',
        'hour_announcement' => 'Hora de la reunión',
        'status_active' => 'Indica si el incidente está activo',
        'date_close' => 'Fecha de cierre del incidente',
        'incident_id' => 'Incidencia',
        'corrective_id' => 'Correctivo pedagógico',
        'status_selected' => 'Seleccionado',
        'finicial' => 'Desde',
        'ffinal' => 'Hasta',
        'status_aggression' => '¿Hubo agresión?',
        'status_close_filter' => 'Incidencias cerradas',
    ];

    public function getCorrectivesAttribute()
    {
        $correctives = IncidentCorrective::select('incident_correctives.*')
            ->join('incident_faults', 'incident_faults.id', '=', 'incident_correctives.fault_id')
            ->where('incident_faults.id', $this->fault_id)
            ->get();
        return $correctives;
    }

    public static function list_status_close()
    {
        return [true => 'SI', false => 'NO'];
    }

    public function getTimeAnnouncementAttribute()
    {
        if ($this->date_announcement) {
            $date = Date::parse($this->date_announcement)->format('y-m-d');
            $parse = $date . ' ' . $this->hour_announcement;
            $time = Date::parse($parse);
            return $time;
        }
    }

    public function getCodeAttribute()
    {
        return 'BE-I' . $this->id;
    }

    public function getDutyAttribute()
    {
        return ($this->incident_duty) ? $this->incident_duty->name : null;
    }

    public function getFaultAttribute()
    {
        return ($this->incident_fault) ? $this->incident_fault->description : null;
    }

    public static function incidents_notificated() {}

    public function getProfesorGuiaAttribute()
    {
        $profesors = Profesor::select('profesors.*')
            ->join('profesor_guias', 'profesors.id', '=', 'profesor_guias.profesor_id')
            ->join('seccions', 'seccions.id', '=', 'profesor_guias.seccion_id')
            ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->where('inscripcions.estudiant_id', $this->estudiant_id)
            ->first();
        return $profesors;
    }

    public function list_correctives()
    {
        $list_correctives = DB::table('incident_correctives')
            ->select('incident_correctives.*')
            ->where('incident_correctives.fault_id', $this->fault_id);

        $list_correctives = $list_correctives->get(); //dd($list_correctives);
        return $list_correctives;
    }
}
