<?php

namespace App\Http\Livewire\Administracion\Refund;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;

use App\Models\app\Planpago\Refund;
use App\Models\app\Planpago\RegistroPagoCombinado;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Planpago\MetodoPago;
use App\Models\app\Institucion\Banco;

class IndexComponent extends Component
{
    public $refund,$refunds;
    public $modeIndex,$modeCreate,$modeShow,$statusLoad,$modeDetails;
    public $name,$representant,$observation;
    public $banco_list,$list_comment_representant,$list_comment,$list_representant,$list_registro_pago_combinado,$list_credito_a_favor;
    public $registro_pago_combinado,$credito_a_favor;
    public $method_pay_list;
    public $refund_id,$registro_pago_combinado_id,$credito_a_favor_id,$method_pay_id,$banco_id,$representant_id,$number_i_pay,$date_transaction,$ammount,$ammount_exchange,$observations;

    public function mount($representant_id = null,$registro_pago_combinado_id = null,$credito_a_favor_id = null)
    {
        $this->list_comment = Refund::COLUMN_COMMENTS;
        $this->list_comment_representant = Representant::COLUMN_COMMENTS;

        $this->method_pay_list    = MetodoPago::list_metodo_pago();
        $this->banco_list         = Banco::banco_list();

        $this->list_representant = Array();
        $this->list_registro_pago_combinado = Array();
        $this->list_credito_a_favor = Array();

        $this->modeIndex=true;
        $this->modeCreate = false ;
        $this->modeShow = false;
        $this->statusLoad = false; 

        if ($representant_id) {
            $this->representant_id = $representant_id ;
            $this->modeCreate = true ;
            $this->modeIndex = false ;
            $this->loadData();
        }

        if ($registro_pago_combinado_id) {
            $this->registro_pago_combinado_id = $registro_pago_combinado_id ;
            $this->modeCreate = true ;
            $this->modeIndex = false ;
            $this->loadCombinado($registro_pago_combinado_id);
        }     
   
    }

    public function render()
    {
        $this->refunds = Refund::all(); //dd($this->refunds);
        return view('livewire.administracion.refund.index-component');
    }

    public function create()
    {
        $this->list_representant = Array();
        $this->modeIndex=false;
        $this->modeCreate=true;
        $this->modeShow = false;
        $this->modeDetails = false;
        $this->refund_id = null;
        $this->statusLoad = false;
        $this->name = null;
        $this->resetErrorBag();
        $this->resetInput();
    }

    public function details($refound_id)
    {
        $this->modeIndex=false;
        $this->modeCreate=false;
        $this->modeShow = false;
        $this->modeDetails = true;
        $this->refund_id = $refound_id;
        $this->name = null;
        $this->refund = Refund::findOrfail($refound_id);
    }

    public function show($registro_pago_combinado_id)
    {
        $this->modeIndex=false;
        $this->modeCreate=false;
        $this->modeShow = true;
        $this->modeDetails = false;
        $this->refund_id = null;
        $this->name = null;
        $this->registro_pago_combinado = RegistroPagoCombinado::findOrfail($registro_pago_combinado_id);
    }

    public function updatedName()
    {
        $list_representant = Representant::Where('name', 'like', "%".$this->name."%")->orderBy('name')->get(); //dd($list_representant);
        if ($list_representant->count()) {
            $this->list_representant = $list_representant->pluck('name','id') ;
            $this->representant_id = $list_representant->first()->id;

            $this->registro_pago_combinado = null;
            $this->registro_pago_combinado_id = null;
            $this->credito_a_favor = null;
            $this->credito_a_favor_id = null;
            $this->ammount = null;
            $this->ammount_exchange = null;
        }
    }

    public function loadCombinado($registro_pago_combinado_id)
    {
        $registro_pago_combinado = RegistroPagoCombinado::find($registro_pago_combinado_id);

        if ($registro_pago_combinado) {

            $this->resetInput();
            $this->list_credito_a_favor = Array();

            $this->registro_pago_combinado = $registro_pago_combinado;
            $this->registro_pago_combinado_id = $registro_pago_combinado->id;

            $this->representant = $registro_pago_combinado->representant;
            $this->representant_id = $registro_pago_combinado->representant_id;
            $this->name = substr($this->representant->name, 0,20) ;

            $this->list_representant = Representant::where('id',$this->representant_id)->pluck('name','id');
            $this->list_registro_pago_combinado = RegistroPagoCombinado::Where('id',$this->registro_pago_combinado_id)->pluck('id','id');

            $credito_a_favors = $this->registro_pago_combinado->credito_a_favor_disponibles;
            if ($credito_a_favors->count()) {
                $this->list_credito_a_favor = $credito_a_favors->pluck('id','id');
                $this->credito_a_favor = $credito_a_favors->first();
                $this->credito_a_favor_id = $this->credito_a_favor->id;
                $this->ammount = $this->credito_a_favor->credito_ammount;
                $this->ammount_exchange = $this->credito_a_favor->exchange_ammount;
            }

            $this->statusLoad = true;
            $this->modeCreate = true;
        }
    }

    public function loadData()
    {
        $list_representant = Representant::where('id',$this->representant_id)->get();
        $this->name = $list_representant->first()->name;
        $this->list_representant = $list_representant->pluck('name','id'); //dd($this->list_representant);
        $this->list_registro_pago_combinado = RegistroPagoCombinado::Where('representant_id',$this->representant_id)->orderBy('created_at','desc')->pluck('description','id')->sortByDesc('description'); //dd($this->list_registro_pago_combinado);
        $this->list_credito_a_favor = Array();
        
        $this->credito_a_favor = null;
        $this->credito_a_favor_id = null;
        $this->ammount = null;
        $this->ammount_exchange = null;
        $this->statusLoad = true;
        $this->resetErrorBag();
    }

    public function loadCafList()
    {
        $registro_pago_combinado = RegistroPagoCombinado::find($this->registro_pago_combinado_id);
        $this->list_credito_a_favor = Array();
        if ($registro_pago_combinado) {
            // $credito_a_favors = $registro_pago_combinado->creditos_generados;
            $credito_a_favors = $registro_pago_combinado->credito_a_favor_disponibles;
            if ($credito_a_favors->count()) {
                $this->list_credito_a_favor = $credito_a_favors->pluck('id','id');
            }
        }
    }

    public function loadCaf()
    {
        $this->credito_a_favor = CreditoAFavor::find($this->credito_a_favor_id);
        if ($this->credito_a_favor) {
            $this->ammount = $this->credito_a_favor->credito_ammount;
            $this->ammount_exchange = $this->credito_a_favor->exchange_ammount;
        }
    }

    public function store()
    {
        $this->validatingRequest();
        $status_return = ($this->credito_a_favor_id) ? false : true ;
        $arr = [
            'registro_pago_combinado_id' => $this->registro_pago_combinado_id,
            'credito_a_favor_id'         => $this->credito_a_favor_id,
            'method_pay_id'              => $this->method_pay_id,
            'banco_id'                   => $this->banco_id,
            'representant_id'            => $this->representant_id,
            'number_i_pay'               => $this->number_i_pay,
            'date_transaction'           => $this->date_transaction,
            'ammount'                    => $this->ammount,
            'ammount_exchange'           => $this->ammount_exchange,
            'status_return'              => $status_return,
            'observations'               => $this->observations
        ];  //dd($arr);    
        $this->refund = Refund::create($arr);

        if ($this->credito_a_favor_id) {
            $creditoafavor = CreditoAFavor::findOrFail($this->credito_a_favor_id);
            $creditoafavor->fill(['status_omitted'=>'true','observations_user'=>'CAF omitido por devolución - Usuario: '.Auth::user()->username]);
            $creditoafavor->save();
        }        

        $this->modeIndex = true;
        $this->modeCreate = false;

        $title = '¡Excelente, buen trabajo! ';
        $html = 'Operación realizada exitosamente';  
                
        $this->resetInput();

        session()->flash('operp_ok', 'Guardado!!!.');
        $this->resetErrorBag();
    }

    public function validatingRequest()
    {
        
        $this->validate([
                'registro_pago_combinado_id'    => 'required|integer',
                'credito_a_favor_id'            => 'nullable',
                'method_pay_id'                 => 'required|integer',
                'banco_id'                      => 'required|integer',
                'representant_id'               => 'required|integer',
                'number_i_pay'                  => 'required|unique:refunds,number_i_pay',
                'date_transaction'              => 'required|date',
                'ammount'                       => 'required|numeric',
                'ammount_exchange'              => 'required|numeric',
                'observations'                  => 'nullable'
            ],
            [
                'registro_pago_combinado_id.required'     => 'El campo pagocombidado es requerido',
            ],
            [
                'registro_pago_combinado_id' => $this->list_comment['registro_pago_combinado_id'],
                'credito_a_favor_id'         => $this->list_comment['credito_a_favor_id'],
                'method_pay_id'              => $this->list_comment['method_pay_id'],
                'banco_id'                   => $this->list_comment['banco_id'],
                'representant_id'            => $this->list_comment['representant_id'],
                'number_i_pay'               => $this->list_comment['number_i_pay'],
                'date_transaction'           => $this->list_comment['date_transaction'],
                'ammount_exchange'           => $this->list_comment['ammount_exchange'],
                'observations'               => $this->list_comment['observations']
            ]
        );
    }

    public function resetInput()
    {
        $this->registro_pago_combinado_id = null;
        $this->credito_a_favor_id = null;
        $this->method_pay_id = null;
        $this->banco_id = null;
        $this->representant_id = null;
        $this->number_i_pay = null;
        $this->date_transaction = null;
        $this->ammount = null;
        $this->ammount_exchange = null;
        $this->observation = null;
        $this->registro_pago_combinado = null;        
        $this->credito_a_favor = null;        
        
        $this->list_representant = Array();
        $this->list_registro_pago_combinado = Array();
        $this->list_credito_a_favor = Array();
        $this->observations = null;
    }

    public function closeCreateMode()
    {        
        $this->resetInput();
        $this->name = null;
        $this->modeIndex=true;
        $this->modeCreate = false;
        $this->modeShow = false;
        $this->modeDetails = false;
        $this->statusLoad = false;
        $this->refunds = Refund::all();
    }

    public function closeShowMode()
    {        
        $this->resetInput();
        $this->name = null;
        $this->modeIndex=true;
        $this->modeCreate = false;
        $this->modeShow = false;
        $this->modeDetails = false;
        $this->statusLoad = false;
        $this->refunds = Refund::all();
    }

    public function closeDetailsMode()
    {        
        $this->resetInput();
        $this->name = null;
        $this->modeIndex=true;
        $this->modeCreate = false;
        $this->modeShow = false;
        $this->modeDetails = false;
        $this->statusLoad = false;
        $this->refunds = Refund::all();
    }

}
