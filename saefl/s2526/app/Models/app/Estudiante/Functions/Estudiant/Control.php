<?php
namespace App\Models\app\Estudiante\Functions\Estudiant;

trait Control
{
    public function getrControlAttribute()
    {
        $retiro = $this->retiro;
        $tipo = null;
        if ($retiro) {
            $tipo = ($retiro->tipo == 'control') ? true : false ;
        }
        return $tipo;
    }    
}
