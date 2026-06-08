<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//validation request
use App\Http\Requests\Administracion\Configuracion\UpdateExchangeRateRequest;
use App\Http\Requests\Administracion\Configuracion\CreateExchangeRateRequest;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Planpago\ExchangeRate;
use App\Models\app\Planpago\Currency;
use App\Models\app\Planpago\ReferentialCurrency;
use Illuminate\Support\Facades\DB;

class ExchangeRateController extends Controller
{
    public function __construct() { $this->middleware(['auth','is_admon']); }

    public function index()
    {
        $exchange_rates = ExchangeRate::all()->sortByDesc('date');
        $list_comment = ExchangeRate::COLUMN_COMMENTS;
        return view('administracion.configuraciones.exchange_rates.index',compact('exchange_rates','list_comment'));
    }

    public function create()
    {
        $exchange_rates = ExchangeRate::all()->sortByDesc('date')->take(5);

        $list_comment = ExchangeRate::COLUMN_COMMENTS;
        $list_currency = Currency::pluck('name','id');
        $list_referential_currency = ReferentialCurrency::pluck('name','id');
        return view('administracion.configuraciones.exchange_rates.create',compact('list_comment','list_currency','list_referential_currency','exchange_rates'));
    }

    public function store(CreateExchangeRateRequest $request)
    // public function store(Request $request)
    {
        // dd($request->all());
        $exchange_rate = ExchangeRate::create($request->all());

        /* llenar exchange_rate para periodos anteriores */
        // if ($exchange_rate) {
        //     $exchange_rate->fill_ammount_exchange_ingresos;
        //     $exchange_rate->fill_ammount_exchange_cafs;
        //     $insert = DB::connection('s2021')
        //         ->table('exchange_rates')
        //         ->insert([
        //         'user_id'=>$request->user_id,
        //         'currency_id'=>1,
        //         'currency_referential_id'=>1,
        //         'ammount'=>$request->ammount,
        //         'ammount_buy'=>null,
        //         'date'=>$request->date,
        //         'source'=>$request->source,
        //         'observations'=>$request->observations,
        //     ]);
        // }

        Session::flash('operp_ok','Registro guardado exitosamente');
        return redirect()->route('administracion.configuraciones.exchange_rates.create');
    }

}
