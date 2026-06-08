<?php
namespace App\Models\app\Planpago\Functions\Payment;

trait Relations {

    /*INI RELACIONES*/
    public function payments()
    {
        return $this->hasMany('App\Models\app\Estudiante\Representant','representant_id');
    }
    /*FIN RELACIONES*/

}
