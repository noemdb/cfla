<?php
namespace App\Http\Controllers\Administracion\Tab;

use App\Http\Controllers\Controller;

class BaremoController extends Controller
{
    public function index()
    {
        return view('administracion.configuraciones.baremos.index');
    }
}
