<?php

namespace App\Http\Controllers\Bienestar\Tab;

use App\Http\Controllers\Controller;
use App\Models\ResendEmail;
use App\Services\ResendEmailService;
use Illuminate\Http\Request;

class ResendController extends Controller
{
    protected $resendEmailService;

    public function __construct(ResendEmailService $resendEmailService)
    {
        $this->resendEmailService = $resendEmailService;
    }

    public function index()
    {
        $error = session('error');
        return view('bienestars.resend.index', compact('error'));
    }

    public function show($id)
    {
        try {
            $email = ResendEmail::findOrFail($id);
            return view('bienestars.resend.show', compact('email'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al obtener los detalles del mensaje: ' . $e->getMessage());
        }
    }
}
