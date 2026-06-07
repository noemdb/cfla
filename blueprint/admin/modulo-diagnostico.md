# Módulo de Diagnóstico Académico

> **Ruta base:** `/admin/diagnostico`
> **Ruta pública:** `/diagnostico`
> **Propósito:** Sistema de evaluación diagnóstica donde los estudiantes responden preguntas de selección múltiple, escala numérica y texto abierto para medir conocimientos en diversas áreas de formación.

---

## 1. ARQUITECTURA GENERAL

### 1.1 Stack Tecnológico

| Componente | Tecnología |
|---|---|
| Framework | Laravel 10 |
| Componentes interactivos | Livewire 3 (full-page + Wizard de 5 vistas) |
| UI | Tailwind CSS 3 + WireUI 2.x |
| BD | MySQL vía Eloquent ORM |
| Layout | Layout personalizado `layouts.diagnostic` (no usa dashboard) |

### 1.2 Navegación del Sistema

```
RUTA PÚBLICA (sin autenticación):
  GET /diagnostico → Vista pública del diagnóstico (Livewire full-page)

RUTA ADMIN (protegida: auth + isAdminOrDiagnostic):
  GET /admin/diagnostico → Panel de activación de áreas de formación
```

### 1.3 Patrón de Arquitectura

El módulo sigue un patrón **monocomponente con vistas parciales**:

1. **Ruta pública** (`/diagnostico`): Controlador `HomeController::diagnostico()` retorna la vista `diagnostico.blade.php` que carga un **único componente Livewire**: `App\Livewire\Diagnostic`
2. **El componente `Diagnostic`** maneja internamente un **wizard de 5 vistas** (student-identification, dashboard, wizard, summary, guide) mediante una variable `$currentView`
3. **Ruta admin** (`/admin/diagnostico`): Livewire `Admin\Diagnostic\IndexComponent` que lista Pestudios > Grados > Pensums con toggles de activación para diagnóstico
4. No usa WebSockets ni broadcasting — todo es petición/respuesta síncrona

---

## 2. MODELOS DE DATOS (7+ entidades)

### 2.1 `diag_questions` — Preguntas del Diagnóstico

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | PK auto | Identificador |
| `pensum_id` | unsigned FK → `pensums` | Pensum/Área de formación |
| `pregunta` | text | Enunciado de la pregunta |
| `tipo_pregunta` | enum? string | Tipo: `multiple`, `scale`, `open` |
| `orden` | integer | Orden de presentación |
| `difficulty` | string nullable | Dificultad: `easy`, `medium`, `hard` |
| `weighing` | integer nullable | Peso/ponderación |
| `activo` | boolean | Activo (visible para diagnóstico) |
| `created_at` / `updated_at` | timestamps | Auditoría |

**Relaciones:** `pensum()`, `options()`, `answers()`
**Accessor:** `getAsignaturaNameAttribute` → nombre completo de la asignatura vía `pensum->asignatura->full_name`

### 2.2 `diag_options` — Opciones de Respuesta

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | PK auto | Identificador |
| `question_id` | unsigned FK → `diag_questions` | Pregunta padre |
| `opcion` | string | Texto de la opción |
| `valor` | integer nullable | Valor numérico (1 = correcta, 0 = incorrecta) |
| `orden` | integer nullable | Orden de las opciones |

**Relaciones:** `question()`

### 2.3 `diag_sessions` — Sesiones de Diagnóstico

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | PK auto | Identificador |
| `estudiant_id` | unsigned FK → `estudiants` | Estudiante |
| `pensum_id` | unsigned FK → `pensums` | Área de formación |
| `iniciado_at` | datetime nullable | Inicio de la sesión |
| `completado_at` | datetime nullable | Finalización |
| `progreso` | integer default 0 | Porcentaje de progreso (0-100) |
| `total_preguntas` | integer default 0 | Total de preguntas en la sesión |
| `activo` | boolean | Sesión activa (en progreso) |
| `created_at` / `updated_at` | timestamps | Auditoría |

**Relaciones:** `estudiant()`, `pensum()`, `answers()` (hasManyThrough via Estudiant)

### 2.4 `diag_answers` — Respuestas

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | PK auto | Identificador |
| `estudiant_id` | unsigned FK → `estudiants` | Estudiante |
| `question_id` | unsigned FK → `diag_questions` | Pregunta |
| `option_id` | unsigned FK → `diag_options` nullable | Opción seleccionada (si es multiple choice) |
| `session_id` | unsigned FK → `diag_sessions` | Sesión |
| `respuesta` | text nullable | Texto de la respuesta (para open/scale) |
| `valor_numerico` | integer nullable | Valor numérico calculado |
| `completado_at` | datetime nullable | Cuándo se respondió |

**Relaciones:** `question()`, `estudiant()`, `selectedOption()`

**Métodos clave:**
- `isCorrect()` — Para multiple choice: true si `selectedOption.valor == 1`
- `calculateStudentPrecision(estudiantId, pensumId)` — Precisión = `(100 * correctas / total_contestadas)`
- `getStudentPrecisionStats(estudiantId, pensumId)` — Precisión de un estudiante
- `getOverallPrecisionStats(pensumId)` — Precisión global

### 2.5 `pensums` — Pensum / Malla Curricular

| Campo clave | Descripción |
|---|---|
| `pestudio_id` FK → `pestudios` | Plan de estudio |
| `grado_id` FK → `grados` | Grado |
| `asignatura_id` FK → `asignaturas` | Asignatura |
| `status_active_diagnostic` boolean | **Activo para diagnóstico** — manejado desde admin |
| `status_component` boolean nullable | ¿Tiene componentes de formación? |

**Relaciones clave:** `diagQuestions()`, `asignatura()`, `pestudio()`, `grado()`, `pevaluacions()`

**Nota:** `Pensum::whereHas('pevaluacions')` — Filtro usado en el admin para mostrar solo pensums con evaluaciones cargadas.

### 2.6 `pestudios` — Planes de Estudio

| Campo clave | Descripción |
|---|---|
| `id` | Identificador |
| `name` | Nombre (ej: "Educación Básica", "Educación Media General") |
| `code` | Código (ej: `21000`, `31059`) |
| `status_active` | Activo/inactivo |

**Relaciones:** `grados()`
**Métodos:** `getGradosActiveWithPensum()` — Grados activos que tienen al menos un pensum cargado

### 2.7 `asignaturas` — Asignaturas

| Campo clave | Descripción |
|---|---|
| `id` | Identificador |
| `name` | Nombre (ej: "Matemática", "Castellano") |
| `code` | Código |
| `peducativo_id` FK → `peducativos` | Programa educativo |

**Relaciones:** `pensums()`
**Accessor:** `fullName` → `[code] name`

### 2.8 `estudiants` — Estudiantes

**Relación clave (accessor):** `getPensumsAttribute()` — Obtiene todos los pensums del estudiante a través de Inscripción → Sección → Grado → Pensum (join de 6 tablas)

### 2.9 `pevaluacions` — Evaluaciones (carga académica)

Se usa como filtro: `Pensum::whereHas('pevaluacions')` — solo se muestran pensums que tienen evaluaciones cargadas.

---

## 3. ESQUEMA RELACIONAL

```
peducativos (1) ──< pestudios (N) ──< grados (N) ──< seccions (N) ──< inscripcions (N) ──< estudiants (N)
                     │                   │                                                          │
                     │                   │                                                          │
                     │            ┌──────┘                                                          │
                     │            │                                                                 │
                     │       pensums (N) ──< diag_questions (N) ──< diag_options (N)                │
                     │            │          │                                                      │
                     │            │          └──< diag_answers (N) ─────────────────────────────────┘
                     │            │                    │
                     │            │              ┌─────┘
                     │            │              │
                     │            └──< pevaluacions (N)
                     │
                     └──< asignaturas (N) ──< pensums (N)
```

---

## 4. RUTAS COMPLETAS

### 4.1 Ruta Pública

```php
Route::get('/diagnostico', [HomeController::class, 'diagnostico'])->name('diagnostico');
```

### 4.2 Ruta Admin

```php
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::middleware(['isAdminOrDiagnostic'])->group(function () {
        Route::prefix('diagnostico')->name('diagnostico.')->group(function () {
            Route::get('/', DiagnosticIndex::class)->name('index');
        });
    });
});
```

---

## 5. COMPONENTES LIVEWIRE (2 principales)

### 5.1 `App\Livewire\Diagnostic` — Componente Principal (Público)

**Namespace:** `App\Livewire\Diagnostic`

**Propiedades de estado (`$currentView`):**

| Vista | Propósito |
|---|---|
| `student-identification` | Formulario de ingreso de cédula |
| `dashboard` | Panel con áreas de formación disponibles |
| `wizard` | Responder preguntas una por una |
| `summary` | Resumen al completar un área |
| `guide` | Guía de participación informativa |

**Propiedades clave:**

| Propiedad | Tipo | Descripción |
|---|---|---|
| `currentView` | string | Vista activa del wizard |
| `studentCi` | string | Cédula ingresada |
| `currentStudent` | Model/null | Estudiante autenticado |
| `isStudentVerified` | bool | ¿Estudiante verificado? |
| `selectedPensum` | Model/null | Pensum seleccionado para diagnóstico |
| `currentSession` | Model/null | Sesión activa (DiagSession) |
| `currentQuestionIndex` | int | Índice de la pregunta actual |
| `currentQuestion` | Model/null | Pregunta actual |
| `selectedAnswer` | mixed | Respuesta seleccionada |
| `answers[]` | array | Array de respuestas indexadas |
| `progress` | int | Porcentaje de progreso (0-100) |
| `isReviewMode` | bool | Modo revisión |
| `showAnsweredQuestions` | bool | Mostrar solo contestadas (toggle) |
| `unansweredQuestions` | Collection | Preguntas sin responder |
| `answeredQuestions` | Collection | Preguntas ya respondidas |
| `showAnsweredModal` | bool | Modal de respuestas detalladas |
| `isProcessing` | bool | Flag anti-doble clic |
| `activeTab` | string | Pestaña activa en la guía: `overview`, `process`, `questions`, `tips` |
| `pensums[]` | array | Áreas de formación disponibles |
| `questions` | Collection | Preguntas del pensum activo |
| `sessionStats[]` | array | Estadísticas de sesiones |

**Eventos listeners (legacy):**
```php
protected $listeners = [
    'startDiagnostic', 'nextQuestion', 'previousQuestion',
    'finishDiagnostic', 'reviewAnswers', 'toggleQuestionView',
    'openAnsweredQuestionsModal', 'closeAnsweredQuestionsModal'
];
```

**Validación:**
- `student-identification`: `studentCi: required|string|min:6|max:15`
- `wizard`: `selectedAnswer: required`
- Mensajes personalizados en español

**Métodos principales:**

| Método | Descripción |
|---|---|
| `verifyStudent()` | Busca estudiante por cédula, verifica activo, carga datos |
| `loadStudentData()` | Carga pensums disponibles y estadísticas de sesión |
| `loadAvailablePensums()` | Filtra pensums `status_active_diagnostic=true` con preguntas activas |
| `startDiagnostic(pensumId)` | Crea/recupera sesión, filtra preguntas, inicia wizard |
| `filterQuestionsByAnsweredStatus()` | Separa respondidas vs no respondidas |
| `toggleQuestionView()` | Alterna entre vistas de preguntas respondidas/pendientes |
| `saveAnswer()` | Guarda respuesta con tipo específico (multiple/scale/open) |
| `nextQuestion()` | Avanza y guarda respuesta si es necesario |
| `previousQuestion()` | Retrocede |
| `finishDiagnostic()` | Marca sesión como completada, muestra resumen |
| `backToDashboard()` | Vuelve al dashboard |
| `restartIdentification()` | Reinicia todo el flujo |
| `getAnsweredQuestionsWithAnswers()` | Obtiene respuestas detalladas |
| `reviewAnswers(pensumId)` | Abre modal de revisión |
| `showGuide()` | Muestra guía de participación |
| `updatedSelectedAnswer()` | Dispara `answer-updated` para UI |
| `getCanProceedProperty()` | Verifica si se puede avanzar según tipo de pregunta |

**Flujo de respuesta por tipo:**

| Tipo | Validación | Almacenamiento |
|---|---|---|
| `multiple` | `selectedAnswer` no vacío | Busca opción, guarda `option_id` + `valor_numerico` |
| `scale` | Numeric 1-10 | Guarda valor directo en `respuesta` y `valor_numerico` |
| `open` | Mínimo 3 caracteres | Guarda texto en `respuesta`, `valor_numerico=0` |

### 5.2 `App\Livewire\Admin\Diagnostic\IndexComponent` — Admin

**Namespace:** `App\Livewire\Admin\Diagnostic`

**Propiedades:** Ninguna persistente (render consulta datos en vivo)

**Métodos:**

| Método | Descripción |
|---|---|
| `toggleAllPestudio(pestudioId, activate)` | Activa/desactiva diagnóstico para todo un plan de estudio |
| `toggleAllGrado(pestudioId, gradoId, activate)` | Activa/desactiva para todo un grado |
| `toggleStatus(pensumId)` | Toggle individual por pensum |

**Render:**
- Carga `Pestudio::with(['grados.seccions', 'grados' => fn($q) => $q->whereActive()->has('pensums')])->whereActive()->orderBy('order')`
- Carga `Pensum::whereHas('pevaluacions')->with(['asignatura','grado','pestudio'])->withCount('diagQuestions')->get()->groupBy(['pestudio_id','grado_id'])`
- Layout: `layouts.dashboard`

---

## 6. VISTAS (ARCHIVOS BLADE)

### 6.1 Estructura completa

```
resources/views/
├── diagnostico.blade.php                       → Vista pública principal (Layout: diagnostic)
├── layouts/
│   └── diagnostic.blade.php                    → Layout personalizado para diagnóstico
├── livewire/
│   ├── diagnostic.blade.php                    → Componente Livewire principal
│   │   (dispatcher interno según $currentView)
│   ├── diagnostic/
│   │   ├── student-identification.blade.php    → Formulario de cédula
│   │   ├── dashboard.blade.php                 → Panel con áreas + estadísticas
│   │   ├── wizard.blade.php                    → Responder preguntas
│   │   ├── summary.blade.php                   → Resumen de finalización
│   │   └── guide.blade.php                     → Guía de participación (4 tabs)
│   └── admin/diagnostic/
│       └── index-component.blade.php           → Admin: activación de áreas
```

### 6.2 Layout `diagnostic.blade.php`

Layout personalizado e independiente que incluye:
- Header con logo de la institución y título "Diagnóstico Educativo"
- Footer institucional con características del sistema
- JavaScript para: prevenir pérdida de progreso, auto-scroll, mejora táctil móvil
- CSS extenso (animaciones, gradientes, scrollbar, efectos glow, responsive)
- Meta tags: `noindex, nofollow`, prevención de zoom en móviles

---

## 7. FLUJO DE USUARIO (Wizard Completo)

### 7.1 Diagrama de Vistas

```
student-identification ──(verificar cédula)──> dashboard
                                                    │
                              ┌─────────────────────┼─────────────────────┐
                              ▼                     ▼                     ▼
                         startDiagnostic        showGuide            reviewAnswers
                              │                     │                     │
                              ▼                     ▼                     ▼
                          wizard                 guide              (modal respuestas)
                              │
                    ┌─────────┼─────────┐
                    ▼         ▼         ▼
              [Finalizar]  [Anterior]  [Siguiente]
                    │
                    ▼
                summary
                    │
            ┌───────┴───────┐
            ▼               ▼
      backToDashboard   restartIdentification
```

### 7.2 Flujo Detallado

**Paso 1: Identificación del Estudiante**
1. Estudiante ingresa su cédula
2. Sistema busca en `Estudiante::where('ci_estudiant', $studentCi)`
3. Verifica que el estudiante esté activo
4. Si todo OK: carga datos y avanza a dashboard
5. Si error: muestra mensaje de error específico

**Paso 2: Dashboard**
1. Muestra estadísticas de sesiones: total, completadas, respuestas, progreso promedio
2. Lista áreas de formación con:
   - Anillo de progreso (svg circular)
   - Contador: completadas/total
   - Distribución de dificultad (barras: fácil, medio, difícil)
   - Estado: "Completado" o "En progreso"
3. Botón "Guía de Participación"
4. Botón "Actualizar Áreas"
5. Cada tarjeta: "Continuar Diagnóstico" o "Ver Respuestas" si completado

**Paso 3: Wizard de Preguntas**
1. Header con: nombre del área, contador, progreso, botón "Ver Contestadas" y "Salir"
2. Pregunta actual con:
   - Número de pregunta
   - Dificultad (badge)
   - Peso/ponderación
   - Opciones según tipo (multiple/scale/open)
3. Navegación: Anterior / Siguiente (o Finalizar)
4. Validación en tiempo real via `getCanProceedProperty()`
5. Al llegar a la última pregunta sin responder: refresca las preguntas pendientes
6. Si todas respondidas: finaliza automáticamente

**Paso 4: Resumen**
1. Estadísticas: preguntas respondidas, progreso promedio, sesiones completadas
2. Mensaje de felicitaciones
3. Botones: "Ver Respuestas Detalladas", "Volver al Dashboard", "Nuevo Diagnóstico"

**Paso 5: Guía (opcional)**
1. 4 pestañas: Información General, Proceso Paso a Paso, Tipos de Preguntas, Consejos
2. Contenido informativo con ejemplos visuales

---

## 8. TIPOS DE PREGUNTA

### 8.1 `multiple` — Selección Múltiple

- Opciones predefinidas con `valor` numérico (1 = correcta, 0 = incorrecta)
- Se almacena `option_id` y `valor_numerico`
- UI: Radio buttons estilizados
- Validación: opción no vacía
- Precisión: `isCorrect()` compara `selectedOption.valor == 1`

### 8.2 `scale` — Escala Numérica

- Rango 1-10
- Se almacena el número como `respuesta` y `valor_numerico`
- UI: Círculos numerados del 1 al 10
- Validación: numérico entre 1 y 10

### 8.3 `open` — Texto Abierto

- Respuesta textual libre
- Se almacena en `respuesta`, `valor_numerico = 0`
- UI: Textarea
- Validación: mínimo 3 caracteres

---

## 9. PANEL DE ADMINISTRACIÓN

### 9.1 Vista `admin/diagnostico`

Jerarquía colapsable (Alpine.js `x-data="{ open: false }"`):

```
Plan de Estudio (accordion)
  ├── Grados (grid)
  │   ├── Área de Formación (pensum)
  │   │   ├── Nombre + Código
  │   │   ├── Cantidad de preguntas
  │   │   ├── Badge "Activo" (si aplica)
  │   │   └── Toggle Activar/Desactivar (individual)
  │   └── Botones masivos: "Activar Todo" / "Desactivar Todo" (por grado)
  └── Botones masivos: "Activar Todo" / "Desactivar Todo" (por plan de estudio)
```

### 9.2 Acciones Masivas

| Acción | Query | Efecto |
|---|---|---|
| `toggleAllPestudio(id, true)` | `Pensum::where('pestudio_id', $id)->update(['status_active_diagnostic' => true])` | Activa todas las áreas de un plan de estudio |
| `toggleAllPestudio(id, false)` | `Pensum::where('pestudio_id', $id)->update(['status_active_diagnostic' => false])` | Desactiva todas |
| `toggleAllGrado(pestudioId, gradoId, true/false)` | `Pensum::where('pestudio_id', $pestudioId)->where('grado_id', $gradoId)->update(...)` | Activa/desactiva por grado |
| `toggleStatus(pensumId)` | `$pensum->status_active_diagnostic = !$pensum->status_active_diagnostic; $pensum->save()` | Toggle individual |

---

## 10. MÉTRICAS Y PRECISIÓN

### 10.1 Cálculo de Precisión

```php
DiagAnswer::calculateStudentPrecision($estudiantId, $pensumId)
```

Fórmula: `precision = (100 * correct_answers) / total_answered`

- Filtra solo preguntas de tipo `multiple` (con `option_id` no nulo)
- Una respuesta es correcta si `selectedOption.valor == 1`
- Retorna: `{ precision, correct_answers, total_answered }`

### 10.2 Estadísticas de Sesión

```php
DiagSession::where('estudiant_id', $id)->count();                // total_sessions
DiagSession::where('estudiant_id', $id)->whereNotNull('completado_at')->count(); // completed_sessions
DiagAnswer::where('estudiant_id', $id)->count();                 // total_answers
DiagSession::where('estudiant_id', $id)->avg('progreso');        // average_progress
```

---

## 11. SEGURIDAD Y CONTROL DE ACCESO

### 11.1 Roles

| Rol | Acceso admin/diagnostico | Acceso /diagnostico |
|---|---|---|
| **Admin** | ✅ Panel de activación de áreas | ✅ (ruta pública) |
| **Diagnostic** | ✅ Panel de activación de áreas | ✅ (ruta pública) |
| **Standard** | ❌ Redirección/403 | ✅ (ruta pública, sin autenticación) |

### 11.2 Middleware

| Middleware | Protege | Verificación |
|---|---|---|
| `auth` | Grupo `admin.*` | Usuario autenticado |
| `isAdminOrDiagnostic` | Subgrupo admin (voting, diagnostic, educational) | `User::isAdminOrDiagnostic()` = `$this->is_admin || $this->is_diagnostic` |

### 11.3 Acceso público

La ruta `/diagnostico` es **completamente pública**. No requiere autenticación. El acceso a los datos del estudiante se controla por cédula (sin contraseña ni token).

---

## 12. CASOS DE USO

### CU-01: Activar Áreas para Diagnóstico (Admin)
1. Admin accede a `/admin/diagnostico`
2. Visualiza planes de estudio con sus grados y áreas
3. Expande un plan de estudio
4. Activa áreas específicas (toggle individual) o masivamente
5. Las áreas activas tienen `status_active_diagnostic = true`
6. Solo las áreas activas aparecerán en el diagnóstico del estudiante

### CU-02: Realizar Diagnóstico (Estudiante)
1. Estudiante accede a `/diagnostico`
2. Ingresa su cédula
3. Sistema verifica y carga sus áreas disponibles
4. Estudiante selecciona un área de formación
5. Responde preguntas (multiple/scale/open)
6. Sistema guarda respuestas automáticamente
7. Al completar todas las preguntas, ve resumen
8. Puede revisar respuestas detalladas

### CU-03: Reanudar Diagnóstico
1. Estudiante ingresa al sistema
2. Dashboard muestra progreso por área
3. Áreas con progreso muestran "En progreso"
4. Al seleccionar, se cargan las preguntas pendientes (no respondidas)
5. Las preguntas ya respondidas quedan registradas

### CU-04: Revisar Respuestas
1. Desde el dashboard, hace clic en "Ver Respuestas" (área completada)
2. Modal muestra todas las preguntas con respuestas
3. Para multiple choice: muestra la opción seleccionada destacada
4. Para scale: muestra valor numérico + barra de progreso
5. Para open: muestra texto de la respuesta
6. Muestra fecha/hora de cada respuesta

### CU-05: Alternar Vistas de Preguntas (Wizard)
1. Durante el wizard, el estudiante puede alternar entre preguntas pendientes y respondidas
2. Botón "Ver Contestadas" muestra preguntas ya respondidas en modo solo lectura
3. Permite revisión sin modificar respuestas

### CU-06: Consultar Guía de Participación
1. Desde el dashboard, estudiante hace clic en "Guía de Participación"
2. Visualiza información general, proceso paso a paso, tipos de preguntas, consejos
3. Puede volver al dashboard en cualquier momento

---

## 13. DIRECTORIO COMPLETO DE ARCHIVOS

### 13.1 Modelos
```
app/Models/app/Instrument/
├── DiagQuestion.php
├── DiagOption.php
├── DiagSession.php
└── DiagAnswer.php
```

### 13.2 Modelos Relacionados
```
app/Models/app/Academy/
├── Pensum.php
├── Pestudio.php
├── Peducativo.php
├── Asignatura.php
├── Grado.php
├── Seccion.php
└── Pevaluacion.php

app/Models/app/Learner/
└── Estudiant.php
```

### 13.3 Migraciones
No se encontraron migraciones específicas en `database/migrations/` (posiblemente en respaldos no indexados o creadas en entornos anteriores). Las tablas existen en la BD.

### 13.4 Middleware
```
app/Http/Middleware/
├── IsAdminOrDiagnostic.php
└── IsDiagnostic.php
```

### 13.5 Livewire Components (2)
```
app/Livewire/
├── Diagnostic.php                                     → Componente público (wizard 5 vistas)
└── Admin/Diagnostic/IndexComponent.php                → Admin: activación de áreas
```

### 13.6 Vistas Blade (8 archivos)
```
resources/views/
├── diagnostico.blade.php                              → Ruta pública
├── layouts/diagnostic.blade.php                       → Layout personalizado
├── livewire/diagnostic.blade.php                      → Livewire maestro
├── livewire/diagnostic/
│   ├── student-identification.blade.php               → Formulario de cédula
│   ├── dashboard.blade.php                            → Panel de áreas
│   ├── wizard.blade.php                               → Responder preguntas
│   ├── summary.blade.php                              → Resumen final
│   └── guide.blade.php                                → Guía (4 tabs, ~430 líneas)
└── livewire/admin/diagnostic/
    └── index-component.blade.php                      → Admin (acordeones)
```

### 13.7 Controlador (parcial)
```
app/Http/Controllers/HomeController.php
  └── método diagnostico() → return view('diagnostico')
```

---

## 14. OBSERVACIONES TÉCNICAS

### 14.1 Patrón de Diseño

| Patrón | Uso |
|---|---|
| Wizard / State Machine | `$currentView` controla 5 estados del componente |
| Active Record | Modelos Eloquent con lógica de negocio (isCorrect(), calculatePrecision) |
| Single-Component Architecture | Un único Livewire gigante maneja todo el flujo |
| Legacy Listeners | Usa `$listeners` (Livewire v2 style) en lugar de `#[On]` attributes |

### 14.2 Particularidades

- **No hay migraciones visibles** en `database/migrations/` — las tablas `diag_*` existen en BD pero sin archivo de migración en el repositorio
- **El componente `Diagnostic` es extenso** (~657 líneas) con toda la lógica del wizard en un solo archivo
- **No usa WebSockets ni eventos broadcast** — todo es petición/respuesta
- **El componente NO está decorado con `#[Layout]`** — el layout se define en la vista blade padre (`diagnostico.blade.php` extiende `layouts.diagnostic`)
- **Validación condicional**: Las reglas `rules()` cambian según `$currentView`
- **Anti-doble clic**: Usa flag `$isProcessing` + `wire:loading.attr="disabled"`
- **Logging**: Usa `Log::info()` para debug de `canProceed` y `selectedAnswer`
- **Transacciones DB**: Las operaciones críticas (`startDiagnostic`, `saveAnswer`, `finishDiagnostic`) usan `DB::beginTransaction/commit/rollBack`
- **Actualización del progreso en BD**: Cada vez que se guarda una respuesta, se recalcula y persiste `progreso` en `diag_sessions`
- **Persistencia de sesión**: Al iniciar un diagnóstico, usa `firstOrCreate` para recuperar sesiones existentes o crear nuevas

### 14.3 Campos faltantes vs migraciones

No hay archivos de migración para las tablas `diag_questions`, `diag_options`, `diag_sessions`, `diag_answers`. La estructura se infiere de los modelos y el uso en código.

### 14.4 Dependencias externas

- `wireui/wireui` — Notificaciones (éxito, error)
- `livewire/livewire` (v3) — Framework de componentes

---

*Documentación generada el 2026-06-06 para el módulo admin/diagnostico*
