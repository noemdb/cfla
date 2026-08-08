<?php

namespace App\Services\Lms;

/**
 * Resultado de una operación de reparación de SVG (P1+P2+P5+recorte).
 *
 * DTO inmutable: el servicio lo crea internamente y lo retorna como
 * respuesta atómica de lo que pasó, sin necesidad de persistir nada extra.
 */
class SvgRepairResult
{
    /**
     * @param  string       $strategy    'ai' | 'deterministic' | 'unchanged' | 'no-svg' | 'error'
     * @param  string|null  $svg         Bloque SVG reparado (sin figure/div wrapper).
     * @param  string|null  $body        Body completo del contenido (figure + svg) reparado.
     * @param  string|null  $model       Modelo que respondió exitosamente (si strategy='ai').
     * @param  string|null  $error       Error descriptivo (si strategy='error').
     * @param  array        $changes     Resumen de cambios ['tag_roto', 'svg_reclosed', 'viewBox_cropped', ...].
     * @param  int|null     $contentId   ID del contenido reparado (si se usó repairContent).
     */
    public function __construct(
        public readonly string $strategy,
        public readonly ?string $svg = null,
        public readonly ?string $body = null,
        public readonly ?string $model = null,
        public readonly ?string $error = null,
        public readonly array $changes = [],
        public readonly ?int $contentId = null,
    ) {
        assert(in_array($strategy, ['ai', 'deterministic', 'unchanged', 'no-svg', 'error']));
    }

    public function isSuccess(): bool
    {
        return $this->strategy !== 'error' && $this->strategy !== 'no-svg';
    }

    public function wasRepaired(): bool
    {
        return in_array($this->strategy, ['ai', 'deterministic'])
            && ($this->svg !== null || $this->body !== null);
    }
}
