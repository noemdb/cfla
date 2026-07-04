<?php
namespace App\Models\app\Estudiante\Functions\Estudiant;

trait Admon
{
    public function getrAdmonAttribute()
    {
        $retiro = $this->retiro;
        $tipo = null;
        if ($retiro) {
            $tipo = ($retiro->status_admon == 'true') ? true : false ;
        }
        return $tipo;
    }
}
