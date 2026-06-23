<?php

namespace App\Models\app\Estudiante\Functions\Representants;

use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;

use Carbon\Carbon;

trait FixDBs
{
    // fix para la base de datos
    public function fix_registro_pagos()
    {

        $datas = collect();

        $registro_pago_combinados = RegistroPagoCombinado::irregular_pay_representant($this->id);

        dd($registro_pago_combinados);

        foreach ($registro_pago_combinados as $registro_pago_combinado) {

            $registro_pagos = RegistroPago::withTrashed()->where('registro_pago_combinado_id',$registro_pago_combinado['id'])->get();

            dd($registro_pagos );

            foreach ($registro_pagos as $registro_pago) {

                $data = $registro_pago->fix_registro_pago_zero($registro_pago_combinado);

                if ($data->isNotEmpty()) {

                    $datas->push($data);

                }

            }

        }

        // return json_encode($datas);
    }
    public function fix_registro_pagos_zero()
    {
        $registro_pagos = RegistroPago::where('representant_id',$this->id)->get();
        // dd($registro_pagos);
        foreach ($registro_pagos as $registro_pago) {

            if (!empty($registro_pago->registro_pago_combinado)) {

                $registro_pago_combinado = $registro_pago->registro_pago_combinado;

                // dd($registro_pago_combinado);

                $registro_pago->fix_registro_pago_zero_id($registro_pago_combinado->id);

            }

        }
    }
}
