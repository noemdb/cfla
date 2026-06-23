<?php

namespace App\Http\Controllers\Administracion\Tab\Diagnostic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReferentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_admin']);
    }

    public function index()
    {
        return view('administracion.diagnostics.referents.index');
    }
}
