<?php

namespace App\Livewire\Evaluacion\Inicial;

/**
 * Revisión de la PLANIFICACIÓN QUINCENAL (`eiplanningbwks`).
 *
 * Se diferencia de la semanal solo en la tabla, y la tabla vive en el catálogo
 * compartido ({@see \App\Services\Inicial\RegistroInicial::columnas()}): esta
 * clase no tiene nada propio que declarar.
 */
class EiplanningbwkComponent extends EvaluacionDocumentComponent
{
    protected string $entidad = 'eiplanningbwks';
}
