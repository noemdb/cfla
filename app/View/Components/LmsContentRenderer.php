<?php

namespace App\View\Components;

use App\Services\Lms\LmsTextSanitizerService;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Componente seguro para renderizar contenido LMS (Markdown / HTML).
 *
 * - Aplica la misma pre-sanitización que el pipeline de guardado
 *   cuando `$sanitize` está activo (wizard preview).
 * - Convierte Markdown → HTML.
 * - Incluye clases prose con bordes de tabla garantizados.
 *
 * Uso:
 *   <x-lms-content-renderer :body="$rawBody" />                      {{-- wizard: sanitiza automáticamente --}}
 *   <x-lms-content-renderer :body="$content->body" :sanitize="false" /> {{-- estudiante: ya viene sanitizado --}}
 */
class LmsContentRenderer extends Component
{
    /** El texto en crudo (Markdown o HTML). */
    public ?string $body;

    /**
     * Si debe pre-sanitizar (strip **bold**, espacios múltiples, etc.)
     * antes de convertir a HTML.  Activo por defecto (wizard preview);
     * desactivar cuando el contenido ya fue sanitizado (vista estudiante).
     */
    public bool $sanitize;

    /** Contenido procesado listo para imprimir con {!! !!}. */
    public string $rendered = '';

    public function __construct(?string $body = '', bool $sanitize = true)
    {
        $this->body = $body;
        $this->sanitize = $sanitize;
    }

    public function render(): View
    {
        $text = $this->body ?? '';

        if ($text === '') {
            $this->rendered = '';
        } else {
            if ($this->sanitize) {
                /** @var LmsTextSanitizerService $sanitizer */
                $sanitizer = app(LmsTextSanitizerService::class);
                $text = $sanitizer->sanitize($text, 'standard');
            }
            $this->rendered = $this->toHtml($text);
        }

        return view('components.lms-content-renderer');
    }

    /**
     * Convierte el texto a HTML.
     * Si contiene etiquetas HTML (<) se asume que ya es HTML y se pasa raw.
     * En caso contrario se convierte de Markdown a HTML.
     */
    private function toHtml(string $text): string
    {
        if (Str::contains($text, '<')) {
            return $text;
        }
        return Str::markdown($text);
    }
}
