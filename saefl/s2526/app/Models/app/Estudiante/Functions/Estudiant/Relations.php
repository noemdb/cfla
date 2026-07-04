<?php

namespace App\Models\app\Estudiante\Functions\Estudiant;

use App\Models\app\Bienestar\StudentRecord;
use App\Models\app\Incident\Incident;
use App\Models\app\Inicial\Eifinalk;
use App\Models\app\Instrument\DiagSession;
use App\Models\app\SocialAction\CommunityHour;

trait Relations
{
    /*INI relaciones entre modelos*/

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function registro_pagos()
    {
        return $this->hasMany('App\Models\app\Planpago\RegistroPago');
    }
    public function diag_sessions()
    {
        return $this->hasOne(DiagSession::class);
    }
    public function registropagos()
    {
        return $this->hasMany('App\Models\app\Planpago\RegistroPago');
    }
    public function planbeneficos()
    {
        return $this->hasMany('App\Models\app\Estudiante\PlanBenefico');
    }
    public function cuentaxpagars()
    {
        return $this->hasMany('App\Models\app\Planpago\Cuentaxpagar');
    }
    public function ingresos()
    {
        return $this->hasMany('App\Models\app\Estudiante\Ingreso');
    }
    public function abonos()
    {
        return $this->hasMany('App\Models\app\Estudiante\Abono', 'estudiant_id');
    }
    public function creditoafavors()
    {
        return $this->hasMany('App\Models\app\Estudiante\CreditoAFavor');
    }
    public function inscripcion()
    {
        return $this->hasOne('App\Models\app\Estudiante\Inscripcion');
    }
    public function prosecucion()
    {
        return $this->hasOne('App\Models\app\Pescolar\Prosecucion');
    }
    public function historico_nota()
    {
        return $this->hasOne('App\Models\app\HistoricoNota');
    }
    public function retiro()
    {
        return $this->hasOne('App\Models\app\Estudiante\Retiro');
    }
    public function administrativa()
    {
        return $this->hasOne('App\Models\app\Estudiante\Administrativa');
    }
    public function preinscripcion()
    {
        return $this->hasOne('App\Models\app\Pescolar\Preinscripcion');
    }
    public function representant()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Representant');
    }
    public function type_ci()
    {
        return $this->belongsTo('App\Models\app\Estudiante\TypeCi');
    }
    public function boletins()
    {
        return $this->hasMany('App\Models\app\Estudiante\Boletin');
    }
    public function boletin_revisions()
    {
        return $this->hasMany('App\Models\app\Estudiante\BoletinRevision');
    }
    public function materia_pendientes()
    {
        return $this->hasMany('App\Models\app\Estudiante\MateriaPendiente');
    }
    public function boletin_ajustes()
    {
        return $this->hasMany('App\Models\app\Estudiante\BoletinAjuste');
    }
    public function hnotas()
    {
        return $this->hasMany('App\Models\app\HistoricoNota\Hnota');
    }
    public function historico_notas()
    {
        return $this->hasMany('App\Models\app\HistoricoNota');
    }

    public function ecualitativas()
    {
        return $this->hasMany('App\Models\app\Profesor\Pevaluacion\Ecualitativa');
    }
    public function edescriptivas()
    {
        return $this->hasMany('App\Models\app\Profesor\Pevaluacion\Edescriptiva');
    }

    public function student_record()
    {
        return $this->hasOne(StudentRecord::class);
    }
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
    public function community_hour()
    {
        return $this->hasOne(CommunityHour::class);
    }
    public function eifinalks()
    {
        return $this->hasMany(Eifinalk::class, 'estudiant_id');
    }
    /*****************************************************************************************/
}
