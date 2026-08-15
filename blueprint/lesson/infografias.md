# Sistema de Generación de Infografías Jerárquicas (4-6 Niveles)

## Resumen Ejecutivo
Este documento especifica la implementación de una funcionalidad para generar infografías jerárquicas de 4 a 6 niveles de profundidad utilizando exclusivamente HTML, SVG y Tailwind CSS, impulsada por servicios de inteligencia artificial. La funcionalidad se integrará en el componente `LessonWizard` mediante un botón "Generar Imagen" que permitirá a los profesores crear infografías educativas estructuradas y visualmente atractivas para sus lecciones LMS.

## Objetivos
- Permitir a los profesores generar infografías complejas (4-6 niveles) con mínimo esfuerzo
- Garantizar que las infografías cumplan con estándares de accesibilidad y diseño educativo
- Utilizar exclusivamente tecnologías web estándar (HTML/SVG/Tailwind) para garantizar compatibilidad y rendimiento
- Aprovechar los servicios de IA existentes para generar contenido estructurado y visualmente coherente
- Integrarse de forma fluida en el flujo existente de creación de lecciones

## Alcance
### Incluido
- Generación de infografías con 4 a 6 niveles de profundidad jerárquica
- Uso exclusivo de HTML, SVG inline y clases de Tailwind CSS
- Integración con el botón existente `generateSlideImage` en LessonWizard
- Servicios de IA para generar estructura de datos y sugerencias de contenido
- Vista previa interactiva antes de insertar en el contenido
- Opciones básicas de personalización (tema de color, dirección del flujo)
- Exportación como SVG inline listo para usar en el editor

### Excluido
- Edición avanzada de vectores dentro de la aplicación
- Generación de raster images (PNG/JPEG) - solo SVG vectorial
- Animaciones o interactividad compleja más allá de hover/tooltips básicos
- Integración con bibliotecas externas de diagrama (Mermaid, etc.) - puro HTML/SVG/Tailwind
- Almacenamiento persistente de plantillas de infografía (se generan bajo demanda)

## Arquitectura Técnica

### Flujo de Trabajo
1. Profesor hace clic en el botón "Generar Imagen" en el editor de contenido
2. Se muestra un modal de configuración para definir:
   - Número de niveles (4-6)
   - Tipo de estructura (jerárquica, radial, flujo, matriz)
   - Tema de color (basado en paleta SAEFL)
   - Dirección del flujo (top-down, left-right, radial)
   - Tema/contenido base (opcional, tomado del contexto actual)
3. Al confirmar, se envía una solicitud al servicio de IA con el contexto pedagógico
4. El servicio de IA devuelve una estructura JSON jerárquica optimizada para visualización
5. Un servicio de renderizado convierte la estructura a HTML/SVG/Tailwind
6. Se muestra una vista previa interactiva en un modal
7. El profesor puede aceptar (inserta el SVG inline en el editor) o rechazar/modificar
8. Al aceptar, el SVG se inserta como contenido de tipo `HTML_EMBED` en la sección actual

### Modelo de Datos de Entrada para IA
```json
{
  "niveles": 4, // entero entre 4 y 6
  "tipo_estructura": "jerarquica", // jerarquica, radial, flujo, matriz
  "direccion": "top-down", // top-down, left-right, radial
  "tema_color": "emerald", // basado en paleta SAEFL: emerald, sky, amber, purple, rose
  "contexto_pedagogico": {
    "grado": "5to básico",
    "asignatura": "Ciencias Naturales",
    "tema_leccion": "El ciclo del agua",
    "indicadores_relacionados": ["CA.5.1.1.", "CA.5.1.2."],
    "referente_normativo": "CNBP - Área de Ciencias Naturales",
    "contenido_actual": "Texto o contenido existente en la sección donde se insertará"
  },
  "restricciones": {
    "maximo_nodos_por_nivel": 8,
    "maximo_total_nodos": 30,
    "etiqueta_maxima_longitud": 50,
    "incluir_iconos": true,
    "incluir_tooltips": true
  }
}
```

### Modelo de Salida Esperado de IA
```json
{
  "estructura": {
    "tipo": "jerarquica",
    "niveles": 5,
    "nodo_raiz": {
      "id": "nivel1_1",
      "etiqueta": "Ciclo del Agua",
      "descripcion": "Proceso continuo de movimiento del agua en la Tierra",
      "color_fondo": "#10b981", // emerald-500
      "color_texto": "#ffffff",
      "icono_sugerido": "water",
      "hijos": [
        {
          "id": "nivel2_1",
          "etiqueta": "Evaporación",
          "descripcion": "Transformación de agua líquida a vapor",
          "color_fondo": "#3b82f6", // blue-500
          "color_texto": "#ffffff",
          "icono_sugerido": "cloud",
          "hijos": [
            {
              "id": "nivel3_1",
              "etiqueta": "Radiación solar",
              "descripcion": "Energía que impulsa la evaporación",
              // ... continúa hasta nivel 5
            }
          ]
        }
        // ... más nodos por nivel
      ]
    }
  },
  "metadatos": {
    "version": "1.0",
    "generado_por": "saefl-infografia-v1",
    "timestamp": "2026-08-14T10:30:00Z",
    "tokens_utilizados": 1245,
    "modelo_usado": "primary"
  }
}
```

## Servicios de IA Involucrados

### Servicio Principal: InfografiaGeneratorService
```
namespace App\Services\Lms;

class InfografiaGeneratorService
{
    public function generate(
        array $requestData
    ): array {
        // 1. Validar requestData contra esquema
        // 2. Enriquecer con contexto académico del profesor/actividad
        // 3. Construir prompt especializado para infografías jerárquicas
        // 4. Ejecutar cadena de fallback: OpenRouter → Nvidia → Kimi
        // 5. Parsear y validar respuesta JSON
        // 6. Aplicar post-procesamiento para asegurar límites de niveles (4-6)
        // 7. Devolver estructura enriquecida con metadatos
        
        return [
            'success' => bool,
            'estructura' => array, // JSON válido de estructura jerárquica
            'error' => string|null,
            'metadata' => array // tokens, modelo, tiempo, etc.
        ];
    }
}
```

### Especialización de Prompt para Infografías
El prompt debe incluir:
- Restricciones estrictas de 4-6 niveles
- Directrices para equilibrio visual (máximo 8 nodos por nivel recomendado)
- Sugerencias de iconografía basada en el contexto educativo (usar conjunto predefinido de íconos educativos simples)
- Paletas de color basadas en la paleta SAEFL (emerald, sky, amber, purple, rose, stone)
- Directrices de accesibilidad: contraste mínimo 4.5:1, tamaños de toque mínimos
- Estructura de salida JSON estrictamente definida
- Ejemplos de estructuras válidas para pocos-shot learning

Ejemplo de fragmento de prompt:
```
Eres un diseñador instruccional especializado en infografías educativas para el sistema escolar venezolano.
Genera una estructura jerárquica de EXACTAMENTE {{niveles}} niveles (entre 4 y 6) para el tema "{{contenido}}".

RESTRICCIONES OBLIGATORIAS:
- Mínimo 4 niveles, máximo 6 niveles
- Máximo 8 nodos por nivel para evitar sobrecarga visual
- Cada nodo debe tener: etiqueta (máx 50 chars), descripción opcional (máx 150 chars)
- Jerarquía clara: cada nodo (excepto raíz) tiene exactamente un padre
- No permitir huérfanos ni ciclos en la estructura

FORMATO DE SALIDA (JSON ESTRICTO):
{
  "estructura": {
    "tipo": "jerarquica",
    "niveles": <entero 4-6>,
    "nodo_raiz": {
      "id": "string único",
      "etiqueta": "string (máx 50)",
      "descripcion": "string opcional (máx 150)",
      "color_fondo": "hex color válido",
      "color_texto": "hex color válido (con contraste ≥4.5:1)",
      "icono_sugerido": "string de conjunto predefinido",
      "hijos": [ /* arreglo de nodos del siguiente nivel */ ]
    }
  }
}

CONJUNTO PREDEFINIDO DE ÍCONOS EDUCATIVOS SIMPLES:
['book', 'lightbulb', 'microscope', 'globe', 'calculator', 'atom', 'leaf', 
 'gear', 'paint-brush', 'music-note', 'soccer-ball', 'heart', 'shield', 
 'magnet', 'flask', 'binoculars', 'compass', 'ruler']

PALETA DE COLORES SAEFL (usar exclusivamente estos valores):
- emerald: #10b981, #059669, #047857, #064e3b, #065f46
- sky: #0ea5e9, #0284c7, #0369a1, #075985, #0c4a6e
- amber: #f59e0b, #d97706, #b45309, #92400e, #78350f
- purple: #8b5cf6, #7c3aed, #6d28d9, #5b21b6, #4c1d95
- rose: #f43f5e, #e11d48, #be123c, #9f1239, #881337
- stone: #78716c, #57534e, #44403c, #292524, #1c1917

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
```

### Cadena de Fallback y Optimización
1. **Intentos**: OpenRouter (primario) → Nvidia (fallback 1) → Kimi (fallback 2)
2. **Parámetros optimizados**:
   - temperature: 0.3 (para mayor determinismo en estructuras)
   - max_tokens: 2000 para OpenRouter; para Nvidia/Kimi usar su `max_tokens` de config (4000-6000) — los modelos de razonamiento de Nvidia (nemotron-*) agotan el presupuesto razonando y no alcanzan a emitir el JSON si el límite es bajo
   - timeout: 30 segundos (NVIDIA_TIMEOUT=10 es insuficiente; usar ≥60)
3. **Validación de respuesta**:
   - Verificar que sea JSON válido (se extrae el bloque JSON de respuestas con texto de razonamiento pre-pendido mediante `extractJsonBlock`)
   - Validar que `estructura.niveles` esté entre 4 y 6
   - La validación de profundidad del nodo raíz usa un tope permisivo (8 niveles); el exceso se colapsa en post-procesamiento
   - Verificar que no haya nodos huérfanos o ciclos
   - Asegurar que todas las etiquetas tengan longitud ≤ 50
   - Verificar contraste de colores usando algoritmo WCAG
4. **Post-procesamiento**:
   - Si hay más de 6 niveles: colapsar niveles excesivos en el nivel 6
   - Si hay menos de 4 niveles: expandir el nivel más pequeño mediante división lógica
   - Si hay más de 8 nodos por nivel: aplicar clustering semántico básico
   - Ajustar colores para asegurar contraste mínimo

> **Nota de operación (2026-08-15):** la cuenta de OpenRouter está sin créditos (HTTP 402 "can only afford 94"). El fallback funcional real es **Nvidia** con modelo `openai/gpt-oss-20b` (responde JSON limpio sin texto de razonamiento; `nvidia/nemotron-3-nano-30b-a3b` y `nemotron-3-super-120b-a12b` sí funcionan pero anteponen razonamiento, soportado por `extractJsonBlock`). Kimi devuelve HTTP 401 (clave inválida) — corregir `KIMI_API_KEY` para reactivar ese fallback.

## Integración con LessonWizard

### Modificaciones al Componente (especificación, no implementación)
#### Estado Adicional
```php
public bool $showInfografiaModal = false;
public ?array $infografiaConfig = null;
public ?string $infografiaPreviewSvg = null;
public ?string $infografiaError = null;
public bool $generatingInfografia = false;
```

#### Métodos Nuevos
1. `openInfografiaModal()` - Abre modal de configuración
2. `closeInfografiaModal()` - Cierra modal y limpia estado
3. `generateInfografia()` - Llama al servicio de IA con la configuración
4. `validateInfografiaResponse()` - Valida y post-procesa respuesta de IA
5. `renderInfografiaToSvg()` - Convierte estructura JSON a SVG/Tailwind HTML
6. `insertInfografiaEnEditor()` - Inserta el SVG como bloque HTML_EMBED

#### Hooks de Livewire
- Actualizar `updating()` para detectar cambios en configuración de infografía
- Agregar reglas de validación para los campos del modal

### Modal de Configuración
```
<x-dialog wire:model="showInfografiaModal">
    <x-slot name="title">
        Generar Infografía Jerárquica
    </x-slot>
    <x-slot name="content">
        <form wire:submit.prevent="generateInfografia">
            <div class="space-y-4">
                <!-- Selector de niveles -->
                <div>
                    <x-label for="niveles" :value="__('Niveles de profundidad')" />
                    <select wire:model="infografiaConfig.niveles" id="niveles"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                        <option value="4">4 niveles</option>
                        <option value="5">5 niveles (recomendado)</option>
                        <option value="6">6 niveles</option>
                    </select>
                </div>
                
                <!-- Selector de tipo de estructura -->
                <div>
                    <x-label for="tipo_estructura" :value="__('Tipo de estructura')" />
                    <select wire:model="infografiaConfig.tipo_estructura" id="tipo_estructura"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                        <option value="jerarquica">Jerárquica (árbol)</option>
                        <option value="radial">Radial (círculo central)</option>
                        <option value="flujo">Flujo de proceso</option>
                        <option value="matriz">Matriz de clasificación</option>
                    </select>
                </div>
                
                <!-- Selector de dirección -->
                <div x-show="infografiaConfig.tipo_estructura === 'jerarquica' || infografiaConfig.tipo_estructura === 'flujo'">
                    <x-label for="direccion" :value="__('Dirección')"/>
                    <select wire:model="infografiaConfig.direccion" id="direccion"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                        <option value="top-down">Top-down (vertical)</option>
                        <option value="left-right">Left-right (horizontal)</option>
                    </select>
                </div>
                
                <!-- Selector de tema de color -->
                <div>
                    <x-label for="tema_color" :value="__('Tema de color')"/>
                    <div class="mt-1 flex gap-2 flex-wrap">
                        @foreach(['emerald', 'sky', 'amber', 'purple', 'rose', 'stone'] as $tema)
                            <button type="button"
                                    wire:click="setTemaColor('{{ $tema }}')"
                                    :class="[infografiaConfig.tema_color === '{{ $tema }}' ? 'ring-2 ring-emerald-500' : '', 'w-10 h-10 rounded-md flex items-center justify-center']"
                                    style="background-color: {{ getColorHex($tema, 500) }};">
                                <svg class="w-5 h-5 text-white" ...>[@icon]</svg>
                            </button>
                        @endforeach
                    </div>
                </div>
                
                <!-- Campo opcional de tema/contenido base -->
                <div>
                    <x-label for="contenido_base" :value="__('Tema base (opcional)')"/>
                    <input wire:model.debounce="infografiaConfig.contenido_base"
                           type="text" id="contenido_base"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                           placeholder="Ej: Ciclo del agua, Fotosíntesis, etc.">
                </div>
                
                <!-- Botones de acción -->
                <div class="flex justify-end space-x-3 pt-4">
                    <x-button wire:click="closeInfografiaModal">
                        Cancelar
                    </x-button>
                    <x-button wire:click="generateInfografia"
                              wire:loading.attr="disabled"
                              wire:target="generateInfografia"
                              class="bg-emerald-600 text-white hover:bg-emerald-700">
                        Generar Infografía
                    </x-button>
                </div>
            </div>
        </form>
    </x-slot>
</x-dialog>
```

### Modal de Vista Previa
```
<x-dialog wire:model="showInfografiaPreview">
    <x-slot name="title">
        Vista Previa de Infografía
    </x-slot>
    <x-slot name="content">
        <div class="max-w-4xl mx-auto">
            <div class="border rounded-lg overflow-hidden">
                <!-- Aquí se insertará el SVG generado -->
                <div wire:self-ignore>{{ $infografiaPreviewSvg }}</div>
            </div>
            
            <div class="mt-4 space-x-3">
                <x-button wire:click="insertInfografiaEnEditor"
                          wire:loading.attr="disabled"
                          wire:target="insertInfografiaEnEditor"
                          class="bg-emerald-600 text-white hover:bg-emerald-700">
                    Insertar en lección
                </x-button>
                <x-button wire:click="closeInfografiaPreview"
                          class="bg-gray-300 text-gray-800 hover:bg-gray-400">
                    Cancelar
                </x-button>
                <x-button wire:click="generarNuevaVariacion"
                          class="bg-blue-600 text-white hover:bg-blue-700">
                    Nueva variación
                </x-button>
            </div>
        </div>
    </x-slot>
</x-dialog>
```

## Consideraciones de Diseño y Accesibilidad

### Principios de Diseño
1. **Jerarquía Visual Clara**: Uso consistente de tamaño, color y posición para indicar nivel
2. **Espaciado Adecuado**: Mínimo 24px entre nodos relacionados, 48px entre niveles
3. **Legibilidad**: Tamaño mínimo de fuente 14px para etiquetas, 12px para descripciones
4. **Contraste**: Todas las combinaciones de fondo/texto deben cumplir WCAG AA (4.5:1 mínimo)
5. **Consistencia**: Mismo estilo aplicado uniformemente a todos los nodos del mismo nivel

### Requisitos de Accesibilidad
- **Navegación por teclado**: Todos los elementos interactivos deben ser alcanzables y operables mediante teclado
- **Etiquetas ARIA**: Cada nodo significativo debe tener `role="img"` y `aria-label` descriptivo
- **Enfoque visible**: Contorno claro de 2px en estado de enfoque
- **Reducción de movimiento**: Opción para desactivar animaciones (si se añaden en futuro)
- **Textos alternativos**: Para iconos, usar descripción textual significativa
- **Escalabilidad**: Diseño que mantenga legibilidad al hacer zoom 200%

### Especificaciones Técnicas de SVG/Tailwind
- **Contenedor**: `<div class="relative w-full h-[400px] min-h-[300px]">` (altura ajustable según contenido)
- **Layout base**: Flexbox o CSS Grid para posicionamiento, con posición absoluta para nodos
- **Nodos**: 
  ```html
  <div class="relative w-[120px] h-[80px] flex items-center justify-center rounded-lg 
           bg-[color_fondo] text-[color_texto] font-medium text-shadow-sm 
           transition-transform duration-200 hover:scale-105"
       :style="--top: {{ posición_y }}px; --left: {{ posición_x }}px;"
       role="img"
       aria-label="{{ etiqueta }}: {{ descripcion }}">
      <svg class="w-5 h-5 mr-2" ...>[icono]</svg>
      <span>{{ etiqueta }}</span>
  </div>
  ```
- **Conexiones**: Líneas SVG `<line>` o `<path>` con clases Tailwind para color y grosor
  ```html
  <line x1="{{ x1 }}" y1="{{ y1 }}" x2="{{ x2 }}" y2="{{ y2 }}"
        class="stroke-[color_conexion] stroke-2" />
  ```
- **Tooltips** (opcional): 
  ```html
  <div class="absolute invisible group-hover:visible z-10 bg-gray-900/90 
           text-white px-2 py-1 rounded text-xs"
       role="tooltip">
     {{ descripcion }}
  </div>
  ```
- **Animaciones sutiles**: 
  - Transiciones de 200ms en escala y opacidad
  - Hover en nodos: scale(1.05), aumento de sombra
  - Enfoque: contorno ring-2 ring-emerald-500

## Integración con Contenido Existente

### Tipos de Contenido Soportados
La infografía generada se insertará como un bloque de tipo `HTML_EMBED` en el editor de contenidos, específicamente:
- En el arreglo `wizardHtmlEmbeds` del LessonWizard
- Con estructura:
  ```php
  [
    'id' => 'temp_'.uniqid(),
    'type' => 'HTML_EMBED',
    'title' => 'Infografía: [tema]',
    'body' => '<div class="infografia-container">[SVG generado]</div>',
    'is_visible' => true,
    'media' => null
  ]
  ```

### Compatibilidad con Edición Existente
- Las infografías se comportarán como cualquier otro embed HTML
- Se podrán seleccionar, mover, eliminar mediante las herramientas existentes del editor
- Al editar la sección, se mostrarán en modo de edición (como cualquier HTML)
- Se aplicarán las mismas reglas de visibilidad (`is_visible`) que otros contenidos

## Rendimiento y Optimización

### Estrategias de Carga
1. **Generación bajo demanda**: Solo se genera cuando el profesor solicita explícitamente
2. **Cache de prompts**: Los prompts similares pueden cachearse por 15 minutos para reducir llamadas a IA
3. **Lazy loading de preview**: El SVG solo se renderiza al mostrar el modal de vista previa
4. **Tamaño límite**: Los SVG generados no deben exceder 150KB para evitar impacto en el editor

### Consideraciones de Base de Datos
- **No almacenar SVG generados**: Solo se guarda la referencia como parte del contenido de la sección
- **Los SVG se generan en tiempo real** cada vez que se necesita mostrar (al cargar la sección)
- **Para mejorar rendimiento**: Opcionalmente guardar en caché por 24h usando clave basada en hash de la configuración

### Límites de Recursos
- **Máximo tiempo de generación**: 10 segundos (con feedback visual de progreso)
- **Máximo tamaño de archivo SVG**: 200KB (para evitar bloques excesivamente grandes)
- **Máximo número de nodos**: 50 nodos total (para mantener legibilidad)
- **Timeout de servicio IA**: 30 segundos con reintento automático una vez

## Seguridad y Privacidad

### Protección de Contenido
- **Sanitización de entrada**: Todo el texto del contexto pedagógico se escapa antes de enviar a IA
- **Validación de salida**: El SVG generado se sanitiza antes de insertar en el DOM
  - Eliminación de atributos peligrosos (`onload`, `onerror`, `script`, etc.)
  - Permitir solo atributos seguros de SVG: `class`, `id`, `width`, `height`, `viewBox`, `fill`, `stroke`, `stroke-width`, `d`, `cx`, `cy`, `r`, `x`, `y`, `text-anchor`
  - Permitir solo elementos SVG seguros: `svg`, `g`, `rect`, `circle`, `ellipse`, `line`, `path`, `polygon`, `polyline`, `text`, `tspan`, `use`
- **CSP Compatible**: Los SVG generados deben ser compatibles con Política de Seguridad de Contenido estricta

### Privacidad de Datos
- **No envío de datos personales**: El contexto pedagógico no incluye información identificable de estudiantes
- **Minimización de datos**: Solo se envía lo estrictamente necesario para generar la infografía
- **Registro de auditoría**: Las solicitudes de generación se registran en el sistema de binnacle (evento `ai_infografia_generation`)

## Manejo de Errores y Estados

### Estados del Componente
- `idle`: Estado inicial, listo para generar
- `configuring`: Modal de configuración abierto
- `generating`: Llamada a IA en curso (botón deshabilitado, spinner visible)
- `previewing`: Mostrando vista previa de SVG generado
- `error`: Estado de error con mensaje descriptivo
- `success`: Infografía insertada correctamente

### Tipos de Errores y Mensajes
| Error | Mensaje al Usuario | Acción Sugerida |
|-------|-------------------|-----------------|
| IA no responde | "No se pudo conectar con el servicio de diseño. Inténtalo nuevamente en unos minutos." | Reintentar después de 2 minutos |
| Respuesta inválida | "El diseño generado no cumple con los requisitos técnicos. Por favor, prueba con una configuración diferente." | Ajustar número de niveles o tipo de estructura |
| SVG no válido | "Se detectó contenido no seguro en el diseño generado. Por razones de seguridad, no se puede mostrar." | Reportar al administrador |
| SVG demasiado grande | "El diseño excede el tamaño máximo permitido. Reduce el número de niveles o nodos." | Reducir complejidad (máx 4 niveles o 30 nodos) |
| Error de renderizado | "No se pudo mostrar la vista previa. El diseño podría tener problemas de compatibilidad." | Intentar regenerar o cambiar tema de color |
| Éxito | "Infografía generada correctamente. Haz clic en 'Insertar en lección' para agregarla a tu sección." | Insertar o generar nueva variación |

## Criterios de Aceptación

### Funcionales
[ ] El botón "Generar Imagen" abre el modal de configuración correctamente
[ ] El modal permite seleccionar niveles entre 4 y 6
[ ] El modal permite seleccionar tipo de estructura (jerárquica, radial, flujo, matriz)
[ ] El modal permite selección de dirección cuando aplica
[ ] El modal permite selección de tema de color de la paleta SAEFL
[ ] Al generar, se muestra un estado de carga apropiado en el botón
[ ] El servicio de IA recibe un request con todos los parámetros requeridos
[ ] La respuesta de IA se interpreta correctamente o se muestra error descriptivo
[ ] El SVG generado cumple exactamente con 4-6 niveles de profundidad
[ ] El SVG utiliza exclusivamente HTML, SVG inline y clases de Tailwind
[ ] Los colores utilizados pertenecen a la paleta SAEFL especificada
[ ] El contraste de texto/fondo cumple WCAG AA mínimo (4.5:1)
[ ] La vista previa muestra el SVG correctamente renderizado
[ ] Al hacer clic en "Insertar en lección", el SVG se agrega como bloque HTML_EMBED
[ ] El bloque insertado se puede editar, mover y eliminar como cualquier otro contenido
[ ] El SVG mantiene su calidad al escalar (vectorial puro)
[ ] Los nodos son navegables y operables mediante teclado (si se implementa navegación)
[ ] Se generan eventos de auditoría apropiados en el sistema de binnacle

### No Funcionales
[ ] Tiempo de generación menor a 8 segundos en condiciones normales de red
[ ] Tamaño del SVG generado menor a 150KB para configuraciones típicas
[ ] Memoria utilizada durante generación menor a 50MB
[ ] Compatibilidad con navegadores: Chrome ≥100, Firefox ≥95, Safari ≥14, Edge ≥100
[ ] Accesibilidad: Puntuación mínima de 80 en axe-core para la infografía generada
[ ] Seguridad: Paso de escaneo OWASP ZAP básico sin vulnerabilidades críticas
[ ] Internacionalización: Todos los textos del modal están preparados para traducción (aunque el contenido genere en español)

## Dependencias y Recursos Requeridos

### Dependencias Técnicas
- Servicios de IA existentes (OpenRouterService, NvidiaService, KimiService)
- Servicio de sanitización de HTML (purifier o similar)
- Sistema de notificaciones existente de WireUI
- Sistema de diálgos existentes de WireUI
- Sistema de binnacle para registro de eventos

### Recursos de Diseño
- Conjunto de íconos educativos simples (20-30 íconos en formato SVG de línea única)
- Definiciones exactas de colores de la paleta SAEFL con valores hex
- Guía de contraste para combinaciones de colores de la paleta
- Plantillas básicas de estructuras (jerárquica, radial, flujo, matriz) para few-shot prompting

## Plan de Implementación por Fases

### Fase 1: Core Generation (Semana 1-2)
- [ ] Crear servicio `InfografiaGeneratorService`
- [ ] Implementar cadena de fallback y validación de respuestas
- [ ] Diseñar prompt especializado para infografías jerárquicas
- [ ] Crear modales de configuración y vista previa en LessonWizard
- [ ] Implementar botón y lógica de conexión
- [ ] Generar SVG básico a partir de estructura JSON
- [ ] Pruebas unitarias del servicio de generación

### Fase 2: Calidad y Accesibilidad (Semana 3)
- [ ] Implementar reglas de contraste y sanitización de SVG
- [ ] Añadir soporte para íconos educativos y tooltips
- [ ] Implementar validación de niveles (4-6) y post-procesamiento
- [ ] Mejorar accesibilidad del modal y elementos generados
- [ ] Agregar eventos de binnacle para generación y errores
- [ ] Pruebas de accesibilidad con axe-core y pruebas de usuario

### Fase 3: Integración y Pulido (Semana 4)
- [ ] Asegurar compatibilidad total con editor de contenidos existente
- [ ] Implementar inserción como bloque HTML_EMBED con metadata adecuada
- [ ] Añadir funcionalidad de "nueva variación" y ajustes posteriores
- [ ] Optimizar rendimiento (cache de prompts, límites de tamaño)
- [ ] Documentación de uso para profesores
- [ ] Pruebas de integración completas y beta testing con grupo pequeño

## Métricas de Éxito

### Métricas de Adoption
- % de profesores que usan la función al menos una vez por semana
- Número promedio de infografías generadas por lección creada
- Tiempo ahorrado estimado en creación de materiales visuales

### Métricas de Calidad
- % de infografías generadas que pasan revisión de accesibilidad inicial
- Tiempo promedio desde apertura del modal hasta inserción en lección
- Número de regeneraciones por infografía exitosa (indicador de satisfacción)
- % de infografías insertadas que permanecen en lección final (no se eliminan inmediatamente)

### Métricas de Técnica
- Tiempo promedio de generación (objetivo: <6 segundos)
- Tasa de éxito de generación (objetivo: >90%)
- Tamaño promedio del SVG generado (objetivo: <100KB)
- Porcentaje de llamadas que utilizan el servicio de IA primario (objetivo: >70%)

## Riesgos y Mitigaciones

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|--------------|---------|------------|
| Generación lenta o fallos de IA | Media | Alto | Cache de prompts, límites de tiempo agresivos, mensajes de error claros |
| Infografías demasiado complejas o ilegibles | Media | Medio | Límites estrictos de niveles/nodos, post-procesamiento automático, preview obligatorio |
| Problemas de accesibilidad | Baja | Alto | Validación de contraste automatizada, pruebas con axe-core, modo de alto contraste opcional |
| Incompatibilidad con editor existente | Baja | Alto | Pruebas de integración exhaustivas, uso de contenedores aislados, fallback a imagen si es necesario |
| Sobrecarga de servicios de IA | Media | Medio | Límites de tasa por usuario, cache de prompts similares, colas de prioridad baja |
| Preocupaciones de privacidad académica | Baja | Alto | Minimización de datos enviados, revisión legal de prompts, anonimización de contexto |

## Anexos

### A. Paleta de Colores SAEFL Completa
```css
/* Emerald */
--emerald-50: #f0fdf4;
--emerald-100: #dcfce7;
--emerald-200: #bbf7d0;
--emerald-300: #86efac;
--emerald-400: #4ade80;
--emerald-500: #10b981;
--emerald-600: #059669;
--emerald-700: #047857;
--emerald-800: #064e3b;
--emerald-900: #065f46;

/* Sky */
--sky-50: #f0f9ff;
--sky-100: #e0f2fe;
--sky-200: #bae6fd;
--sky-300: #7dd3fc;
--sky-400: #38bdf8;
--sky-500: #0ea5e9;
--sky-600: #0284c7;
--sky-700: #0369a1;
--sky-800: #075985;
--sky-900: #0c4a6e;

/* Amber */
--amber-50: #fffbeb;
--amber-100: #fef3c7;
--amber-200: #fde68a;
--amber-300: #fbbf24;
--amber-400: #facc15;
--amber-500: #f59e0b;
--amber-600: #d97706;
--amber-700: #b45309;
--amber-900: #78350f;

/* Purple */
--purple-50: #faf5ff;
--purple-100: #f3e8ff;
--purple-200: #e9d5ff;
--purple-300: #d8b4fe;
--purple-400: #c084fc;
--purple-500: #8b5cf6;
--purple-600: #7c3aed;
--purple-700: #6d28d9;
--purple-800: #5b21b6;
--purple-900: #4c1d95;

/* Rose */
--rose-50: #fff1f2;
--rose-100: #ffe4e6;
--rose-200: #fecdd3;
--rose-300: #fda4af;
--rose-400: #fb7185;
--rose-500: #f43f5e;
--rose-600: #e11d48;
--rose-700: #be123c;
--rose-900: #881337;

/* Stone */
--stone-50: #fafaf9;
--stone-100: #f5f5f4;
--stone-200: #e7e5e4;
--stone-300: #d6d3d1;
--stone-400: #a8a29e;
--stone-500: #78716c;
--stone-600: #57534e;
--stone-700: #44403c;
--stone-900: #1c1917;
```

### B. Conjunto de Íconos Educativos Sugeridos (SVG Path Data)
```html
<!-- Libro -->
<path d="M4 4h16c1.1 0 2 .9 2 2v8c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>

<!-- Bombilla -->
<path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>

<!-- Microscopio -->
<path d="M9 4h6a2 2 0 012 2v2a2 2 0 002 2v2h2a2 2 0 012 2v8a2 2 0 00-2 2h-2v-2a2 2 0 00-2-2v-2h-2a2 2 0 01-2-2V6a2 2 0 00-2-2V4zM9 10h6v2H9z"/>

<!-- Globo -->
<path d="M12 2.252a9 9 0 100 0 16.506 9 9 0 000-16.506zm0 14.25a3 3 0 100-5.196 3 3 0 0010.392-6 3 3 0 00-10.392 6z"/>

<!-- Átomo -->
<path d="M12 2.252a9 9 0 100 0 16.506 9 9 0 000-16.506zm0 14.25a3 3 0 100-5.196 3 3 0 0010.392-6 3 3 0 00-10.392 6zM12 16a4 4 0 110-8 4 4 0 010 8z"/>

<!-- Hoja -->
<path d="M12 2.252a9 9 0 100 0 16.506 9 9 0 000-16.506zm0 14.25a3 3 0 100-5.196 3 3 0 0010.392-6 3 3 0 00-10.392 6zM12 8a4 4 0 100 8 8h-8a4 4 0 100 0-8z"/>

<!-- Engranaje -->
<path d="M12 2.252a9 9 0 100 0 16.506 9 9 0 000-16.506zm0 14.25a3 3 0 100-5.196 3 3 0 0010.392-6 3 3 0 00-10.392 6zM12 12a2.25 2.25 0 100-4.5 0 2.25 2.25 0 004.5 0z"/>

<!-- Pincel -->
<path d="M5 3.5a2.5 2.5 0 015 0v1h4a.5.5 0 010 1h-4v4a2 2 0 01-2 2H3a2 2 0 01-2-2V4.5h4a.5.5 0 010-1H5v-1z"/>

<!-- Nota Musical -->
<path d="M12 2.252a9 9 0 100 0 16.506 9 9 0 000-16.506zm0 14.25a3 3 0 100-5.196 3 3 0 0010.392-6 3 3 0 00-10.392 6zM12 8v4h2v-2h2V8h-2V4h-2v4z"/>

<!-- Pelota de Fútbol -->
<path d="M12 2.252a9 9 0 100 0 16.506 9 9 0 000-16.506zm0 14.25a4 4 0 100 8 0 0 0-8-8zm0 0a4 4 0 10-8 8 0 0 8-8z"/>

<!-- Corazón -->
<path d="M12 2.252a9 9 0 100 0 16.506 9 9 0 000-16.506zm0 14.25a5 5 0 00-5 8.83 0 0 0-5-8.83z"/>

<!-- Escudo -->
<path d="M12 2.252a9 9 0 100 0 16.506 9 9 0 000-16.506zm0 14.25a4.5 4.5 0 010 9 0 0 0-9-9z"/>

<!-- Imán -->
<path d="M12 2.252a9 9 0 100 0 16.506 9 9 0 000-16.506zm0 14.25a3 3 0 100 6 0 0 0-6-6z"/>

<!-- Matraz -->
<path d="M12 2.252a9 9 0 100 0 16.506 9 9 0 000-16.506zm0 14.25a2 2 0 012 3.5h-4a2 2 0 012-3.5z"/>

<!-- Binoculares -->
<path d="M12 2.252a9 9 0 100 0 16.506 9 9 0 000-16.506zm0 14.25a2 2 0 10-2 4 0 0 0 2-4z"/>

<!-- Brújula -->
<path d="M12 2.252a9 9 0 100 0 16.506 9 9 0 000-16.506zm0 14.25a2 2 0 100-4 4v4a2 2 0 00-4-4z"/>

<!-- Regla -->
<path d="M12 2.252a9 9 0 100 0 16.506 9 9 0 000-16.506zm0 14.25a1 1 0 012 0v14a1 1 0 00-2 0V2.252z"/>
```

### C. Algoritmo de Validación de Contraste WCAG
```php
/**
 * Calcula el contraste entre dos colores hexadecimales
 * @param string $hex1 Color en formato #RRGGBB
 * @param string $hex2 Color en formato #RRGGBB
 * @return float Ratio de contraste (1.0 a 21.0)
 */
private function calculateContrast(string $hex1, string $hex2): float
{
    $rgb1 = $this->hexToRgb($hex1);
    $rgb2 = $this->hexToRgb($hex2);
    
    $luminance1 = $this->calculateRelativeLuminance($rgb1);
    $luminance2 = $this->calculateRelativeLuminance($rgb2);
    
    $lighter = max($luminance1, $luminance2);
    $darker = min($luminance1, $luminance2);
    
    return ($lighter + 0.05) / ($darker + 0.05);
}

/**
 * Convierte hexadecimal a array RGB
 */
private function hexToRgb(string $hex): array
{
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) .
               str_repeat(substr($hex, 1, 1), 2) .
               str_repeat(substr($hex, 2, 1), 2);
    }
    
    return [
        'r' => hexdec(substr($hex, 0, 2)) / 255,
        'g' => hexdec(substr($hex, 2, 2)) / 255,
        'b' => hexdec(substr($hex, 4, 2)) / 255
    ];
}

/**
 * Calcula luminancia relativa según WCAG
 */
private function calculateRelativeLuminance(array $rgb): float
{
    $r = $rgb['r'];
    $g = $rgb['g'];
    $b = $rgb['b'];
    
    $r = ($r <= 0.03928) ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
    $g = ($g <= 0.03928) ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
    $b = ($b <= 0.03928) ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);
    
    return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
}
```