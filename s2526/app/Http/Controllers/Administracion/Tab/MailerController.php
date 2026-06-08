<?php

namespace App\Http\Controllers\Administracion\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion\Autoridad;
use Illuminate\Http\Request;

class MailerController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['auth','is_control']);
        $this->middleware(['auth','is_common']);
    }
    public function index()
    {
        return view('administracion.mailers.index');
    }
}
