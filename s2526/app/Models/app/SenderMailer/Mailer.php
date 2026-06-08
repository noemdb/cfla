<?php

namespace App\Models\app\SenderMailer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\app\Estudiante\Representant;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Estudiante\Enrollment;
use App\Models\app\Institucion\Autoridad;
use App\User;

class Mailer extends Model
{
    protected $fillable = [
        'user_id','name','code','description','seccion_id','grado_id','pestudio_id','ci_list','date','fecha','time',
        'subject','title','subtitle','greeting','body','insert','footer','atte',
        'status','status_adviders','autoridad_id','status_test','status_email','status_whatsapp','status_exclude_last','status_quota','template','general'
    ];

    protected $dates = ['date','created_at','updated_at'];

    protected $dateFormat = 'Y-m-d H:i:s';

    const LIST_TEMPLATE = [
        'general'=>'Mensaje General',
        'notice_collection'=>'Notificación de Cobro',
        'notication_academic'=>'Notificación de Académica',
        'notification_agree'=>'Notificación de Acuerdo',
        'coexistence_notifications'=>'Notificación de Convivencia',
    ];

    const COLUMN_COMMENTS = [
        'user_id' => 'Usuario',
        'name' => 'Nombre',
        'code' => 'Código',
        'description' => 'Descripción',

        'seccion_id' => 'Sección',
        'grado_id' => 'Grado',
        'pestudio_id' => 'Plan de Estudio',
        'ci_list' => 'Lista de CI.',
        'autoridad_id' => 'Autoridad asociada al área del asunto.',

        'date'=>'Fecha Envío',
        'fecha'=>'Fecha Envío',
        'time' => 'Hora Envío',

        'subject' => 'Asunto',
        'title' => 'Título',
        'subtitle' => 'Subtítulo',
        'greeting' => 'Saludo formal',
        'body' => 'Cuerpo',
        'insert' => 'Insertado',
        'footer' => 'Despedida',
        'atte' => 'Atte',

        'status' => 'Estado',
        'status_test' => 'Consulta de prueba',
        'status_adviders' => 'Representantes delegados',
        'grado_seccion'=>'Grado/Sección',
        'finicial_finicial'=>'F.Inicial/F.Final',
        'status_sender'=>'% de envío',
        'status_ready'=>'Cola creada',
        'status_exclude_last'=>'Excluye el último curso',
        'status_quota'=>'Filtrado por cuotas',
        'status_email'=>'Envio por email',
        'status_whatsapp'=>'Envio por WhatsApp',
        'template'=>'Plantilla de WhatsApp',
        'general'=>'Mensaje de WhatsApp',
    ];

    public function autoridad() { return $this->belongsTo(Autoridad::class); }
    public function maileds() { return $this->hasMany('App\Models\app\SenderMailer\Mailed'); }
    public function pestudio() { return $this->belongsTo('App\Models\app\Pescolar\Pestudio'); }
    public function grado() { return $this->belongsTo('App\Models\app\Pescolar\Grado'); }
    public function seccion() { return $this->belongsTo('App\Models\app\Pescolar\Seccion'); }
    public function user() { return $this->belongsTo('App\User'); }

    public function getUserNameAttribute()
    {
        return $this->user->username;
    }

    public function getStatusSenderAttribute()
    {
        $representants = $this->representants;
        $goal = $representants->count();
        $maileds_send = $this->maileds_send;
        $real = $maileds_send->count();
        $indicator = ($goal>0) ? 100 * $real / $goal: null ;
        return $maileds_send.' %';
    }

    public function getMailedsSendAttribute()
    {
        $user = User::find(Auth::id()) ;
        $maileds = DB::table('maileds')
        ->select('maileds.*','jobs.queue')
        ->join('mailers', 'mailers.id', '=', 'maileds.mailer_id')
        ->join('representants', 'representants.id', '=', 'maileds.representant_id')
        ->leftjoin('jobs', 'jobs.available_at', '=', 'maileds.available_at')
        ->where('mailers.id',$this->id)
        ->whereNull('jobs.id')
        ->GroupBy('representants.id');

        $maileds = (! $user->IsAdmin() ) ? $maileds->where('mailers.user_id',$user->id) : $maileds;

        $maileds = $maileds->get();

        return $maileds;
    }

    public function getStatusReadyAttribute()
    {
        $maileds = $this->maileds;
        return ($maileds->count()) ? true :false ;
    }

    public function getRepresentantsAttribute()
    {
        $representants = Representant::select('representants.*')
        ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id');

        if (empty($this->ci_list)) {
            //dd($this->ci_list);
            $representants = $representants->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
            ->where('representants.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->where('pestudios.status_active', 'true')
            ->where('planpagos.status_inscription_affects', 'true')
            ->whereNull('seccions.deleted_at')
            ->whereNull('pestudios.deleted_at')
            ->whereNull('planpagos.deleted_at');

            $representants = (isset($this->grado_id)) ? $representants->where('grados.id',$this->grado_id) : $representants ;
            $representants = (isset($this->seccion_id)) ? $representants->where('seccions.id',$this->seccion_id) : $representants ;
            $representants = (isset($this->pestudio_id)) ? $representants->where('pestudios.id',$this->pestudio_id) : $representants ;

            $representants = ($this->status_exclude_last =="true") ? $representants->where('grados.id','<>',11) : $representants ;
            
        } else {
            $arr_ci = explode(',',$this->ci_list);
            $representants = $representants->whereIn('representants.ci_representant',$arr_ci);
        }  
        
        $representants = ($this->status_adviders == 'true') ? $representants->where('representants.status_adviders','true') : $representants ;   

        $representants = $representants
        ->where('estudiants.status_active', 'true')
        ->whereNull('estudiants.deleted_at')
        ->whereNull('representants.deleted_at')        
        ->groupBy('representants.id');        

        $representants = $representants->get(); //dd($representants);

        return $representants;
    }

    public function getRepresentantEnrollmentsAttribute()
    {
        return Enrollment::select('enrollments.*')->groupBy('enrollments.ci_representant')->get();
    }

    public function getRepresentantAttribute()
    {
        return ($this->representants->count()) ? $this->representants->random() : null;
    }

    public function getPestudioGradoSeccionAttribute()
    {
        $text = null;
        $pestudio = $this->pestudio;
        $text = ($pestudio) ? $pestudio->name : $text ;

        $grado = $this->grado;
        $text = ($grado) ? $grado->name : $text ;

        $seccion = $this->seccion;
        $text = ($seccion) ? $text . ' ' . $seccion->name : $text ;
        return ($text) ? $text : 'TODOS' ;
    }

    public function getDateAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s',$this->fecha. ' ' . $this->time);
    }

}

/*

18683168,17254078,18052771,19063772,26224085,17992009,12937602,20466205,22316927,18683168,13095090,18054915,16483512,16483512,14442948,17611707,20467289,16949468,16111960,17699708,26224085,16483512,16822849,25718158,25177359,16690862,14607859,17401952


18683168,
17254078,
18052771,
19063772,
26224085,
17992009,
12937602,
20466205,
22316927,
18683168,
13095090,
18054915,
16483512,
16483512,
14442948,
17611707,
20467289,
16949468,
16111960,
17699708,
26224085,
16483512,
16822849,
25718158,
25177359,
16690862,
14607859,
17401952


18683168
17254078
18052771
19063772
26224085
17992009
12937602
20466205
22316927
18683168
13095090
18054915
16483512
16483512
14442948
17611707
20467289
16949468
16111960
17699708
26224085
16483512
16822849
25718158
25177359
16690862
14607859
17401952



*/


