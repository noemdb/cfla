# Procedimiento: Confirmación de Prosecución Estudiantil

> **Propósito**: Guía paso a paso para que el representante confirme la continuidad educativa de sus estudiantes en la institución.

---

## Requisitos previos

- Acceso a internet y un navegador web (Chrome, Firefox, Edge).
- Número de cédula de identidad del representante (registrado previamente en el sistema).
- Los estudiantes deben estar inscritos activamente en el año escolar 2025-2026.
- **Solo aplica para estudiantes REGULARES** que continuarán en la institución.

---

## Diagrama de flujo

```
┌─────────────────────────────────────────────────────────┐
│                    INICIO                               │
│           /prosecucion (o desde guía)                    │
└─────────────────────┬───────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────┐
│  PASO 1: IDENTIFICACIÓN                                 │
│  Ingresar cédula del representante                      │
│  ┌─────────────────────────────────────────────────┐    │
│  │  [Input: Cédula de Identidad]                   │    │
│  │  [Botón: Buscar Representante]                  │    │
│  └─────────────────────────────────────────────────┘    │
└─────────────────────┬───────────────────────────────────┘
                      │
          ┌───────────┴───────────┐
          │                       │
          ▼                       ▼
   ┌──────────────┐     ┌──────────────────┐
   │  Éxito       │     │  Error           │
   │  Ir a paso 2 │     │  Mostrar toast   │
   └──────┬───────┘     │  con mensaje     │
          │             └────────┬─────────┘
          │                      │
          │                      ▼
          │              ┌──────────────────┐
          │              │  Reintentar CI   │
          │              └──────────────────┘
          │
          ▼
┌─────────────────────────────────────────────────────────┐
│  PASO 2: SELECCIÓN DE ESTUDIANTES                       │
│  ┌─────────────────────────────────────────────────┐    │
│  │  ☑ Estudiante 1 - 5to Grado A  (Confirmado)    │    │
│  │  ☐ Estudiante 2 - 3er Grado B                  │    │
│  │  ☐ Estudiante 3 - 1er Año C                    │    │
│  │                                                 │    │
│  │  [Botón: Confirmar Prosecución]                 │    │
│  │  [Botón: Volver]                                │    │
│  └─────────────────────────────────────────────────┘    │
│                                                         │
│  NOTA: Los estudiantes ya confirmados en prosecuciones  │
│  anteriores aparecen pre-marcados y NO pueden            │
│  desmarcarse.                                            │
└─────────────────────┬───────────────────────────────────┘
                      │
          ┌───────────┴───────────┐
          │                       │
          ▼                       ▼
   ┌──────────────┐     ┌──────────────────────┐
   │  OK          │     │  Sin selección       │
   │  Ir a paso 3 │     │  Toast de error      │
   └──────┬───────┘     └──────────────────────┘
          │
          ▼
┌─────────────────────────────────────────────────────────┐
│  PASO 3: CONFIRMACIÓN Y DESCARGA                        │
│                                                         │
│  🎉 Prosecución Confirmada                              │
│                                                         │
│  ┌─────────────────────────────────────────────────┐    │
│  │  Resumen de estudiantes confirmados             │    │
│  │  ─────────────────────                          │    │
│  │  ✓ García María      5to Grado A   [Previo]    │    │
│  │  ✓ López Carlos      3er Grado B                │    │
│  └─────────────────────────────────────────────────┘    │
│                                                         │
│  ┌─────────┐                                            │
│  │  QR     │  Código de verificación                   │
│  │  CODE   │  Escanee para descargar la planilla       │
│  └─────────┘                                            │
│                                                         │
│  [Botón: Descargar Planilla]                            │
│  [Botón: Nueva Consulta]                                │
└─────────────────────────────────────────────────────────┘
```

---

## Paso a paso detallado

### Paso 1: Identificación del Representante

**Pantalla**: `/prosecucion`

```
┌──────────────────────────────────────────────────┐
│  Panel izquierdo (pasos)     │  Panel derecho     │
│                              │                    │
│  ┌────────────────────────┐  │  Identificación    │
│  │  1  Identificación ◄   │  │  del Representante │
│  │  2  Estudiantes        │  │                    │
│  │  3  Confirmación       │  │  [Cédula]          │
│  │  Empezar de nuevo      │  │                    │
│  └────────────────────────┘  │  [Buscar]          │
└──────────────────────────────────────────────────┘
```

1. **Ingrese** su número de cédula de identidad en el campo de texto.
2. **Haga clic** en el botón **"Buscar Representante"**.
3. **Resultados posibles**:
   - ✅ **Éxito**: Se avanza automáticamente al Paso 2.
   - ❌ **Error**: Aparece una notificación toast indicando que la cédula no está registrada. Verifique el número e intente nuevamente.
   - ❌ **Sin estudiantes**: Si la cédula existe pero no tiene estudiantes activos, se muestra un mensaje de error.

### Paso 2: Selección de Estudiantes

```
┌──────────────────────────────────────────────────┐
│  Panel izquierdo          │  Panel derecho        │
│                           │                       │
│  1  Identificación ✓      │  Seleccionar          │
│  2  Estudiantes ◄        │  Estudiantes          │
│  3  Confirmación          │                       │
│                           │  □ García María       │
│                           │     CI: V-98765432    │
│                           │     5to Grado A       │
│                           │                       │
│                           │  □ López Carlos       │
│                           │     CI: V-87654321    │
│                           │     3er Grado B       │
│                           │                       │
│                           │  [Confirmar] [Volver] │
└──────────────────────────────────────────────────┘
```

1. **Revise** la lista de estudiantes asociados a su cédula.
2. Para cada estudiante verá:
   - Nombre y apellido.
   - Número de cédula del estudiante.
   - Grado y sección actual.
   - Edad.
3. **Marque** los estudiantes que continuarán en la institución:
   - Haga clic en el checkbox junto al nombre del estudiante.
   - Los estudiantes ya confirmados en visitas previas aparecen **pre-marcados y bloqueados**.
4. **Haga clic** en **"Confirmar Prosecución"**.
5. **Resultados posibles**:
   - ✅ **Éxito**: Se guarda la confirmación y se avanza al Paso 3.
   - ❌ **Error**: Si no selecciona ningún estudiante, aparece una notificación. Seleccione al menos uno.
   - ⚠️ **Advertencia**: Si intenta desmarcar un estudiante ya confirmado, el sistema lo rechazará.

### Paso 3: Confirmación y Descarga

```
┌──────────────────────────────────────────────────┐
│  Panel izquierdo          │  Panel derecho        │
│                           │                       │
│  1  Identificación ✓      │  🎉 Prosecución      │
│  2  Estudiantes ✓         │     Confirmada        │
│  3  Confirmación ◄       │                       │
│                           │  Resumen:            │
│                           │  ✓ García María      │
│                           │     5to Grado A      │
│                           │  ✓ López Carlos      │
│                           │     3er Grado B      │
│                           │                       │
│                           │  [QR Code]           │
│                           │  [Descargar]         │
│                           │  [Nueva Consulta]     │
└──────────────────────────────────────────────────┘
```

1. **Verifique** el resumen de estudiantes confirmados.
2. **Descargue la planilla** de confirmación:
   - Opción A: Haga clic en **"Descargar Planilla"** para obtener el PDF.
   - Opción B: **Escanee el código QR** con su teléfono para descargar la planilla.
3. La planilla PDF incluye:
   - Datos del representante.
   - Lista de estudiantes confirmados.
   - Código QR de verificación.
   - Firma digital y fecha de confirmación.
4. **Opcional**: Haga clic en **"Nueva Consulta"** para reiniciar el proceso.

---

## Arquitectura técnica

### Rutas

| Método | URI | Controlador | Propósito |
|--------|-----|-------------|-----------|
| GET | `/prosecucion` | `HomeController@prosecucion` | Muestra el wizard |
| GET | `/prosecucion/guia` | `HomeController@prosecucion_guia` | Guía informativa |
| GET | `/prosecucion/download/{id}` | `HomeController@downloadProsecucionPDF` | Descarga PDF |

### Componentes

| Componente | Archivo | Propósito |
|------------|---------|-----------|
| `ProsecucionWizard` | `app/Livewire/ProsecucionWizard.php` | Lógica del wizard (3 pasos) |
| `HomeController` | `app/Http/Controllers/HomeController.php` | Controlador de rutas y PDF |

### Vistas

| Vista | Ruta | Propósito |
|-------|------|-----------|
| `prosecucion` | `resources/views/prosecucion.blade.php` | Layout principal |
| `prosecucion_guia` | `resources/views/prosecucion_guia.blade.php` | Página de guía |
| `prosecucion-wizard` | `resources/views/livewire/prosecucion-wizard.blade.php` | Wizard interactivo |
| `left` | `resources/views/livewire/prosecucion/section/left.blade.php` | Sidebar con pasos |
| `prosecucion-form` | `resources/views/pdfs/prosecucion-form.blade.php` | Plantilla PDF |

### Modelos

| Modelo | Conexión | Propósito |
|--------|----------|-----------|
| `Estudiant` | `s2526` | Datos del estudiante (incluye `status_prosecution`, `date_prosecution`) |
| `Representant` | `s2526` | Datos del representante |

### Flujo de datos

```
1. Representante ingresa CI
       │
       ▼
2. ProsecucionWizard.searchRepresentant()
   ├── Busca Representant·on('s2526')->where('ci_representant', $ci)
   ├── Busca Estudiant·on('s2526')->where('representant_id', $id)
   │      ->where('status_active', true)
   │      ->whereHas('inscripcion.seccion', ...)
   │
   ▼
3. ProsecucionWizard.confirmProsecucion()
   ├── Valida selección
   ├── Estudiant::whereIn('id', $newConfirmations)
   │      ->update(['status_prosecution' => true, 'date_prosecution' => now()])
   ├── Genera URL de descarga: route('prosecucion.download.pdf', $id)
   └── Genera QR code
       │
       ▼
4. HomeController.downloadProsecucionPDF($id)
   ├── Busca representante
   ├── Carga estudiantes con status_prosecution = true
   └── Genera PDF con dompdf
```

### Estilos visuales

El diseño sigue la misma línea visual que el **Censo Escolar**:
- **Fondo**: Negro (`bg-black`) con imagen `census.jpg` a opacidad 10%.
- **Layout**: Dos paneles (sidebar + contenido) con `flex flex-col lg:flex-row`.
- **Sidebar**: Gradiente `from-green-400 via-green-600 to-black`, esquinas `rounded-[40px]`.
- **Pasos**: Lista vertical numerada con efecto `backdrop-blur-sm`.
- **Botones**: Verde oscuro (`!bg-green-800 hover:!bg-green-900 border-2 !border-green-900`).
- **Inputs**: WireUI `x-input` con `right-icon`.
- **Notificaciones**: WireUI toast con `z-index: 9999`.

---

## Manejo de errores

| Escenario | Comportamiento |
|-----------|---------------|
| Cédula no registrada | Toast de error: "No se encontró un representante con la cédula proporcionada" |
| Sin estudiantes activos | Toast de error: "No se encontraron estudiantes habilitados para prosecución" |
| Ningún estudiante seleccionado | Toast de error: "Debe seleccionar al menos un estudiante para continuar" |
| Desmarcar estudiante ya confirmado | Toast de error: "No se puede desmarcar estudiantes que ya han sido confirmados" |
| CI inválido (menos de 6 caracteres) | Validación inline: "La cédula debe tener al menos 6 caracteres" |
| Error al generar QR | Toast de advertencia, permite descarga directa sin QR |

---

## Mantenimiento

### Agregar/quitar estudiantes de la consulta

Editar el método `searchRepresentant()` en `app/Livewire/ProsecucionWizard.php`:

```php
->whereHas('inscripcion', function($query) {
    $query->whereHas('seccion', function($subQuery) {
        $subQuery->where('status_active', 'true')
                ->where('status_inscription_affects', 'true')
                ->whereNotIn('id', ['21','22','35','46','75','76','77','78']);
    });
});
```

Los IDs en `whereNotIn('id', [...])` son secciones excluidas (ej. egresados, grupos especiales).

### Cambiar período escolar

Actualmente configurado para **2025-2026**. Para cambiarlo:
1. Actualizar el título en `resources/views/livewire/prosecucion/section/left.blade.php`
2. Actualizar el título en `resources/views/prosecucion.blade.php` (si aplica)

---

## Referencias

- [Layout de Censo (referencia visual)](../../resources/views/livewire/catchment-wizard.blade.php)
- [Componente ProsecucionWizard](../../app/Livewire/ProsecucionWizard.php)
- [Controlador PDF](../../app/Http/Controllers/HomeController.php)
- [Modelo Estudiant - Trait Prosecucions](../../app/Models/app/trait/Estudiant/Prosecucions.php)
