<?php

namespace App\Http\Livewire\Bot;

use Livewire\Component;

use Illuminate\Support\Facades\DB;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Planpago\ExchangeRate;
use App\Models\app\Bot\Bmain;
use App\Models\app\Bot\Bmessege;
use App\Models\app\Bot\Boption;
use Carbon\Carbon;

class IndexComponent extends Component
{
	public $area,$bmain,$bmain_id,$messege,$response;
    public $history,$messegeCount;

    public function render()
    {
        return view('livewire.bot.index-component');
    }

    protected $listeners = ['messegeAdded' => 'incrementMessegeCount'];

    public function incrementMessegeCount()
    {
        $this->messegeCount = count($this->history);
    }

    public function mount($area = null)
	{
		$this->bmain = Bmain::where('area',$area)->where('status_active','true')->orderBy('created_at','desc')->first();
		$this->area = ($this->bmain) ? $area : null ;
		$this->bmain_id = ($this->bmain) ? $this->bmain->id : null ;
        $this->setHistory('Hola',$this->getMenu());
	}

    public function setHistory($messege,$response)
    {
        $this->history[] = [
            'messege'=>$messege,
            'response'=>$response,
        ];
        $this->emit('messegeAdded');
    }


    public function send()
	{
        $this->setHistory($this->messege,$this->getResponse());
        $this->messege = null;
        $this->response = null;
	}

    public function getMenu()
    {
        if ($this->bmain) {
            $boptions = Boption::where('bmain_id',$this->bmain->id)->get(); //dd($this->bmain->id,$boptions);
            $messege = $this->bmain->description."<br>";
            foreach ($boptions->sortBy('key') as $boption) {
                $messege .= $this->getTextHtml($boption->description)."<br>";
            }
            return $messege;
        }
    }

    public function getResponse()
    {
        $response = "------------------------ <br>";
        $response .= 'Opción no encontrada<br>';
        $response .= "------------------------ <br>".$this->getMenu();

        if ($this->bmain_id) {
            if ($this->messege>=1 && $this->messege<=9) {
                $boption = Boption::where('bmain_id',$this->bmain_id)->where('key',$this->messege)->first(); //dd($this->messege,$boption);
                if ($boption) {

                    //normales
                    if ($this->messege >= 1 && $this->messege <= 5) {
                        $response = $boption->texthtml ; //dd($boption->text,$response);
                    }

                    //AREA ADMINISTRACION
                    if ($this->area == 'ADMINISTRACION') {
                        // Tasa de cambio oficial
                        if ($this->messege == "6") {
                            $exchange_rate = ExchangeRate::whereDate('date',Carbon::now())->first() ;
                            if ($exchange_rate) {
                                $response =
                                    "*Tasa de Cambio BCV*  [".Carbon::now()->format('d-m-y')."]".
                                    "</br> ```Bs. ".f_float($exchange_rate->ammount)."```";
                            } else {
                                $response = "</br> - No hay tasa de cambio oficial registrada";
                                $response .= "</br> - Las tasas de cambio se registran de lunes a viernes";
                            }
                        }
                    }
                }
            }

            //AREA ADMINISTRACION
            if ($this->area == 'ADMINISTRACION') {
                if (preg_match('/^\d{7,8}$/', $this->messege)) {
                    $representant = Representant::where('ci_representant',$this->messege)->first();
                    if ($representant) {
                        $response = $this->getRepresentantResponse($representant);
                    } else {
                        $response = "------------------------ <br>";
                        $response .= 'Cédula no encontrada<br>';
                        $response .= "------------------------ <br>".$this->getMenu();
                    }
                }
            }
        }
        else {
            $response = 'No hay un bot asociado esta área: '.$this->area.'. <br>'. $this->getMenu();
        }
        return $this->getTextHtml($response);
    }

    public function getRepresentantResponse(Representant $representant)
    {
        $ammount_expire_bill = $representant->total_exchange_ammount_expire_bill;
        $exchange_rate = ExchangeRate::whereDate('date',Carbon::now())->first() ;
        $ammountBs = ($exchange_rate) ? ($ammount_expire_bill * $exchange_rate->ammount) : null ;
        $ammountBs = ($ammountBs) ? round($ammountBs,2).' (Según Tasa BCV del día)': '[Sin Tasa BCV]';

        $json_message =
            "*Información Administrativa* ".
            "</br> *Nombre*: ".$representant->name.
            "</br> *CI*: ". $representant->ci_representant.
            "</br> *Deuda Vencida*: $ ".round($ammount_expire_bill,2)
            ;
        if ($exchange_rate) {
            $ammountBs = $ammount_expire_bill * $exchange_rate->ammount;
            $ammountBs = round($ammountBs,2) . ' (Según Tasa BCV del día)';
            $json_message .= "</br> *Deuda Vencida*: Bs ".$ammountBs;
        }

        $exchange_expire_bill = $representant->exchange_expire_bill_pendientes;
        if ($ammount_expire_bill > 0 ) {
            if ($exchange_expire_bill->isNotEmpty()) {
                $list = null;
                foreach ($exchange_expire_bill as $expire_bill) {
                    $list .= "</br>   -. ".$expire_bill['expire_bill_name']. ": ```$".round($expire_bill['ammount'],2).'```';
                }
                $json_message .= "</br> *Cuotas vencidas*:".$list;
            }
        }

        $unexpire_bills = $representant->exchange_unexpire_bill_pendientes->take(2);
        if ($unexpire_bills->count()) {
            $json_message .= "</br>------------------------</br>";
            $json_message .= "*Cuota(s) por vencer*:";
            foreach ($unexpire_bills as $unexpire_bill) {
                if ($unexpire_bill){
                    if (!empty($unexpire_bill['ammount'])){
                        $ammount = $unexpire_bill['ammount'];
                        $json_message .= "</br>   -. ".$unexpire_bill['expire_bill_name']." - $```".round($ammount,2)."``` | *".f_date($unexpire_bill['date_expiration'])."*";
                    }

                }
            }
        }

        $status_blacklist = $representant->status_blacklist;
        if ($status_blacklist) {
            $bad_exchange_ammount_expire_bill = $representant->bad_exchange_ammount_expire_bill;
            if ($bad_exchange_ammount_expire_bill > 0) {
                $json_message .= "</br>------------------------";
                $json_message .= "</br>*Este representante incumplió con el compromiso de pago en las fechas correspondientes en otro período escolar*:";
                $json_message .= "</br>Deuda de períodos anteriores: ``` $".round($bad_exchange_ammount_expire_bill,2)."```";
                $json_message .= "</br>------------------------";
            }
        }

        $json_message .= "</br>------------------------";
        $json_message .= "</br>Los reportes de pago se hacen efectivo en 2 o 3 días hábiles, mientras se realizan las conciliaciones bancarias respectiva.";
        $json_message .= "</br>------------------------";

        // $json_message .= "</br>------------------------";
        // $json_message .= "</br>En los pagos realizados a través del *TPV Botón de Pago CFLA*, la verificación, concialición y registro es automático.";
        // $json_message .= "</br>------------------------";

        $json_message .= "</br>------------------------";
        $json_message .= "</br>Correo Electrónico registrado: ".$representant->email;
        $json_message .= "</br>------------------------";

        return $this->getTextHtml($json_message);

    }

    public function getTextHtml($text)
    {
        //bold
        $pattern = '/\*(?=\w)/';
        $text = preg_replace($pattern, '<strong>', $text);
        $pattern = '/(?<=\w)\*/';
        $text = preg_replace($pattern, '</strong>', $text);

        //italic
        $pattern = '/\_(?=\w)/';
        $text = preg_replace($pattern, '<em>', $text);
        $pattern = '/(?<=\w)\_/';
        $text = preg_replace($pattern, '</em>', $text);

        //strike
        $pattern = '/\~(?=\w)/';
        $text = preg_replace($pattern, '<em>', $text);
        $pattern = '/(?<=\w)\~/';
        $text = preg_replace($pattern, '</em>', $text);

        //mono
        $pattern = '/\```(?=\w)/';
        $text = preg_replace($pattern, '<em>', $text);
        $pattern = '/(?<=\w)\```/';
        $text = preg_replace($pattern, '</em>', $text);

        return $text;
    }

}
