<?php

namespace App\Http\Controllers\Helper;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ManulasController extends Controller
{

    public function CalendarEventManage()
    {
        return view('administracion.instructions.manageCalendarEvent');
    }

    public function representantsBlacklistManage()
    {
        return view('administracion.instructions.representantsBlacklistManage');
    }

    public function boletinRevision()
    {
        return view('administracion.instructions.boletinRevision');
    }

    /**
     * Mostrar ingresosListado
     */
    public function activities()
    {
        return view('profesors.instructions.activities');
    }

    /**
     * Mostrar ingresosListado
     */
    public function sendNotificationsCollection()
    {
        return view('administracion.instructions.sendNotificationsCollection');
    }


    /**
     * Mostrar ingresosListado
     */
    public function wizardAdministrativas()
    {
        return view('administracion.instructions.wizardAdministrativas');
    }

    /**
     * Mostrar ingresosListado
     */
    public function paymentsListado()
    {
        return view('administracion.instructions.paymentsListado');
    }

    /**
     * Mostrar ingresosListado
     */
    public function ingresosListado()
    {
        return view('administracion.instructions.ingresosListado');
    }

    /**
     * Mostrar registropagosListado
     */
    public function registropagosListado()
    {
        return view('administracion.instructions.registropagosListado');
    }

    /**
     * Mostrar Historico de Pagos del Representante
     */
    public function retirosProntoPago()
    {
        return view('administracion.instructions.retirosProntoPago');
    }

    /**
     * Mostrar Historico de Pagos del Representante
     */
    public function representantsHistorico()
    {
        return view('administracion.instructions.representantsHistorico');
    }

    /**
     * Mostrar partial de libros de facturacion
     */
    public function bancosLibros()
    {
        return view('administracion.instructions.bancosLibros');
    }

    /**
     * Mostrar partial de listado de saldos
     */
    public function representantsSaldos()
    {
        return view('administracion.instructions.representantsSaldos');
    }

    /**
     * Mostrar partial de retiros
     */
    public function cuentaxpagars_list()
    {
        return view('administracion.instructions.cuentaxpagarsList');
    }
    /**
     * Mostrar partial de retiros
     */
    public function asistenteRegistroPago()
    {
        return view('administracion.instructions.asistenteRegistroPago');
    }

    /**
     * Mostrar Asistente de Deuda Individual
     */
    public function asistenteIndividual()
    {
        return view('administracion.instructions.asistenteIndividual');
    }

    /**
     * Mostrar partial de retiros
     */
    public function excecutions()
    {
        return view('evaluacions.instructions.excecutions');
    }

    /**
     * Mostrar partial de retiros
     */
    public function pases()
    {
        return view('evaluacions.instructions.pases');
    }

    /**
     * Mostrar partial de retiros
     */
    public function diagnostic()
    {
        return view('evaluacions.instructions.diagnostics');
    }

    /**
     * Mostrar partial de retiros
     */
    public function retiros()
    {
        return view('administracion.instructions.retiros');
    }

    /**
     * Mostrar partial de conceptopagos
     */
    public function conceptopagos()
    {
        return view('administracion.instructions.conceptopagos');
    }

    /**
     * Mostrar partial de liberaciones
     */
    public function liberaciones()
    {
        return view('administracion.instructions.liberaciones');
    }


    /**
     * Mostrar partial de cancelaciones
     */
    public function cancelations()
    {
        return view('administracion.instructions.cancelations');
    }

    /**
     * Mostrar partial de cuentas por pagar
     */
    public function cuentasxpagars()
    {
        return view('administracion.instructions.cuentasxpagars');
    }

    /**
     * Mostrar partial de estructura de cobranzas
     */
    public function estruturaCobranzas()
    {
        return view('administracion.instructions.estruturaCobranzas');
    }

    /**
     * Mostrar partial de gestión de roles
     */
    public function gestionRols()
    {
        return view('administracion.instructions.gestionRols');
    }

    /**
     * Mostrar partial de gestión de trabajadores
     */
    public function manageWorker()
    {
        return view('administracion.instructions.manageWorker');
    }

    /**
     * Mostrar partial de morosidad
     */
    public function morosidad()
    {
        return view('administracion.instructions.morosidad');
    }

    /**
     * Mostrar partial de restablecimiento BIO
     */
    public function restablecimientoBIO()
    {
        return view('administracion.instructions.restablecimientoBIO');
    }

    /**
     * Cargar partial dinámicamente (método alternativo)
     */
    public function loadPartial($partial)
    {
        $allowedPartials = [
            'cancelations',
            'cuentasxpagars',
            'estruturaCobranzas',
            'gestionRols',
            'manageWorker',
            'morosidad',
            'restablecimientoBIO'
        ];

        if (!in_array($partial, $allowedPartials)) {
            abort(404, 'Partial no encontrado');
        }

        $viewPath = 'administracion.instructions.' . $partial;

        if (!view()->exists($viewPath)) {
            abort(404, 'Vista no encontrada');
        }

        return view($viewPath);
    }
}
