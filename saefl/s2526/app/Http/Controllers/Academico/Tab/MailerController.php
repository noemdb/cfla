<?php

namespace App\Http\Controllers\Academico\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion\Autoridad;
use Illuminate\Http\Request;

class MailerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_academico']);
    }
    public function index()
    {
        return view('academicos.mailers.index');
    }
}
