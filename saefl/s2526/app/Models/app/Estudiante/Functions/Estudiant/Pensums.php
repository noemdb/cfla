<?php
namespace App\Models\app\Estudiante\Functions\Estudiant;

trait Pensums
{
    public function getPensumsAttribute()
    {
        return ($this->grado) ? $this->grado->pensums : collect() ;
    }

}

?>
