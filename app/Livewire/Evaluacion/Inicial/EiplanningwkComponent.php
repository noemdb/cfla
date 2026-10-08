<?php

namespace App\Livewire\Evaluacion\Inicial;

/**
 * Revisión de la PLANIFICACIÓN SEMANAL (`eiplanningwks`).
 *
 * Las columnas no se declaran aquí: salen de
 * {@see \App\Services\Inicial\RegistroInicial::columnas()}, que es la definición
 * compartida con la tabla de la perspectiva de Planificación.
 */
class EiplanningwkComponent extends EvaluacionDocumentComponent
{
    protected string $entidad = 'eiplanningwks';
}
