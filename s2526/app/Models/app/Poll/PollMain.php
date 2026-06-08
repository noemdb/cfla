<?php

namespace App\Models\app\Poll;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;
use Carbon\Carbon;

use App\User;
use App\Models\app\Institucion\Autoridad;

use App\Models\app\Estudiante\Representant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Poll\Functions\PollGroupTrait;
use Illuminate\Support\Facades\DB;

class PollMain extends Model
{
    use HasFactory;
    use PollGroupTrait;

    protected $fillable = [
        'user_id','autoridad_id','poll_group_id','name','description','observations','image',"date_start","time_start","date_end","time_end",'ci_list',
        'status_notifiled','status_test','status_estudiant','status_exclude_last','status_representant'
    ];
    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'user_id' => 'Usuario',
        'autoridad_id' => 'Autoridad asociada al área del asunto.',
        'poll_group_id' => 'Grupo',
        'name' => 'Nombre',
        'description' => 'Descripción',
        'observations' => 'Observaciones',
        'image' => 'Imagen',
        'start_end' => 'Inicio/Fin',
        'date_start' => 'F.Inicio',
        'time_start' => 'H. de Inicio',
        'date_end' => 'F.Finalización',
        'time_end' => 'H. de Fin',
        'ci_list' => 'Lista de CI.',
        'tokens' => 'Tokens',
        'status_notifiled' => 'Notificaciones enviadas',
        'status_sender' => 'Notificación enviada',
        'participations' => 'Participación',
        'status_test' => 'Consulta de prueba',
        'status_estudiant' => 'Incluir a estudiantes',
        'status_exclude_last' => 'Excluir último curso',
        'status_representant' => 'Excluye a representante',
    ];

    public function getImageUrlAttribute()
    {
        return ($this->image) ? asset('storage/polls/'.$this->image) : null;
    }

    /*INI Relationsheep-------------------------------------------------------------------------------------------------------------------------*/
        public function user() { return $this->belongsTo(User::class); }
        public function autoridad() { return $this->belongsTo(Autoridad::class); }
        public function poll_group() { return $this->belongsTo(PollGroup::class); }

        public function poll_questions() { return $this->hasMany(PollQuestion::class); }
        public function poll_tokens() { return $this->hasMany(PollToken::class); }
    /*END Relationsheep-------------------------------------------------------------------------------------------------------------------------*/

    /*INI properties extesion-------------------------------------------------------------------------------------------------------------------------*/
        public function getStartAttribute() {
            return Carbon::createFromFormat('Y-m-d H:i:s',$this->date_start.' '.$this->time_start);
        }

        public function getEndAttribute()
        {
            return Carbon::createFromFormat('Y-m-d H:i:s',$this->date_end.' '.$this->time_end);
        }

        public function getUsernameAttribute()
        {
            $user = $this->user;
            return ($user) ? $user->username : null;
        }

        public function getStatusActiveAttribute()
        {
            $now = Carbon::now()->timestamp;
            $start = $this->start->timestamp;
            $end = $this->end->timestamp;
            return ($now >= $start && $now <= $end) ? true : false;
        }

        public function getGroupNameAttribute()
        {
            $poll_group = $this->poll_group;
            return ($poll_group) ? $poll_group->name : null;
        }
    /*INI properties extesion-------------------------------------------------------------------------------------------------------------------------*/

    public function getPollAnswersUserId($user_id)
    {
        $poll_answers =
            PollAnswer::select('poll_answers.*')
                ->join('poll_tokens', 'poll_tokens.token', '=', 'poll_answers.token')
                ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
                ->where('poll_tokens.user_id',$user_id)
                ->where('poll_mains.id',$this->id)
                ->get();
        return $poll_answers;
    }

    public function getTokenUserId($user_id)
    {
        $poll_token =
            PollToken::select('poll_tokens.*')
                ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
                ->where('poll_mains.id',$this->id)
                ->where('poll_tokens.user_id',$user_id)
                ->first();
        return $poll_token;
    }

    public function getTokenAttendeeUserId($user_id)
    {
        $poll_token =
            PollToken::select('poll_tokens.*')
                ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
                ->where('poll_mains.id',$this->id)
                ->where('poll_tokens.user_id',$user_id)
                ->first();
        return $poll_token;
    }

    public function getCompetitorsAttribute()
    {
        $poll_tokens =
            PollToken::select('poll_tokens.*')
                ->join('poll_answers', 'poll_tokens.token', '=', 'poll_answers.token')
                ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
                ->where('poll_mains.id',$this->id)
                ->groupBy('poll_tokens.token')
                ->get(); //dd($poll_answers);
        return $poll_tokens;
    }

    public function getCompetitorsForArea($area)
    {
        $poll_tokens =
            PollToken::select('poll_tokens.*')
                ->join('poll_answers', 'poll_tokens.token', '=', 'poll_answers.token')
                ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
                ->join('users', 'users.id', '=', 'poll_tokens.user_id')
                ->join('rols', 'users.id', '=', 'rols.user_id')
                ->where('poll_mains.id',$this->id)
                ->where('rols.area',$area)
                ->groupBy('poll_tokens.token')
                ->get(); //dd($poll_answers);
        return $poll_tokens;
    }

    public function getPollAnswerByToken($token)
    {
        $poll_answers =
            PollAnswer::select('poll_answers.*')
                ->join('poll_tokens', 'poll_tokens.token', '=', 'poll_answers.token')
                ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
                ->where('poll_mains.id',$this->id)
                ->where('poll_tokens.token',$token)
                ->get();
        return $poll_answers;
    }

    public function getPollAnswerByTokensAttribute()
    {
        $poll_answers =
            PollAnswer::select('poll_answers.*')
                ->join('poll_tokens', 'poll_tokens.token', '=', 'poll_answers.token')
                ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
                ->where('poll_mains.id',$this->id)
                ->groupBy('poll_tokens.token')
                ->get();
        return $poll_answers;
    }

    public function StatusActiveAttendee($token)
    {
        $poll_questions = $this->poll_questions; //dd($poll_questions);
        $data = collect();
        foreach ($poll_questions as $poll_question) {
            $poll_answer =
            PollAnswer::select('poll_answers.*')
            ->join('poll_questions', 'poll_questions.id', '=', 'poll_answers.poll_question_id')
            ->where('poll_answers.token',$token)
            ->where('poll_questions.id',$poll_question->id)
            ->first();
            if (! $poll_answer) {
                return true;
            }
        }
    }

    public function getRepresentantsAttribute()
    {
        $representants = collect();
        switch ($this->poll_group_id) {
            case '1':
                $representants = Representant::select('representants.*')
                    ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
                    ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                    ->where('inscripcions.seccion_id','<>',21)
                    ->where('inscripcions.seccion_id','<>',22)
                    ->where('representants.status_active','true')
                    ->where('estudiants.status_active','true')
                    // ->where('representants.id',131)
                    ->groupBy('representants.id')
                    ->get();
                break;

            default:
                # code...
                break;
        }

        return $representants;
    }

    public function getGenerateTokenAttribute()
    {
        $tokens = collect();
        $attendees = $this->attendees; //dd($attendees);

        $arr_id = $attendees->pluck('id')->toArray();

        $poll_tokens = PollToken::where('poll_main_id',$this->id)->whereNotIn('user_id',$arr_id)->get(); //dd($poll_tokens->pluck('user_id')->toArray());

        foreach ($poll_tokens as $token) {
            $arr_del[] = $token;
            $token->delete();
        }

        foreach ($attendees as $attendee) {
            $poll_token = PollToken::where('poll_main_id',$this->id)->where('user_id',$attendee->id)->first();
            $email = $attendee->email;
            if (empty($poll_token)) {
                $email = (empty($email)) ? Carbon::now()->timestamp.'@saefl.test' : $email;
                $user_id = $attendee->id;
                $token = substr(str_replace(['+', '/', '=', '&'], '', password_hash(bin2hex(random_bytes(45)), PASSWORD_BCRYPT)), 0, 32);
                $arr = [
                    'poll_main_id'=>$this->id,
                    'user_id'=>$user_id,
                    'token'=>$token,
                    'email'=>$email,
                ];

                //se registran los token si y solo si $this->status_representant=='true'
                if ($this->status_representant=='true') {
                    $poll_token = PollToken::create($arr);
                } 
                
                $tokens->push($arr);
                if ($attendee->isRepresentant()) {
                    if ($this->status_estudiant=='true') {
                        $representant = $attendee->representant;
                        $estudiants = $representant->estudiants_formaly;
                        foreach ($estudiants as $estudiant) {
                            $user_id = $estudiant->user_id;
                            if (empty($user_id)) $user_id = $estudiant->setCreateUserGetId('ESTUDIANTIL','ESTUDIANTE');
                            // $token = substr(str_replace(['+', '/', '=', '&'], '', bcrypt(random_bytes(45))), 0, 32); // 32 characters, without /=+
                            $token = substr(str_replace(['+', '/', '=', '&'], '', password_hash(bin2hex(random_bytes(45)), PASSWORD_BCRYPT)), 0, 32);
                            $email = ($estudiant->gsemail) ? $estudiant->gsemail : $token.'@saefl.test' ;
                            $arr = [
                                'poll_main_id'=>$this->id,
                                'user_id'=>$user_id,
                                'token'=>$token,
                                'email'=>$email,
                            ];
                            $poll_token = PollToken::create($arr);
                            $tokens->push($arr);
                        }
                    }
                }
            }
        }
        return $tokens;
    }

    public function getGradosAttribute()
    {
        $grados = collect();
        switch ($this->poll_group_id) {
            case '1':
                $grados = Grado::select('grados.*')
                    ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
                    ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
                    ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                    ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
                    ->join('poll_tokens', 'representants.id', '=', 'poll_tokens.representant_id')
                    ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
                    //->join('poll_answers', 'poll_tokens.token', '=', 'poll_answers.token')
                    ->where('seccions.id','<>',21)
                    ->where('seccions.id','<>',22)
                    ->where('poll_mains.id',$this->id)
                    ->groupBy('grados.id')
                    ->get();
                break;

            default:
                # code...
                break;
        }

        return $grados;
    }

    public function getPestudiosAttribute()
    {
        $attendees = $this->attendees->pluck('id')->toArray(); //dd($attendees);
        $pestudios = collect(New Pestudio);

        $pestudios = Pestudio::select('pestudios.*')
        ->join('grados', 'pestudios.id', '=', 'grados.pestudio_id')
        ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
        ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
        ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
        ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
        ->join('users', 'users.id', '=', 'representants.user_id')
        ->whereIn('users.id',$attendees)
        ->groupBy('pestudios.id')
        ->get();

        return $pestudios;
    }

    public function getParticipationsAttribute()
    {
        $count_tokens = ($this->poll_tokens->isNotEmpty()) ? $this->poll_tokens->count() : null;
        $count_answer = ($this->poll_answer_by_tokens->isNotEmpty()) ? $this->poll_answer_by_tokens->count() : null;
        $participations = ($count_tokens) ? 100 * $count_answer / $count_tokens : null;
        $participations = round($participations,2);
        return $participations;
    }

    public static function list_poll_main() /* usada para llenar los objetos de formularios select*/
    {
        return PollMain::pluck('name','id');
    }

    public static function list_poll_main_user($user_id) /* usada para llenar los objetos de formularios select*/
    {
        $user = User::find($user_id);
        if ($user) {
            $is_admin = $user->IsAdmin();
            $poll_mains = PollMain::select('poll_mains.*');
            $poll_mains = ($is_admin) ? $poll_mains : $poll_mains->where('user_id',$user->id) ;
            return $poll_mains->pluck('name','id');
        }
    }

    public static function list_poll_main_autority($autoridad_id) /* usada para llenar los objetos de formularios select*/
    {
        $poll_mains = PollMain::select('poll_mains.*')->where('autoridad_id',$autoridad_id);
        return $poll_mains->pluck('name','id');
    }

    public static function list_poll_main_enable() /* usada para llenar los objetos de formularios select*/
    {
        $now = Carbon::now()->format('Y-m-d'); //dd($now,PollMain::all());
        $poll_main = PollMain::orderBy('date_start')
        ->where('date_start','<=',$now)
        ->pluck('name','id');
        return $poll_main;
    }

    public static function list_poll_main_enable_user($user_id) /* usada para llenar los objetos de formularios select*/
    {
        $user = User::find($user_id);
        if ($user) {
            $now = Carbon::now()->format('Y-m-d');
            $poll_mains = PollMain::select('poll_mains.*')->whereDate('date_start','>',$now);

            $is_admin = $user->IsAdmin();
            $poll_mains = ($is_admin) ? $poll_mains : $poll_mains->where('user_id',$user->id) ;
            return $poll_mains->pluck('name','id');
        }
    }

    public static function polls_active_user($user_id = null) /* usada para llenar los objetos de formularios select*/
    {
        $now = Carbon::now()->format('Y-m-d');
        $poll_mains = PollMain::select('poll_mains.*')
            ->where('date_start','<=',$now)
            ->where('date_end','>=',$now)
            ->get();
        return $poll_mains;
    }

    public static function getPollMainsActiveByUserId($user_id)
    {
        $now = Carbon::now()->format('Y-m-d');
        $poll_mains =
        PollMain::select('poll_mains.*')
        ->join('poll_tokens', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
        ->where('poll_tokens.user_id',$user_id)
        ->where('date_start','<=',$now)
        ->where('date_end','>=',$now)
        ->get();
        return $poll_mains;
    }

    public function getStatusDeleteAttribute()
    {
        return ($this->poll_tokens->empty()) ? false : true;
    }

    public function getStatusOffLineAttribute()
    {
        $now = Carbon::now();
        return ($this->end > $now) ? true : false;
    }

}

