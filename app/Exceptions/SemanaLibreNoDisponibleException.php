<?php

namespace App\Exceptions;

/**
 * No hay una semana libre para colocar la copia de un plan.
 *
 * Es una excepción de dominio: el componente la muestra como notificación,
 * mientras que los errores de base de datos siguen el camino genérico y se
 * reportan sin exponer SQL al docente.
 */
class SemanaLibreNoDisponibleException extends \RuntimeException {}
