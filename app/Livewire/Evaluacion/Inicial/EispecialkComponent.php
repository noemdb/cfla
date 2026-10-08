<?php

namespace App\Livewire\Evaluacion\Inicial;

/**
 * Revisión del PLAN ESPECIAL (`eispecialks`).
 *
 * El plan especial se revisa igual que los demás (`observacion`); lo que lo hace
 * distinto —que su campo de cabecera sea `justificacion` y su contenido sean las
 * `activities`— ya está en las columnas del catálogo.
 */
class EispecialkComponent extends EvaluacionDocumentComponent
{
    protected string $entidad = 'eispecialks';
}
