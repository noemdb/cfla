<?php

namespace App\Http\Livewire\Administracion\ExchangeRate;

use App\Http\Controllers\Restapi\Exchange\CapriceController;
use App\Http\Controllers\Restapi\Exchange\GoutteController;
use App\Models\app\Planpago\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Livewire\Component;

use App\Http\Controllers\Restapi\Exchange\KurokuroController;

class ListComponent extends Component
{
    public $exchange_rate,$date,$today;
    public $connectTimeOutError,$connectTimeOutErrorMessege;
    public $caprice,$exchange_rate_suggested;

    public function mount()
    {
        $this->date = Carbon::now();
        $this->today = Carbon::now();
        $date = $this->date->format('Y-m-d');
        $this->exchange_rate = ExchangeRate::whereDate('date',$date)->first(); //dd($this->exchange_rate);
        if (is_null($this->exchange_rate)) {
            $this->getExchanRateSuggested();
        }
    }

    public function render()
    {
        return view('livewire.administracion.exchange-rate.list-component');
    }

    public function upDate()
    {
        $this->date = $this->date->addDays(1);
        $date = $this->date->format('Y-m-d');
        $this->exchange_rate = ExchangeRate::where('date',$date)->first();
    }

    public function downDate()
    {
        $this->date = $this->date->subDays(1);
        $date = $this->date->format('Y-m-d');
        $this->exchange_rate = ExchangeRate::where('date',$date)->first();
    }

    public function setExchangeKuroKuro()
    {
        $kuro = New KurokuroController;
        $kuro->setExchangeRate();
        $this->mount();
    }

    public function getExchanRateSuggested()
    {
        // $goutte = New GoutteController;
        // $this->exchange_rate_suggested = round($goutte->getExchanRateToday(),2);
        $this->exchange_rate_suggested = round(ExchangeRate::getAmmountExchange(),2);
    }

    public function setExchanRateSuggested()
    {
        if (ExchangeRate::getAmmountExchange() == null) {
            $today = Carbon::now()->format('Y-m-d');
            if ($this->exchange_rate_suggested) {
                $arr = [
                    'currency_id'=>1, //Bolivares
                    'currency_referential_id'=>1, //Dolar
                    'date'=>$today,
                    'ammount'=>round($this->exchange_rate_suggested,2),
                    'source'=>'Caprice',
                    'user_id'=>1,
                ];
                ExchangeRate::create($arr);
            }
        }
        $this->mount();
    }
}
