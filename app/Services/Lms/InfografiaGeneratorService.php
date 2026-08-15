<?php

namespace App\Services\Lms;

use App\Services\KimiService;
use App\Services\NvidiaService;
use App\Services\OpenRouterService;
use Psr\Log\LoggerInterface;

/**
 * Servicio especializado para generar infografías jerárquicas (4-6 niveles)
 * utilizando servicios de IA con cadena de fallback.
 *
 * Responsabilidades:
 *   - Validar requestData contra esquema esperado
 *   - Enriquecer con contexto académico del profesor/actividad
 *   - Construir prompt especializado para infografías jerárquicas
 *   - Ejecutar cadena de fallback: OpenRouter → Nvidia → Kimi
 *   - Parsear y validar respuesta JSON
 *   - Aplicar post-procesamiento para asegurar límites de niveles (4-6)
 *   - Devolver estructura enriquecida con metadatos
 */
class InfografiaGeneratorService
{
    private const FALLBACK_REINFORCEMENT = <<<'TEXT'

⚠️ CORRECCIÓN — Intento anterior no siguió las instrucciones.

Reglas críticas:
1. Generar EXACTAMENTE el número de niveles solicitado (entre 4 y 6).
2. Cada nodo debe tener: etiqueta (máx 50 chars), descripción opcional (máx 150 chars).
3. Jerarquía clara: cada nodo (excepto raíz) tiene exactamente un padre.
4. No permitir huérfanos ni ciclos en la estructura.
5. Usar exclusivamente colores de la paleta SAEFL especificados.
6. Asegurar contraste mínimo WCAG AA (4.5:1) entre fondo y texto.
7. Salida debe ser JSON estrictamente válido que siga el esquema definido.
8. NO incluyas nada más que el JSON solicitado.
TEXT;

    public function __construct(
        private readonly OpenRouterService $openRouter,
        private readonly NvidiaService $nvidia,
        private readonly KimiService $kimi,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * Genera una estructura jerárquica para infografía basada en el request.
     *
     * @param  array  $requestData  Debe contener: niveles, tipo_estructura, direccion, tema_color, contexto_pedagogico, restricciones
     * @return array ['success' => bool, 'estructura' => array|null, 'error' => string|null, 'metadata' => array]
     */
    public function generate(array $requestData): array
    {
        // 1. Validar requestData contra esquema básico
        $validation = $this->validateRequest($requestData);
        if (! $validation['valid']) {
            return [
                'success' => false,
                'estructura' => null,
                'error' => $validation['error'],
                'metadata' => [],
            ];
        }

        // 2. Enriquecer con contexto académico del profesor/actividad
        $enrichedRequest = $this->enrichRequest($requestData);

        // 3. Construir prompt especializado para infografías jerárquicas
        $prompt = $this->buildInfografiaPrompt($enrichedRequest);

        // 4. Ejecutar cadena de fallback: OpenRouter → Nvidia → Kimi
        $result = $this->executeFallbackChain($prompt);

        if (! $result['success']) {
            return [
                'success' => false,
                'estructura' => null,
                'error' => $result['error'],
                'metadata' => $result['metadata'] ?? [],
            ];
        }

        // 5. Parsear y validar respuesta JSON
        $parsed = $this->parseAndValidateResponse($result['content']);
        if (! $parsed['valid']) {
            return [
                'success' => false,
                'estructura' => null,
                'error' => $parsed['error'],
                'metadata' => array_merge(
                    $result['metadata'] ?? [],
                    ['raw_response' => $result['content']]
                ),
            ];
        }

        // 6. Aplicar post-procesamiento para asegurar límites de niveles (4-6)
        $processed = $this->postProcessStructure($parsed['estructura'], $requestData['niveles']);

        // 7. Devolver estructura enriquecida con metadatos
        return [
            'success' => true,
            'estructura' => $processed,
            'error' => null,
            'metadata' => array_merge(
                $result['metadata'] ?? [],
                [
                    'tokens_utilizados' => $result['usage']['total_tokens'] ?? 0,
                    'modelo_usado' => $result['model'],
                    'tiempo_generacion' => $result['usage']['total_time'] ?? 0,
                    'niveles_finales' => $this->countLevels($processed),
                ]
            ),
        ];
    }

    /**
     * Valida el requestData contra el esquema esperado.
     */
    private function validateRequest(array $requestData): array
    {
        // Validar niveles
        if (! isset($requestData['niveles']) || ! is_int($requestData['niveles']) || $requestData['niveles'] < 4 || $requestData['niveles'] > 6) {
            return ['valid' => false, 'error' => 'El número de niveles debe ser un entero entre 4 y 6.'];
        }

        // Validar tipo_estructura
        $allowedTypes = ['jerarquica', 'radial', 'flujo', 'matriz'];
        if (! isset($requestData['tipo_estructura']) || ! in_array($requestData['tipo_estructura'], $allowedTypes)) {
            return ['valid' => false, 'error' => 'Tipo de estructura no válido. Debe ser: jerarquica, radial, flujo o matriz.'];
        }

        // Validar dirección (solo para jerarquica y flujo)
        $allowedDirections = ['top-down', 'left-right', 'radial'];
        if (in_array($requestData['tipo_estructura'], ['jerarquica', 'flujo']) &&
            (! isset($requestData['direccion']) || ! in_array($requestData['direccion'], $allowedDirections))) {
            return ['valid' => false, 'error' => 'Dirección no válida para el tipo de estructura seleccionado.'];
        }

        // Validar tema_color
        $allowedColors = ['emerald', 'sky', 'amber', 'purple', 'rose', 'stone'];
        if (! isset($requestData['tema_color']) || ! in_array($requestData['tema_color'], $allowedColors)) {
            return ['valid' => false, 'error' => 'Tema de color no válido. Debe ser uno de: emerald, sky, amber, purple, rose, stone.'];
        }

        // Validar contexto_pedagogico (opcional pero si existe debe tener ciertos campos)
        if (isset($requestData['contexto_pedagogico']) && ! is_array($requestData['contexto_pedagogico'])) {
            return ['valid' => false, 'error' => 'El contexto pedagógico debe ser un array.'];
        }

        // Validar restricciones (opcional)
        if (isset($requestData['restricciones']) && ! is_array($requestData['restricciones'])) {
            return ['valid' => false, 'error' => 'Las restricciones deben ser un array.'];
        }

        return ['valid' => true, 'error' => null];
    }

    /**
     * Enriquece el request con contexto académico del profesor/actividad.
     */
    private function enrichRequest(array $requestData): array
    {
        // Si ya tiene contexto_pedagogico, lo usamos; sino creamos uno básico
        $contexto = $requestData['contexto_pedagogico'] ?? [];

        // Agregar información por defecto si faltan campos esenciales
        if (empty($contexto['grado'])) {
            $contexto['grado'] = '5to básico';
        }
        if (empty($contexto['asignatura'])) {
            $contexto['asignatura'] = 'Ciencias Naturales';
        }
        if (empty($contexto['tema_leccion'])) {
            $contexto['tema_leccion'] = 'Tema general';
        }
        if (empty($contexto['indicadores_relacionados'])) {
            $contexto['indicadores_relacionados'] = [];
        }
        if (empty($contexto['referente_normativo'])) {
            $contexto['referente_normativo'] = 'CNBP - Área de Ciencias Naturales';
        }
        if (empty($contexto['contenido_actual'])) {
            $contexto['contenido_actual'] = '';
        }

        $requestData['contexto_pedagogico'] = $contexto;

        // Asegurar que restricciones tenga valores por defecto
        $restricciones = $requestData['restricciones'] ?? [];
        $restricciones = array_merge([
            'maximo_nodos_por_nivel' => 8,
            'maximo_total_nodos' => 30,
            'etiqueta_maxima_longitud' => 50,
            'incluir_iconos' => true,
            'incluir_tooltips' => true,
        ], $restricciones);
        $requestData['restricciones'] = $restricciones;

        return $requestData;
    }

    /**
     * Construye el prompt especializado para infografías jerárquicas.
     */
    private function buildInfografiaPrompt(array $requestData): string
    {
        $niveles = $requestData['niveles'];
        $tipoEstructura = $requestData['tipo_estructura'];
        $direccion = $requestData['direccion'] ?? 'top-down';
        $temaColor = $requestData['tema_color'];
        $contexto = $requestData['contexto_pedagogico'];
        $restricciones = $requestData['restricciones'];

        $iconosEducativos = ['book', 'lightbulb', 'microscope', 'globe', 'calculator', 'atom', 'leaf',
            'gear', 'paint-brush', 'music-note', 'soccer-ball', 'heart', 'shield',
            'magnet', 'flask', 'binoculars', 'compass', 'ruler'];

        $paletaColores = $this->getSaeflPalette($temaColor);

        $systemPrompt = <<<'PROMPT'
Eres un diseñador instruccional especializado en infografías educativas para el sistema escolar venezolano.
Genera una estructura jerárquica de EXACTAMENTE {{niveles}} niveles (entre 4 y 6) para el tema "{{tema_leccion}}".

RESTRICCIONES OBLIGATORIAS:
- Mínimo 4 niveles, máximo 6 niveles
- Máximo {{maximo_nodos_por_nivel}} nodos por nivel para evitar sobrecarga visual
- Cada nodo debe tener: etiqueta (máx {{etiqueta_maxima_longitud}} chars), descripción opcional (máx 150 chars)
- Jerarquía clara: cada nodo (excepto raíz) tiene exactamente un padre
- No permitir huérfanos ni ciclos en la estructura
- Usar exclusivamente colores de la paleta SAEFL proporcionada para {{tema_color}}
- Asegurar contraste mínimo WCAG AA (4.5:1) entre fondo y texto de cada nodo
- Sugerir iconos del conjunto predefinido cuando sea apropiado

FORMATO DE SALIDA (JSON ESTRICTO):
{
  "estructura": {
    "tipo": "{{tipo_estructura}}",
    "niveles": <entero 4-6>,
    "nodo_raiz": {
      "id": "string único",
      "etiqueta": "string (máx {{etiqueta_maxima_longitud}})",
      "descripcion": "string opcional (máx 150)",
      "color_fondo": "hex color válido de la paleta {{tema_color}}",
      "color_texto": "hex color válido de la paleta {{tema_color}} (con contraste ≥4.5:1)",
      "icono_sugerido": "string del conjunto predefinido o null",
      "hijos": [ /* arreglo de nodos del siguiente nivel */ ]
    }
  }
}

CONJUNTO PREDEFINIDO DE ÍCONOS EDUCATIVOS SIMPLES:
['book', 'lightbulb', 'microscope', 'globe', 'calculator', 'atom', 'leaf',
 'gear', 'paint-brush', 'music-note', 'soccer-ball', 'heart', 'shield',
 'magnet', 'flask', 'binoculars', 'compass', 'ruler']

PALETA DE COLORES SAEFL PARA {{tema_color}} (usar exclusivamente estos valores):
{{paleta_colores}}

CONTEXTO PEDAGÓGICO:
**Grado:** {{grado}}
**Asignatura:** {{asignatura}}
**Tema de la lección:** {{tema_leccion}}
**Indicadores relacionados:** {{indicadores_relacionados}}
**Referente normativo:** {{referente_normativo}}
**Contenido actual (para inspiración):** {{contenido_actual}}

INSTRUCCIONES ADICIONALES:
- Prioriza conceptos clave y relaciones causales sobre enumeraciones simples
- Usa vocabulario acorde al grado especificado
- Asegura progresión lógica de conceptos (de general a específico o viceversa según tipo de estructura)
- Incluye al menos una relación de causa-efecto o proceso en cada nivel intermedio
- El nodo raíz debe representar el concepto central del tema
- Los niveles deben mostrar una progresión pedagógica clara
PROMPT;

        // Reemplazar placeholders
        $systemPrompt = str_replace('{{niveles}}', $niveles, $systemPrompt);
        $systemPrompt = str_replace('{{tipo_estructura}}', $tipoEstructura, $systemPrompt);
        $systemPrompt = str_replace('{{maximo_nodos_por_nivel}}', $restricciones['maximo_nodos_por_nivel'], $systemPrompt);
        $systemPrompt = str_replace('{{etiqueta_maxima_longitud}}', $restricciones['etiqueta_maxima_longitud'], $systemPrompt);
        $systemPrompt = str_replace('{{tema_color}}', $temaColor, $systemPrompt);
        $systemPrompt = str_replace('{{grado}}', $contexto['grado'], $systemPrompt);
        $systemPrompt = str_replace('{{asignatura}}', $contexto['asignatura'], $systemPrompt);
        $systemPrompt = str_replace('{{tema_leccion}}', $contexto['tema_leccion'], $systemPrompt);
        $systemPrompt = str_replace('{{indicadores_relacionados}}', implode(', ', $contexto['indicadores_relacionados']), $systemPrompt);
        $systemPrompt = str_replace('{{referente_normativo}}', $contexto['referente_normativo'], $systemPrompt);
        $systemPrompt = str_replace('{{contenido_actual}}', $contexto['contenido_actual'], $systemPrompt);
        $systemPrompt = str_replace('{{paleta_colores}}', $this->formatColorPalette($paletaColores), $systemPrompt);

        $userPrompt = <<<'PROMPT'
Genera la estructura jerárquica según las especificaciones anteriores.
RESPONDE ÚNICAMENTE CON EL JSON, SIN TEXTO ADICIONAL.
PROMPT;

        return $systemPrompt."\n\n".$userPrompt;
    }

    /**
     * Obtiene la paleta de colores SAEFL para un tema dado.
     */
    private function getSaeflPalette(string $tema): array
    {
        $palettes = [
            'emerald' => ['#f0fdf4', '#dcfce7', '#bbf7d0', '#86efac', '#4ade80', '#10b981', '#059669', '#047857', '#064e3b', '#065f46'],
            'sky' => ['#f0f9ff', '#e0f2fe', '#bae6fd', '#7dd3fc', '#38bdf8', '#0ea5e9', '#0284c7', '#0369a1', '#075985', '#0c4a6e'],
            'amber' => ['#fffbeb', '#fef3c7', '#fde68a', '#fbbf24', '#facc15', '#f59e0b', '#d97706', '#b45309', '#92400e', '#78350f'],
            'purple' => ['#faf5ff', '#f3e8ff', '#e9d5ff', '#d8b4fe', '#c084fc', '#8b5cf6', '#7c3aed', '#6d28d9', '#5b21b6', '#4c1d95'],
            'rose' => ['#fff1f2', '#ffe4e6', '#fecdd3', '#fda4af', '#fb7185', '#f43f5e', '#e11d48', '#be123c', '#9f1239', '#881337'],
            'stone' => ['#fafaf9', '#f5f5f4', '#e7e5e4', '#d6d3d1', '#a8a29e', '#78716c', '#57534e', '#44403c', '#292524', '#1c1917'],
        ];

        return $palettes[$tema] ?? $palettes['emerald'];
    }

    /**
     * Formatea la palette de colores para incluir en el prompt.
     */
    private function formatColorPalette(array $palette): string
    {
        // Degradado de 5 niveles: 50, 100, 200, 500, 900
        return "- 50: {$palette[0]}\n- 100: {$palette[1]}\n- 200: {$palette[2]}\n- 500: {$palette[5]}\n- 900: {$palette[9]}";
    }

    /**
     * Ejecuta la cadena de fallback: OpenRouter → Nvidia → Kimi.
     */
    private function executeFallbackChain(string $prompt): array
    {
        // Intentos: OpenRouter (primario) → Nvidia (fallback 1) → Kimi (fallback 2)
        $attempts = [
            [
                'service' => $this->openRouter,
                'label' => 'OpenRouter primario',
                'params' => ['model' => config('openrouter.model_primary'), 'temperature' => 0.3, 'max_tokens' => 2000],
            ],
            [
                'service' => $this->nvidia,
                'label' => 'Nvidia fallback 1',
                'params' => ['model' => config('openrouter.model_fallback1'), 'temperature' => 0.3, 'max_tokens' => 2000],
            ],
            [
                'service' => $this->kimi,
                'label' => 'Kimi fallback 2',
                'params' => ['model' => config('openrouter.model_fallback2'), 'temperature' => 0.3, 'max_tokens' => 2000],
            ],
        ];

        foreach ($attempts as $attempt) {
            $this->logger->info("Intentando generar infografía con {$attempt['label']}");

            try {
                $result = $attempt['service']->ask(
                    'Eres un diseñador instruccional especializado en infografías educativas. Genera únicamente el JSON solicitado.',
                    $prompt,
                    $attempt['params']
                );

                if ($result['success'] && ! empty($result['content'])) {
                    $this->logger->info("Éxito con {$attempt['label']}");

                    return [
                        'success' => true,
                        'content' => $result['content'],
                        'model' => $attempt['params']['model'],
                        'usage' => $result['usage'] ?? [],
                    ];
                }
            } catch (\Throwable $e) {
                $this->logger->warning("Error con {$attempt['label']}: {$e->getMessage()}");

                continue;
            }
        }

        return [
            'success' => false,
            'error' => 'Todos los servicios de IA fallaron al generar la infografía.',
            'metadata' => [],
        ];
    }

    /**
     * Parsea y valida la respuesta JSON del servicio de IA.
     */
    private function parseAndValidateResponse(string $content): array
    {
        // Limpiar posibles wrappers
        $content = trim($content);
        if (preg_match('/^```(?:json)?\s*$/i', $content)) {
            $content = '';
        }
        $content = preg_replace('/^```(?:json)?\s*\n/i', '', $content);
        $content = preg_replace('/\n?```\s*$/s', '', $content);
        $content = trim($content);

        // Intentar parsear JSON
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'valid' => false,
                'error' => 'Respuesta no es JSON válido: '.json_last_error_msg(),
            ];
        }

        // Validar estructura básica
        if (! isset($data['estructura']) || ! is_array($data['estructura'])) {
            return [
                'valid' => false,
                'error' => 'Falta el campo "estructura" o no es un array.',
            ];
        }

        $estructura = $data['estructura'];
        if (! isset($estructura['tipo']) || ! isset($estructura['niveles']) || ! isset($estructura['nodo_raiz'])) {
            return [
                'valid' => false,
                'error' => 'La estructura debe tener "tipo", "niveles" y "nodo_raiz".',
            ];
        }

        // Validar niveles
        $niveles = $estructura['niveles'];
        if (! is_int($niveles) || $niveles < 4 || $niveles > 6) {
            return [
                'valid' => false,
                'error' => 'El número de niveles debe estar entre 4 y 6.',
            ];
        }

        // Validar nodo raíz recursivamente
        $nodoRaizValidation = $this->validateNode($estructura['nodo_raiz'], 1, $niveles);
        if (! $nodoRaizValidation['valid']) {
            return [
                'valid' => false,
                'error' => 'Error en nodo raíz: '.$nodoRaizValidation['error'],
            ];
        }

        return [
            'valid' => true,
            'estructura' => $estructura,
        ];
    }

    /**
     * Valida un nodo y sus hijos recursivamente.
     */
    private function validateNode(array $node, int $level, int $maxLevels): array
    {
        // Validar campos obligatorios
        if (! isset($node['id']) || ! is_string($node['id'])) {
            return ['valid' => false, 'error' => 'El nodo debe tener un "id" string.'];
        }
        if (! isset($node['etiqueta']) || ! is_string($node['etiqueta']) || mb_strlen($node['etiqueta']) > 50) {
            return ['valid' => false, 'error' => 'La etiqueta debe ser un string de máximo 50 caracteres.'];
        }
        if (isset($node['descripcion']) && (! is_string($node['descripcion']) || mb_strlen($node['descripcion']) > 150)) {
            return ['valid' => false, 'error' => 'La descripción debe ser un string de máximo 150 caracteres.'];
        }
        // Validar colores (deben ser hex y de la paleta, pero aquí solo verificamos formato hex)
        if (isset($node['color_fondo']) && ! preg_match('/^#[0-9A-Fa-f]{6}$/', $node['color_fondo'])) {
            return ['valid' => false, 'error' => 'El color de fondo debe ser un hex válido (#RRGGBB).'];
        }
        if (isset($node['color_texto']) && ! preg_match('/^#[0-9A-Fa-f]{6}$/', $node['color_texto'])) {
            return ['valid' => false, 'error' => 'El color de texto debe ser un hex válido (#RRGGBB).'];
        }
        // Validar icono (opcional, debe ser del conjunto predefinido o null)
        if (isset($node['icono_sugerido']) && $node['icono_sugerido'] !== null && ! is_string($node['icono_sugerido'])) {
            return ['valid' => false, 'error' => 'El icono sugerido debe ser string o null.'];
        }

        // Validar hijos si existen y no estamos en el último nivel
        if (isset($node['hijos']) && is_array($node['hijos'])) {
            if ($level >= $maxLevels) {
                return ['valid' => false, 'error' => 'No se permiten hijos en el nivel máximo ('.$maxLevels.').'];
            }
            foreach ($node['hijos'] as $hijo) {
                if (! is_array($hijo)) {
                    return ['valid' => false, 'error' => 'Cada hijo debe ser un array.'];
                }
                $hijoValidation = $this->validateNode($hijo, $level + 1, $maxLevels);
                if (! $hijoValidation['valid']) {
                    return $hijoValidation;
                }
            }
        } elseif ($level < $maxLevels) {
            // Si no hay hijos y no estamos en el último nivel, es aceptable (nodo hoja antes del nivel máximo)
            // Pero preferimos que haya al menos algún hijo en niveles intermedios para evitar estructura plana
            // Esto se manejará en post-procesamiento si es necesario
        }

        return ['valid' => true, 'error' => null];
    }

    /**
     * Aplica post-procesamiento para asegurar que la estructura tenga exactamente el número de niveles solicitado.
     */
    private function postProcessStructure(array $estructura, int $nivelesSolicitados): array
    {
        $nivelesActuales = $this->countLevels($estructura);

        if ($nivelesActuales === $nivelesSolicitados) {
            return $estructura; // Ya está correcto
        }

        if ($nivelesActuales < $nivelesSolicitados) {
            // Necesitamos expandir: dividir el nivel más profundo
            return $this->expandLevels($estructura, $nivelesSolicitados - $nivelesActuales);
        }

        // $nivelesActuales > $nivelesSolicitados: necesitamos colapsar niveles excesivos
        return $this->collapseLevels($estructura, $nivelesActuales - $nivelesSolicitados);
    }

    /**
     * Cuenta el número máximo de niveles en la estructura.
     */
    private function countLevels(array $estructura, int $currentLevel = 1): int
    {
        $maxLevel = $currentLevel;
        if (isset($estructura['nodo_raiz']['hijos']) && is_array($estructura['nodo_raiz']['hijos'])) {
            foreach ($estructura['nodo_raiz']['hijos'] as $hijo) {
                $hijoLevel = $this->countLevels(['nodo_raiz' => $hijo], $currentLevel + 1);
                if ($hijoLevel > $maxLevel) {
                    $maxLevel = $hijoLevel;
                }
            }
        }

        return $maxLevel;
    }

    /**
     * Expande la estructura añadiendo niveles dividiendo nodos hoja.
     */

    /**
     * Expande la estructura añadiendo niveles dividiendo nodos hoja.
     */
    private function expandLevels(array $estructura, int $nivelesAAadir): array
    {
        if ($nivelesAAadir <= 0 || empty($estructura['nodo_raiz'])) {
            return $estructura;
        }

        // Estrategia simple: añadir niveles hoja duplicando y modificando ligeramente
        // Esto es una implementación básica - en producción se haría algo más sofisticado
        $resultado = $estructura;

        for ($i = 0; $i < $nivelesAAadir; $i++) {
            // Encontrar nodos hoja y añadir un hijo a cada uno
            $resultado = $this->addLevelToLeaves($resultado);
        }

        return $resultado;
    }

    /**
     * Colapsa la estructura eliminando niveles excesivos.
     */
    private function collapseLevels(array $estructura, int $nivelesAEliminar): array
    {
        if ($nivelesAEliminar <= 0 || empty($estructura['nodo_raiz'])) {
            return $estructura;
        }

        // Estrategia simple: fusionar niveles desde el más profundo hacia arriba
        $resultado = $estructura;

        for ($i = 0; $i < $nivelesAEliminar; $i++) {
            $resultado = $this->mergeDeepestLevel($resultado);
        }

        return $resultado;
    }

    /**
     * Añade un nivel hoja a todas las hojas del árbol.
     */
    private function addLevelToLeaves(array $estructura): array
    {
        if (empty($estructura['nodo_raiz'])) {
            return $estructura;
        }

        // Clonar la estructura para no modificar la original
        $resultado = $estructura;

        // Encontrar todas las hojas y añadir un hijo a cada una
        $hojas = $this->findLeafNodes($resultado['nodo_raiz']);

        foreach ($hojas as &$hoja) {
            // Añadir un nodo hijo hoja
            $hoja['hijos'][] = [
                'id' => 'temp_'.uniqid(),
                'etiqueta' => 'Detalle',
                'descripcion' => 'Información adicional',
                'color_fondo' => $hoja['color_fondo'] ?? '#10b981',
                'color_texto' => $hoja['color_texto'] ?? '#ffffff',
                'icono_sugerido' => $hoja['icono_sugerido'] ?? 'book',
                'hijos' => [],
            ];
        }

        return $resultado;
    }

    /**
     * Fusiona el nivel más profundo moviendo sus hijos al nivel superior.
     */
    private function mergeDeepestLevel(array $estructura): array
    {
        if (empty($estructura['nodo_raiz'])) {
            return $estructura;
        }

        $nivelMaximo = $this->countLevels($estructura);

        if ($nivelMaximo <= 1) {
            return $estructura; // Ya no se puede colapsar más
        }

        // Encontrar nodos en el nivel más profundo y su padres
        $nodosProfundosConPadres = $this->findDeepestNodesWithParents($estructura['nodo_raiz']);

        foreach ($nodosProfundosConPadres as $nodoInfo) {
            $nodoProfundo = $nodoInfo['nodo'];
            $padre = $nodoInfo['padre'];

            // Mover los hijos del nodo profundo al padre
            if (! empty($nodoProfundo['hijos']) && is_array($nodoProfundo['hijos'])) {
                foreach ($nodoProfundo['hijos'] as $hijo) {
                    $padre['hijos'][] = $hijo;
                }

                // Eliminar el nodo profundo de los hijos del padre
                $padre['hijos'] = array_filter($padre['hijos'], function ($h) use ($nodoProfundo) {
                    return ! isset($h['id']) || $h['id'] !== $nodoProfundo['id'];
                });

                // Reindexar el array
                $padre['hijos'] = array_values($padre['hijos']);
            }
        }

        return $estructura;
    }

    /**
     * Encuentra todas las hojas en el árbol y devuelve su información incluyendo padre.
     */
    private function findLeafNodes(array $nodo, array $padres = []): array
    {
        $hojas = [];

        // Si es hoja
        if (empty($nodo['hijos']) || ! is_array($nodo['hijos'])) {
            $hojas[] = [
                'nodo' => $nodo,
                'padre' => end($padres) ?: null,
            ];
        } else {
            // Continuar recursivamente
            $nuevosPadres = array_merge($padres, [$nodo]);
            foreach ($nodo['hijos'] as $hijo) {
                $hojas = array_merge($hojas, $this->findLeafNodes($hijo, $nuevosPadres));
            }
        }

        return $hojas;
    }

    /**
     * Encuentra los nodos en el nivel más profundo y devuelve su información con padre.
     */
    private function findDeepestNodesWithParents(array $nodo, array $padres = [], int $nivelActual = 1): array
    {
        $resultado = [];
        $nivelMaximo = $this->countLevels(['nodo_raiz' => $nodo]);

        // Si llegamos al nivel más profundo
        if ($nivelActual === $nivelMaximo) {
            $resultado[] = [
                'nodo' => $nodo,
                'padre' => end($padres) ?: null,
            ];
        } else {
            // Continuar recursivamente
            $nuevosPadres = array_merge($padres, [$nodo]);
            if (! empty($nodo['hijos']) && is_array($nodo['hijos'])) {
                foreach ($nodo['hijos'] as $hijo) {
                    $resultado = array_merge($resultado, $this->findDeepestNodesWithParents($hijo, $nuevosPadres, $nivelActual + 1));
                }
            }
        }

        return $resultado;
    }
}
