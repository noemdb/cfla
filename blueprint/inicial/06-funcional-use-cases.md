# 06 — Especificación funcional (casos de uso documentados en el legacy)

> **Fuente:** `saefl/s2526/resources/views/inicials/partials/use-cases/` — la "documentación viva" del módulo, renderizada en `/app/inicials/use-cases` (ruta `inicials.use-cases`, controlador `HomeInicialController@useCases` → `getUseCasesData`). 7 parciales grandes con diagramas Mermaid.
> Transcripción fiel: todo lo aquí contenido proviene de las vistas del legacy.

## 0. Contexto y navegación

La página "Casos de Uso del Módulo" presenta *"los diagramas de casos de uso más importantes del sistema, mostrando las interacciones entre los usuarios y las funcionalidades principales para la planificación, gestión y evaluación de actividades pedagógicas"*.

**Pestañas de navegación (exactamente 7):** Resumen (`#overview`) · Autenticación (`#authentication`) · Planificación (`#weekly-planning`) · Proyectos (`#classroom-projects`) · Evaluaciones (`#evaluations`) · Informes (`#pedagogical-reports`) · Exportar (`#export-print`).

La pestaña Resumen muestra tarjetas de `$useCases` (id, icon, title, description, color) con badge "Ver Diagrama", navegables vía `data-target="#{id}"`.

### Hallazgos estructurales

1. `authentication.blade.php` líneas 69–566: **todo el detalle está dentro de un comentario Blade `{{-- --}}`** — existe pero no se renderiza.
2. `weekly-planning.blade.php` líneas 453–523: la sección "Métodos del Sistema" también está comentada.
3. `classroom-projects.blade.php` líneas 218–1002: **segundo `tab-pane` anidado duplicado con el mismo `id`** — dos capas documentales del mismo caso de uso.
4. `special-reports.blade.php` **NO tiene pestaña** en navigation (aunque el archivo existe y es alcanzable desde las tarjetas del resumen).
5. `Eiplanningbwk` (Planificación Quincenal) se menciona **únicamente en export-print** como documento exportable — no tiene caso de uso propio.

---

## 1. Autenticación de Usuarios (`authentication.blade.php`, 569 líneas)

**Propósito:** autenticación segura de docentes del módulo: validación de credenciales, verificación de permisos de educación inicial, gestión de sesiones, RBAC.

**Funcionalidades:** inicio de sesión seguro · verificación de permisos · gestión automática de sesiones con timeout configurable · registro de actividad y auditoría · recuperación de contraseñas · cambio de contraseñas con políticas · cierre de sesión seguro.

**Actores:** Docente, Sistema, Middleware, Base de Datos. **Modelos:** User, Autoridad, Profesor, Session. **Middleware:** `auth`, `is_inicial`, `throttle`.

### Flujo principal (inicio de sesión, 6 pasos)

1. **Acceso** (URL del sistema) → 2. **Credenciales** (usuario/contraseña) → 3. **Validación** (BD) → 4. **Permisos** (rol y área) → 5. **Sesión** (creación segura) → 6. **Dashboard**.

**Sub-flujos:** Recuperar Contraseña (Solicitar → Validar Email → Enviar Token → Restablecer) · Cambiar Contraseña (Verificar Actual → Validar Nueva → Aplicar Políticas → Actualizar) · Cerrar Sesión (Invalidar → Limpiar Cookies → Registrar Logout → Redirigir) · Ver Perfil.

### Reglas de negocio

**Middleware `is_inicial`** (código documentado):
```php
if (!Auth::check()) { return redirect()->route('login'); }
$user = Auth::user();
if ($user->area !== 'Educación Inicial') { abort(403, 'Acceso denegado'); }
$autoridad = Autoridad::where('user_id', $user->id)->first();
if (!$autoridad) { abort(403, 'Sin autorización'); }
```
Verifica: Área del Usuario = "Educación Inicial" · Rol Activo · Autoridad Asociada · Estado del Usuario.

**Políticas de contraseña:** mínimo 8 caracteres, una mayúscula, un número, especiales recomendados, no reutilizar las últimas 3. **Token de recuperación:** validez 60 min, uso único, hash de 64 caracteres, vinculado al email.

**Constructor documentado de `HomeInicialController`** (middleware inline):
```php
$this->middleware(['auth','is_inicial', function ($request, $next) {
    $this->user = User::find(Auth::id());
    $this->autoridad = Autoridad::where('user_id',Auth::id())->first();
    $this->list_comment_autoridad = Autoridad::COLUMN_COMMENTS;
    return $next($request);
}]);
```

### Errores y requisitos no funcionales

**Casos de error:** Credenciales Inválidas · Usuario Inactivo · Permisos Insuficientes · Sesión Expirada · Múltiples Intentos (bloqueo temporal). Mensajes genéricos para no exponer información sensible.

**Sesiones:** 120 minutos de inactividad · driver BD · AES-256-CBC · cookies HttpOnly/Secure/SameSite · limpieza horaria. **Seguridad:** throttle 5 intentos/min · regeneración de ID de sesión · CSRF · XSS sanitización · consultas preparadas.

**Auditoría:** registro de accesos · log de intentos fallidos · seguimiento de cambios de contraseña · alertas de actividad sospechosa.

---

## 2. Gestión de Planificación Semanal (`weekly-planning.blade.php`, 1,230 líneas)

**Propósito:** crear, gestionar y mantener planificaciones semanales detalladas, con estrategias pedagógicas diarias, vinculación con proyectos de aula y resúmenes por áreas, siguiendo la rutina diaria de educación inicial "desde el recibimiento hasta la despedida".

**Actores:** Docente (creador/gestor), Coordinador (supervisor/revisor), Sistema, BD. **Modelos:** Eiplanningwk, Eiplanningwstrategy, Eiplanningwsummary, Eiprojectk, Pevaluacion.

**Casos de uso del diagrama:** Crear · Editar · Eliminar · Consultar · Exportar a PDF · Duplicar · Validar Completitud.

### Flujo de creación (6 pasos)

1. **Inicio** (acceso al módulo) → 2. **Información** (grado/sección asignada; fechas; tiempo de ejecución **calculado automáticamente**; diagnóstico inicial; observaciones) → 3. **Vinculación** con proyecto de aula (activo; coherencia temporal; alineación de objetivos) → 4. **Estrategias** por día (momento de rutina; estrategia por día; recursos; adaptación al grupo; orden lógico) → 5. **Resúmenes** por área (área; componente; objetivos; aprendizajes esperados; indicadores) → 6. **Finalización** (validar y guardar).

### Rutina diaria (10 momentos — `LIST_MOMENT`)

- **Iniciales:** Recibimiento (bienvenida/adaptación) · Momento Cívico (actividades patrias/valores) · Aseo-Desayuno-Aseo (higiene/alimentación)
- **Trabajo:** Planificación (los niños eligen y planifican) · Trabajo Libre (ejecución) · Orden y Limpieza · Intercambio y Recuento (socialización)
- **Dirigidos:** Trabajos en Pequeños Grupos (focalizadas) · Actividades Colectivas
- **Cierre:** Despedida

### Reglas de negocio y validaciones

- Fechas coherentes (inicio < final) · Período mínimo 1 semana · Grado/sección asignados al docente · **Diagnóstico obligatorio ≥ 50 caracteres** · Proyecto vinculado activo y compatible
- Estrategias: momento de lista predefinida · **al menos 3 días con contenido** · coherencia pedagógica · orden lógico
- Resúmenes: área válida y activa · componente relacionado · objetivo claro y medible · indicadores específicos y observables

**Funcionalidades avanzadas documentadas:** duplicación (uso como plantilla) · autocompletado (basado en historial) · **validación en tiempo real** · **guardado automático**.

**Filtros de consulta:** por Profesor · por Período (rango) · por Grado/Sección · por Proyecto.

### Datos y ejemplo documentado

Estructura: Eiplanningwk (profesor_id, grado_id, seccion_id, eiprojectk_id, finicial, ffinal, tiempo_ejecucion, diagnostico, observacion) · Eiplanningwstrategy (momento_rutina_diaria, lunes…viernes, order) · Eiplanningwsummary (pevaluacion_id, componente, objetivo, aprendizaje_esperado, indicadores, linea_investigacion, enfasis_curriculares, order).

**Ejemplo "Explorando los Colores"** (Maternal 3–4 años, Sección A, proyecto "Mi Mundo de Colores y Formas", 11–15/03/2024, 1 semana): diagnóstico de curiosidad por los colores; estrategias por día/momento; resúmenes por área (Formación Personal y Social — Identidad y Género; Comunicación y Lenguaje — Lenguaje Oral; Relación con el Ambiente — Procesos Matemáticos; Educación Estética — Expresión Plástica).

**Integración:** Proyectos de Aula (coherencia de objetivos, alineación temporal) · Sistema de Evaluación (vinculación con Pevaluacion, indicadores, logros) · Informes Pedagógicos (resumen de planificación ejecutada, evidencias).

---

## 3. Gestión de Proyectos de Aula (`classroom-projects.blade.php`, 1,005 líneas, doble capa)

**Propósito (capa extendida):** crear, desarrollar y gestionar proyectos de aula integrales como **eje articulador del proceso educativo**, partiendo de los intereses y necesidades de los niños, mediante investigación, exploración y construcción colectiva.

**Actores (capa extendida):** Docente (facilitador) · **Niños (protagonistas)** · **Familias (colaboradores)** · Coordinador · **Comunidad (fuente de recursos)**. **Modelos:** Eiprojectk, Eiprojectsummary, Eiprojectreview, Pevaluacion.

### Flujo extendido de creación

- **Definir Información General:** grado/sección · título · período · **justificación** · línea de investigación
- **Establecer Objetivos:** general · específicos · vincular con áreas · competencias
- **Planificar Fases:** **Inicio–Diagnóstico · Desarrollo–Investigación · Cierre–Socialización** · cronograma
- **Definir Recursos:** humanos · materiales · tecnológicos · del entorno
- **Criterios de Evaluación** → **Guardar y Activar**

**Otros casos:** Editar · Eliminar · Consultar (filtros por profesor/período/estado; ver activos; historial) · Duplicar · Generar Informe · Evaluar Impacto. Participación de niños/familias: diagnosticar, aportar ideas, colaborar, proporcionar recursos.

### Reglas de negocio (validaciones con checkmarks)

- **Título único por docente y período** · **Duración mínima 3 semanas** · **máxima 6 semanas** · fechas coherentes · **justificación ≥ 100 caracteres** · objetivo general obligatorio · **≥ 3 objetivos específicos** · **≥ 2 áreas de aprendizaje** · **≥ 3 fases de desarrollo** · grado/sección asignados.

### Ejemplo documentado "Los Animales de Mi Comunidad" (Preescolar 4–5, Sección B, 01–26/04/2024, 4 semanas)

- Justificación: interés por animales del entorno; preguntas sobre dónde viven/qué comen; sistematizar; respeto por seres vivos; cuidado ambiental.
- Línea: "Conocimiento y cuidado del ambiente natural comunitario". 5 objetivos específicos (identificar/clasificar; investigar necesidades; desarrollar respeto; fortalecer observación-registro-comunicación; participación familiar).
- **3 fases:** Inicio–Diagnóstico (semana 1: conversatorio, recorrido, registro gráfico, lluvia de ideas, carta a familias) · Desarrollo–Investigación (semanas 2–3: investigación familiar, visita veterinario, fichas informativas, hábitats reciclados, dramatizaciones, normas) · Cierre–Socialización (semana 4: exposición "Nuestros Amigos Animales", presentación a otros grupos, "Feria de Mascotas Responsables", evaluación grupal, compromiso grupal).
- 4 áreas: Relación con el Ambiente · Comunicación y Lenguaje · Formación Personal y Social · Educación Estética (cada una con componentes).
- Recursos humanos/materiales/entorno + evaluación (criterios, instrumentos: observación directa, registro anecdótico, portafolio, lista de cotejo, autoevaluación grupal; indicadores de logro) + cronograma semanal con responsables.

---

## 4. Gestión de Evaluaciones (`evaluations.blade.php`, 313 líneas)

**Propósito:** registrar actividades de evaluación y seguimiento del desempeño, incluyendo planes de evaluación, actividades evaluativas específicas y control de asistencia.

**Actores:** Docente, Sistema de Evaluación (valida completitud, calcula estadísticas, genera alertas). **Modelos:** Eievaluationk, Eievaluationp, Pevaluacion, Lapso.

### Flujo general (4 pasos)

1. **Planificación** (crear el plan: período, fechas, objetivos) → 2. **Registro** (documentar actividades evaluativas) → 3. **Seguimiento** (monitorear progreso, ajustar) → 4. **Reporte** (informes para análisis).

**Casos de uso:** Crear Plan (seleccionar lapso; definir fechas; observaciones; asistencia) · Registrar Actividad Evaluativa (fecha; nombres de niños; aprendizaje alcanzado; indicadores; **instrumento**) · Editar · Consultar (filtrar por área/estudiante/período) · Generar Reporte.

### Instrumentos de evaluación documentados

Observación Directa · Lista de Cotejo · Escala de Estimación · Registro Anecdótico · Portafolio · Entrevista.

### Reglas de negocio

Fechas coherentes · lapso activo · área vinculada válida (Pevaluacion) · campos obligatorios completos · permisos del docente para la sección · orden lógico de actividades.

**Integración:** Proyectos de Aula (medir logro de objetivos) · Planificación Semanal (coherencia curricular) · Informes Pedagógicos (resultados alimentan los informes finales).

---

## 5. Generación de Informes Pedagógicos (`pedagogical-reports.blade.php`, 473 líneas)

**Propósito:** generar **informes finales individualizados** por estudiante, documentando logros, observaciones socioafectivas, participación familiar y recomendaciones, integrando expectativas de aprendizaje con evaluaciones específicas.

**Actores:** Docente, Sistema de Informes (valida completitud, formatea documento, genera PDF oficial). **Modelos:** Eifinalk, Eilearningexpectation, Eilearningarea, Pevaluacion, Estudiant.

### Flujo (6 pasos)

1. **Selección** (estudiante y período evaluativo) → 2. **Información** (datos generales y contexto del grupo) → 3. **Expectativas** (vincular áreas de aprendizaje) → 4. **Logros** (documentar avances) → 5. **Observaciones** (individuales y participación familiar) → 6. **Conclusiones** (reflexiones y recomendaciones).

**Descomposición de "Crear Informe Final":** Seleccionar Estudiante · Título · Agregar Contexto del Grupo → **Vincular Expectativas** (seleccionar área; asociar con evaluación) · Describir Planificación Ejecutada · Documentar Proyecto Destacado · Registrar Actividades Especiales · Describir Logros · Agregar Observaciones Individuales · Documentar Participación Familiar · Incluir Conclusiones · Agregar Recomendaciones.

### Reglas de negocio

Estudiante válido y **de la sección** · lapso vigente · **≥ 1 expectativa vinculada** · título, contexto y conclusiones obligatorios · permisos docente · coherencia temporal.

### Áreas y observaciones documentadas

- **Áreas:** Formación Personal y Social (identidad y autonomía; convivencia; valores, normas, derechos y deberes; autoestima) · Comunicación y Lenguaje (lenguaje oral; escrito; expresión plástica/corporal/musical) · Relación con el Ambiente (tecnología y calidad de vida; ambiente; relaciones espaciales/temporales; matemática).
- **Observaciones socioafectivas:** interacción social, expresión emocional, autonomía, autoestima. **Cognitivas:** atención, memoria, resolución de problemas, creatividad.
- **Participación familiar:** asistencia a reuniones, apoyo en casa, comunicación, actividades especiales. **Recomendaciones a la familia:** estrategias de apoyo, recursos sugeridos, seguimiento, próximos pasos.

### Datos (sistema de expectativas)

**Estructura del informe:** Información General (título, estudiante, período, docente, sección) · Contexto Educativo (contexto del grupo, planificación ejecutada, proyecto destacado, actividades especiales) · Desarrollo Individual (logros, observaciones individuales, participación familiar, expectativas) · Conclusiones y Proyección (conclusiones, recomendaciones, orden).

**Pivote `eifinalk_expectation`:** eifinalk_id, eilearningexpectation_id, eilearningarea_id, pevaluacion_id — many-to-many con área y evaluación explícitas.

---

## 6. Administración de Informes Especiales (`special-reports.blade.php`, 429 líneas — sin pestaña)

**Propósito:** gestionar **eventos o situaciones particulares que requieren atención especial**, con planes específicos y actividades diferenciadas.

**Actores:** Docente, Sistema de Planes Especiales (valida justificación, ordena actividades, genera documentación). **Modelos:** Eispecialk, Eispecialact, Pevaluacion, Grado, Seccion.

### Flujo de creación (6 pasos)

1. **Identificación** (detectar la necesidad) → 2. **Justificación** (razones y fundamentos) → 3. **Planificación** (tiempos y recursos) → 4. **Actividades** (diseñar específicas) → 5. **Ejecución** (implementar) → 6. **Evaluación** (valorar resultados).

### Situaciones que requieren planes especiales

- **Necesidades Educativas Especiales:** discapacidades; dificultades de aprendizaje; trastornos del desarrollo; adaptaciones curriculares.
- **Eventos Extraordinarios:** emergencias sanitarias; situaciones climáticas; eventos comunitarios; celebraciones.
- **Programas Especializados:** talleres temáticos; proyectos de investigación; refuerzo; enriquecimiento.

### Reglas de negocio

**Justificación obligatoria y detallada** · fechas coherentes · tiempo realista · **≥ 1 actividad por área** · permisos docente · orden lógico. Características: flexibilidad, personalización, temporalidad limitada, justificación sólida, seguimiento continuo, documentación detallada.

**Ejemplos documentados:** Plan de Emergencia Sanitaria (brote contagioso; higiene; 2–3 semanas) · Apoyo a Estudiante con NEE (adaptaciones curriculares; todo el período) · Proyecto Ambiental (evento climático; huerto, reciclaje, agua; 4–6 semanas).

---

## 7. Exportación e Impresión de Formatos (`export-print.blade.php`, 460 líneas)

**Propósito:** descargar/imprimir **documentos PDF listos para presentación oficial** — estandarizados con formato profesional, encabezados institucionales, contenido estructurado y elementos de validación oficial.

### Flujo (6 pasos)

1. **Selección** del documento → 2. **Validación** (completitud de datos) → 3. **Configuración** (formato y opciones) → 4. **Generación** (PDF con formato oficial) → 5. **Vista Previa** → 6. **Descarga/Impresión**.

### Documentos disponibles (6)

| Documento | Modelo | Descripción |
|---|---|---|
| Planificación Semanal | Eiplanningwk | Estrategias diarias, áreas y vinculación con proyectos |
| Planificación Quincenal | **Eiplanningbwk** | "Formato bimensual con resumen de actividades y componentes curriculares" *(única mención en toda la doc.)* |
| Proyecto de Aula | Eiprojectk | Diagnóstico, revisión y áreas |
| Plan de Evaluación | Eievaluationk | Actividades evaluativas con indicadores y observaciones |
| Plan Especial | Eispecialk | Situaciones particulares con justificación y actividades |
| Informe Pedagógico | Eifinalk | Informe final individual con logros, observaciones y recomendaciones |

**Configuración de página:** tamaño A4/Carta/Legal · orientación V/H · márgenes (normal/estrecho/amplio/personalizado). **Elementos:** encabezado institucional · pie (fecha, página) · marca de agua opcional. **Impresión:** impresora, calidad, color, páginas, copias, intercalado.

### Validaciones y calidad

Datos completos · fechas válidas · relaciones correctas · permisos de exportación · integridad · plantillas institucionales. Controles: vista previa, formato consistente, legibilidad, elementos gráficos, paginación, metadatos.

**Destinatarios documentados:** Presentación a Directivos (encabezados oficiales, alta resolución, logos/sellos/firmas) · Entrega a Padres (simplificado, terminología accesible) · Archivo Institucional (**PDF/A**, metadatos completos, nomenclatura estandarizada).

**Tecnologías documentadas:** DomPDF, Blade, Storage, Response (backend) · Print.js, FileSaver.js, PDF.js, Bootstrap (frontend) · PDF/A, protección contra modificación, metadatos, accesibilidad.

---

## Resumen consolidado

### Modelos por caso de uso

| Modelo | Casos de uso | Rol |
|---|---|---|
| User, Autoridad, Profesor, Session | 1 | Autenticación |
| Eiplanningwk / Eiplanningwstrategy / Eiplanningwsummary | 2, 7 | Planificación semanal |
| Eiplanningbwk | 7 (solo aquí) | Planificación quincenal |
| Eiprojectk / Eiprojectsummary / Eiprojectreview | 2, 3, 7 | Proyecto de aula |
| Eievaluationk / Eievaluationp | 4, 7 | Evaluación |
| Eifinalk + Eilearningarea / Eilearningexpectation + pivote | 5, 7 | Informe final |
| Eispecialk / Eispecialact | 6, 7 | Plan especial |
| Pevaluacion | 2–7 (transversal) | Áreas de aprendizaje |
| Lapso, Grado, Seccion, Estudiant | 2–7 | Estructura académica |

### Patrones transversales

- **Ordenamiento** de detalles por `order` NULLS-LAST + `created_at`.
- **Métodos utilitarios repetidos por cabecera:** `getPevaluacions($profesor_id, $lapso_id)` / `getPevaluacionsList(...)`.
- **Vinculación con proyecto:** planificación ↔ proyecto activo y compatible con coherencia temporal.
- **Permisos:** grado/sección asignado al docente, validado en todos los CRUD.
- **Flujo documental:** Planificación (semanal/quincenal) → Proyecto de aula → Evaluaciones → Informes/Planes especiales → Exportación PDF oficial, con integración explícita entre cada etapa.

> ⚠️ **Nota de veracidad:** estas reglas están **documentadas** en las vistas use-cases del legacy, pero el código real no siempre las implementa (p. ej. el diagnóstico `min:50` documentado vs `min:10` real en `EiplanningwkComponent`; el `after_or_equal` sí existe; la "validación ≥ 3 días con estrategias" y "≥ 2 áreas" no constan en los traits de validación — ver 03-livewire-componentes.md). El blueprint de migración debe decidir qué reglas se adoptan.
