<?php

namespace App\Http\Controllers\Administracion\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Estudiante\Representant;
use Illuminate\Http\Request;

class CollectionPoliceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function asistent()
    {
        $representants = Representant::inRandomOrder()->get(); //dd($representants);
        $data = collect();
        $representant = collect();
        foreach ($representants as $item) {
            $exchange_ammount_expire_bill = $item->exchange_ammount_expire_bill;
            if ($exchange_ammount_expire_bill > 0) {
                $representant = $item;
                break;
            }
        }

        return view('administracion.collection_polices.asistent',compact('representant'));
    }
}
