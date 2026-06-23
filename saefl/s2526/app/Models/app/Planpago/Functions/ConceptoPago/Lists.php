<?php
namespace App\Models\app\Planpago\Functions\ConceptoPago;

use App\Models\app\Planpago\ConceptoPago;
use Illuminate\Support\Facades\DB;

trait Lists {

    public static function list_conceptopago() /* usada para llenar los objetos de formularios select*/
    {
        return ConceptoPago::select('nom_concepto_pagos.name as name', 'concepto_pagos.id as id')
        ->join('nom_concepto_pagos', 'nom_concepto_pagos.id', '=', 'concepto_pagos.nom_concepto_pago_id')
        ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')
        ->where('cuentaxpagars.type','GENERAL')
        ->where('concepto_pagos.status_active','true')
        ->wherenull('cuentaxpagars.deleted_at')
        ->orderby('concepto_pagos.id','asc')
        ->pluck('name', 'id');
    }

}
