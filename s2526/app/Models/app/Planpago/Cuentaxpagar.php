<?php

namespace App\Models\app\Planpago;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\app\Planpago;
use App\Models\app\Planpago\DescuentoAplicado;
use App\Models\app\Pescolar;
use App\Models\app\Pescolar\Grado;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

//trait
use App\Models\app\Planpago\Functions\Cuentaxpagar\Relations;
use App\Models\app\Planpago\Functions\Cuentaxpagar\Charts;
use App\Models\app\Planpago\Functions\Cuentaxpagar\Exchanges;
use App\Models\app\Planpago\Functions\Cuentaxpagar\Administracions;
use App\Models\app\Planpago\Functions\Cuentaxpagar\AutoPayment;
use App\Models\app\Planpago\Functions\Cuentaxpagar\Lists;

class Cuentaxpagar extends Model
{
    use SoftDeletes;

    use Relations;
    use Charts;
    use Administracions;
    use Exchanges;
    use AutoPayment;
    use Lists;

    protected $fillable = [
        'planpago_id',
        'name',
        'type',
        'estudiant_id',
        'date_expiration',
        'date_late_payment',
        'date_calendar_start',
        'date_calendar_end',
        'description',
        'observations',
        'status_active',
        'status_inscription',
        'status_bad',
        'status_late_payment',
        'enable_late_payment',
        'quota_original_id',
    ];

    const COLUMN_COMMENTS = [
        'id' => 'Identificador',
        'planpago_id' => 'Plan de pago',
        'name' => 'Nombre',
        'type' => 'Tipo',
        'date_expiration' => 'Fecha de vencimiento',
        'date_late_payment' => 'Fecha de recargo por morosidad',
        'date_calendar_start' => 'Fecha Inicial Calendario',
        'date_calendar_end' => 'Fecha Final Calendario',
        'description' => 'Descripción',
        'observations' => 'Observaciones',
        'status_inscription' => 'La cancelación establece la inscripción administrativa',
        'estudiant_id' => 'Estudiante',
        'status_bad' => 'Cuenta incobrable',
        'status_late_payment' => 'Cuota por morosidad',
        'enable_late_payment' => 'Habilitada para cargo por morosidad',
        'quota_original_id' => 'clave para unicidad de recarga por morosidad',
    ];

    public function getStatusAsignationAttribute()
    {
        return (empty($this->concepto_cancelados->count())) ? true : false;
    }

    public function getStatuspartialEditAttribute()
    {
        return (empty($this->concepto_cancelados->count())) ? true : false;
    }

    public function getStatusEditAttribute()
    {
        $date = Carbon::now()->format('Y-m-d');
        $state = ($this->date_expiration <= $date && $this->concepto_cancelados->isNotEmpty()) ? false : true;
        // return $state ;
        return true;
    }

    public function getStatusDeleteAttribute()
    {
        return (empty($this->conceptopagos->count())) ? true : false;
    }

    public function getStatusRegistroPagoAttribute()
    {
        return (empty($this->registropagos->count())) ? true : false;
    }
}
