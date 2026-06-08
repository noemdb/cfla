<?php

namespace App\Http\Livewire\Profesor\Census;

use Livewire\Component;
use Livewire\WithPagination;

use App\Models\app\Estudiante\Census;
use App\Models\app\Estudiant;
use App\Models\app\HistoricoNota\Oinstitucion;
use App\Models\app\Estudiante\Administrativa;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Estudiante\Enrollment;


// use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Http\Livewire\Profesor\Census\CensusTrait;
//app/Http/Livewire/Profesor/Census/EnrollmentTrait.php

use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use Carbon\Carbon;

class IndexComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    use CensusTrait;

    protected $listeners = ['showSwal','alertConfirm','alertQuestion','remove'];

    public Census $census;
    public $lapso,$status_active_lapso_censu,$date_start_census,$date_end_census,$time_start_census,$time_end_census;
    public $list_comment,$list_grado,$list_country_birth,$list_relationship,$list_oinstitucions,$list_admited;
    public $enrollment_id;

    public function mount()
    {
        $this->census = new Census;
        $this->list_comment = Census::COLUMN_COMMENTS;
        $this->list_grado = Grado::list_pestudio_grado();
        $this->list_country_birth = Estudiant::list_country_birth();
        $this->list_relationship = ['Madre'=>'Madre','Padre'=>'Padre','Hermano(a)'=>'Hermano(a)','Abuelo(a)'=>'Abuelo(a)','Otro'=>'Otro'];
        $this->list_admited = ['true'=>'SI','false'=>'NO'];
        $this->list_oinstitucions = Oinstitucion::list_oinstitucions();

        // $lapso = Lapso::current();
        $lapso = Lapso::getLapsoCensusActive(); //dd($lapso);

        if ($lapso) {
            $this->lapso = $lapso;
            $this->date_start_census = ($lapso->date_start_census) ? Carbon::parse($lapso->date_start_census) : null ;
            $this->time_start_census = $lapso->time_start_census;

            $this->date_end_census = ($lapso->date_end_census) ? Carbon::parse($lapso->date_end_census) : null ;
            $this->time_end_census = $lapso->time_end_census;

            $this->status_active_lapso_censu = $lapso->status_active_censu;
        }

        //dd($this->time_start_census, $this->time_end_census);

    }

    public function render()
    {
        $censuses = Census::where('user_id',Auth::id())->paginate(20); //dd($censuses);
        return view('livewire.profesor.census.index-component',['censuses'=>$censuses]);
    }

    public function save()
    {
        $this->validate();
        $this->census->user_id = Auth::id();
        $this->census->save();
        if ($this->census->status_admite=="true") {

            $estudiant = Estudiant::where('ci_estudiant',$this->census->ci_estudiant)->first();

            if (empty($estudiant)) {

                $representant = Representant::where('ci_representant',$this->census->ci_representant)->first();

                if (empty($representant)) {
                    $arr = [
                        'ci_representant'=> $this->census->ci_representant,
                        'name'=> $this->census->name_representant,
                        'phone'=> $this->census->phone_representant,
                        'email'=> $this->census->email_representant,
                        'status_active'=>  'true',
                        'status_blacklist'=>  'false',
                        'status_adviders'=>  'false',
                    ]; //dd($arr);
                    $representant = Representant::create($arr);
                }

                $arr = [
                    'planpago_id'=> 1 ,
                    'type_ci_id'=> 1 ,
                    'ci_estudiant'=> $this->census->ci_estudiant,
                    'name'=> $this->census->name ,
                    'lastname'=> $this->census->lastname ,
                    'representant_id'=> $representant->id,
                    'representant_ci'=> $representant->ci_representant,
                    'status_active'=>  'true',
                    'status_blacklist'=>  'false',
                    'date_birth'=>  $this->census->date_birth,
                    'town_hall_birth'=>  $this->census->town_hall_birth,
                    'state_birth'=>  $this->census->state_birth,
                    'country_birth'=>  $this->census->country_birth,
                    'dir_address'=>  $this->census->dir_address,
                ]; //dd($arr);
                $estudiant = Estudiant::create($arr);
                                
            }

            if (empty($estudiant->administrativa)) {
                $arr = [
                    'estudiant_id'=> $estudiant->id ,
                    'user_id'=> Auth::id() ,
                    'planpago_id'=> 1,
                ];
                $administrativa = Administrativa::create($arr);
            }

            if (empty($estudiant->inscripcion)) {
                $seccion = Seccion::where('name','U')->first();
                $arr = [
                    'tipo_id'=> 1 ,
                    'seccion_id'=> $seccion->id,
                    'estudiant_id'=> $estudiant->id,
                    'escolaridad_id'=> 1,
                    'programacion_id'=> 1,
                ];
                $inscripcion = Inscripcion::create($arr);
            }


            // $enrollments = Enrollment::where('ci_estudiant',$estudiant->ci_estudiant)->first();
            // if (empty($enrollments) && $this->census->status_admite=='true') {
            //     $arr = $this->census->toArray();
            //     $enrollment = Enrollment::create($arr);
            // }
            
        }
        $this->enrollment_id = $this->census->id;
        $this->census = new Census;
        $title = '¡Excelente, buen trabajo! ';
        $html = 'Operación realizada exitosamente';
        $this->showSwal($title,$html);
    }

    public function showSwal($title,$html,$icon='success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer'=>6000,
            'icon'=>$icon,
            'toast'=>false,
            'position'=>'center',
            'type' => 'warning',
        ]);
    }

    /*
    public function alertConfirm($id)
    {
        $mailer = Mailer::findOrFail($id);
        $this->dispatchBrowserEvent('swal:confirm', [
            'type' => 'question',
            'message' => 'Estas seguro? ',
            'text' => 'Sí realizas esta operación, no la podrás revertir',
            'id'=>$mailer->id
        ]);
    }

    public function alertQuestion($id,$method)
    {
        $mailer = Mailer::findOrFail($id);
        $this->dispatchBrowserEvent('swal:question', [
            'type' => 'question',
            'message' => 'Estas seguro de crear la cola de correos automatizados ? ',
            'text' => 'Sí realizas esta operación, no la podrás revertir.',
            'id'=>$mailer->id,
            'method'=>$method
        ]);
    }
    */
}
