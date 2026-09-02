# Comparativa objetiva y propuesta de coexistencia — asistescolar.com vs. Módulo de Planificación (SAEFL)

> **Fuentes de verdad**
> - **SAE (asistescolar.com)**: `blueprint/otherSystemReferenc/analisisyValidacion.md` (dictamen de validación). Solo se usan sus veredictos: ✅ confirmado, ⚠️ declarado por el proveedor sin detalle técnico, ❌ no evidenciado públicamente (≠ inexistente).
> - **Módulo de Planificación / LMS SAEFL**: blueprints del proyecto — `blueprint/lesson/saefl_lms_schema.sql`, `blueprint/lesson/RulesStatusLesson.md`, `blueprint/lesson/Spec LMS - Completions and Extensions.md`, `blueprint/flow/diagrama-activity-lesson-planning.md`, `blueprint/director/implementations.md`, `blueprint/lms/*`. Marcado como 📐 documentado en esquema/specs.
>
> **Alcance**: solo dimensiones con sentido de comparación — Estudiante, Profesor, Control de Estudio, Planificación Docente, LMS/Aula Virtual.

---

## 1. Resumen ejecutivo

Los dos sistemas **no compiten en todo**: son complementarios con **una zona de solapamiento real**.

- SAE es fuerte donde este proyecto no llega hoy: el **núcleo académico regulado** (notas, boletines MPPE, planillas, títulos) y el **alcance familiar** (app móvil, portal representante).
- El Módulo de Planificación es fuerte donde SAE solo declara capacidades genéricas: **planificación como entidad estructurada**, **publicación gobernanza** (aprobación, programación automática, monitores multi-rol, auditoría) y **lecciones ricas** (secciones tipificadas, Mermaid/KaTeX, slideshow estudiante).
- El solapamiento es el **aula virtual**: ambos ofrecen contenidos al estudiante, tareas y feedback. Sin una política clara, el docente termina **cargando contenido dos veces** → dualidad de funciones.

---

## 2. Comparativa por dimensión

### 2.1 Estudiante

| Capacidad | SAE (asistescolar.com) | Evidencia SAE | Planificación SAEFL | Evidencia SAEFL |
|---|---|---|---|---|
| Consulta de lecciones/contenidos publicados | ✅ recursos educativos, clases online vía calendario | analisisyValidacion §Aula virtual | 📐 vista estudiante tipo slideshow/swiper, solo lecciones de su sección y con 6 candados | `vista-estudiante-slideshow-swiper.md`, flow diagram §5 |
| Tareas pendientes/entregadas con detalle y feedback del docente | ✅ confirmado (info, calificación, comentario, tiempo restante) | analisisyValidacion §Aula virtual | 📐 assignments/submissions + comentarios por actividad | `saefl_lms_schema.sql` |
| Notas oficiales (evaluación continua + boletines) | ✅ núcleo del producto | analisisyValidacion §Confirmadas | ❌ fuera de alcance (lo cubre SAE en la propuesta) | — |
| Progreso/completions trazables | ❌ no evidenciado públicamente | analisisyValidacion §Límites | 📐 activity_progress, content_progress, completions | `saefl_lms_schema.sql`, Spec Completions |
| App móvil nativa | ⚠️ declarada iOS/Android; disponibilidad a validar por SO/versión | analisisyValidacion §Ajustes | ❌ web responsive (Livewire/Tailwind) | stack del proyecto |
| Incidencias | ✅ módulo reciente | sae-representante-detallado §3.5 | ❌ no contemplado | — |

### 2.2 Profesor (docente)

| Capacidad | SAE | Evidencia SAE | Planificación SAEFL | Evidencia SAEFL |
|---|---|---|---|---|
| Carga de contenidos al aula virtual | ✅ carga de contenidos, recepción/corrección de tareas, feedback directo | analisisyValidacion §Confirmadas | 📐 wizard de lecciones: secciones tipificadas, recursos, enlaces, embeds HTML, Mermaid/KaTeX | `docWizardLesson.md`, schema |
| Planificación académica estructurada | ⚠️ planificadores de lapso, cronogramas, distribución de contenidos — declarado sin detalle funcional | analisisyValidacion §Planificación | 📐 jerarquía `Pestudio → Pensum → Pevaluación → Activity`; actividad con estados y aprobación formal | flow diagram §3.1 |
| Flujo de aprobación/supervisión del plan | ❌ no evidenciado públicamente | analisisyValidacion §Límites | 📐 Candado 1 (Jefe de Área/Planning aprueba), publicación por responsable; nunca auto-publicación | flow diagram §5 |
| Monitores de seguimiento multi-rol | ❌ no evidenciado | ídem | 📐 LmsMonitor (Planning), LessonMonitor (Liderazgo scoped), LessonList (Dirección global, Coordinación scoped), impresión Ver/Imprimir | `director/implementations.md` 2.7–2.7.7 |
| Auditoría de actividad | ❌ no evidenciado | analisisyValidacion §Límites | 📐 activity_logs / ActivityAudit | Spec LMS Completions |
| Asistencia diaria con notificación al representante | ✅ confirmado (SAE WEB) | analisisyValidacion §Confirmadas | ◐ asistencia por actividad en esquema (`activity_attendances`) — alcance pedagógico, no oficial | `saefl_lms_schema.sql` |
| Chats con representantes / foros | ✅ confirmado | analisisyValidacion §Confirmadas | ❌ no existe | — |

### 2.3 Control de Estudio (núcleo regulado)

| Capacidad | SAE | Planificación SAEFL |
|---|---|---|
| Planillas MPPE, resúmenes finales 01–20/NC/P/Art.109/IN | ✅ confirmado | ❌ no aplica (módulo distinto) |
| Títulos, Acta de Revisión de Documentos, constancias, certificaciones | ✅ confirmado | ❌ no aplica |
| Históricos vigentes/derogados + conversión (literal: «notas históricas a planes derogados») | ✅ con corrección de alcance | ❌ no aplica |
| Prosecución automática, escolaridad regulares/repitientes | ✅ confirmado | ✅ prosecución existe en cfla (`Estudiant` + trait `Prosecucions`) — pero notas/boletines no |
| Estadística institucional (inscritos, repitientes, demografía, honor) | ✅ confirmado | ⚠️ parcial en cfla |

**Conclusión de la dimensión**: el control de estudio es territorio exclusivo de SAE en la propuesta; el módulo de planificación no debe intentar replicarlo.

### 2.4 Planificación Docente

| Capacidad | SAE | Planificación SAEFL |
|---|---|---|
| Plan de evaluación (formato visible a estudiantes/familias) | ⚠️ planes de evaluación declarados | 📐 formato Plan de Evaluación y Actividades dentro del módulo |
| Estructura por lapso/asignatura/sección | ⚠️ declarada genérica | 📐 primera clase: lapso, sección, asignatura, docente (carga académica) |
| Versionado/aprobación del plan | ❌ no evidenciado | 📐 estados + aprobación Candado 1 + auditoría |
| Publicación programada del contenido derivado | ❌ no evidenciado | 📐 SCHEDULED → PUBLISHED automático, con despublicación opcional |
| Visibilidad condicionada (6 candados) | ❌ no evidenciado | 📐 aprobación, visibilidad, publicada, fecha, no expirado, sección |

### 2.5 LMS / Aula Virtual

Ver diagrama comparativo: [`comparativo-aulas-virtuales.html`](comparativo-aulas-virtuales.html). Síntesis:

| Bloque | SAE | Planificación SAEFL |
|---|---|---|
| Contenidos estructurados ricos | ⚠️ genérico | 📐 secciones, tipos de contenido, embeds, Mermaid/KaTeX, slideshow |
| Tareas + feedback | ✅ | 📐 |
| Evaluaciones interactivas autocalificables | ❌ no evidenciado | 📐 assessments/questions/attempts (esquema) |
| Publicación programada + gobernanza + auditoría | ❌ no evidenciado | 📐 |
| Notificaciones | ✅ push nativas app | 📐 tiempo real web (Reverb/Echo) + dropdown DB |
| App nativa / chat / incidencias | ✅ / ✅ / ✅ | ❌ / ❌ / ❌ |

---

## 3. Lectura del resultado: la dualidad

La **zona de dualidad** son las funciones que hoy existen (o podrían existir) en ambos mundos:

1. Contenidos/publicaciones al estudiante
2. Tareas/asignaciones y corrección
3. Feedback docente
4. Recursos educativos y calendario/clases online
5. Plan de evaluación visible

Sin política explícita, cada función duplicada cuesta **doble carga docente**, doble mantenimiento y confusión para el estudiante sobre «dónde está mi clase». → Ver [`dualidad-aula-virtual.html`](dualidad-aula-virtual.html).

---

## 4. Conclusión

1. La comparación **no es SAE vs. SAEFL en general**: SAE gana en control de estudio regulado y alcance familiar; el Módulo de Planificación gana en gobernanza pedagógica, trazabilidad y riqueza de contenido.
2. El conflicto práctico se limita al **aula virtual**. Es también la oportunidad: el módulo ya cubre lo verificable de SAE y supera los límites documentados (publicación programada, aprobación, auditoría, progreso).
3. Coexistir es viable **si y solo si** cada función tiene un único hogar y una única audiencia canónica.

## 5. Recomendación de coexistencia (modelo propuesto)

### 5.1 Principio rector
> **Una sola fuente de verdad por tipo de dato; el docente produce cada cosa una sola vez.**

### 5.2 Reparto por actor

| Actor | Sistema | Funciones canónicas |
|---|---|---|
| **Estudiante** | **Módulo de Planificación** (= su Aula Invertida) | Lecciones publicadas, plan de evaluación visible, actividades/tareas, notificaciones pedagógicas |
| **Representante** | **SAE (asistescolar.com)** | Informe de notas/boletines, formatos de inscripción, constancias/solvencias, comunicación administrativa |
| **Docente** | **Ambos, con frontera explícita** | SAE: aspectos únicos de Control de Estudio (carga de notas, asistencia diaria oficial, reportes MPPE). Planificación: planificar, formatos (Plan de Evaluación y Actividades), lecciones, publicación, notificaciones, aula invertida |

### 5.3 Reglas anti-duplicidad

1. **Contenido pedagógico** se produce SOLO en Planificación. Nunca se suben lecciones/recursos a SAE.
2. **Notas oficiales** viven SOLO en SAE. Las evaluaciones interactivas del LMS son formativas; su resultado no reemplaza la nota oficial.
3. **Plan de Evaluación**: se define, valida y aprueba en Planificación (formato + Candado 1); SAE lo consume como referencia para registrar notas. Integración futura: exportar el plan aprobado (ponderaciones/fechas) hacia SAE para no recargarlo allí.
4. **Asistencia**: registro diario oficial en SAE (con notificación al representante); la asistencia por actividad del LMS queda como evidencia pedagógica opcional y no oficial.
5. **Comunicación**: notificaciones de publicación/tareas desde Planificación (Reverb); comunicados administrativos y de notas desde SAE. No construir chat propio mientras SAE lo provee.
6. **Identidad**: branding claro («Aula Invertida» dentro de la app escolar) para que el estudiante sepa dónde entrar; evaluar enlace SSO o deep-link desde el portal institucional más adelante.

### 5.4 Matriz rápida de decisión (¿dónde hago esto?)

| Necesidad | Hogar |
|---|---|
| Publicar la clase de mañana con material y video | Planificación (programada → automática) |
| Definir qué se evalúa y con qué peso en el lapso | Planificación (formato Plan de Evaluación) |
| Registrar la nota del examen | SAE (control de estudio) |
| Justificar inasistencia del estudiante | SAE (asistencia oficial) |
| Enviar constancia/solvencia al representante | SAE |
| Notificar al estudiante que hay nueva lección | Planificación (Reverb) |
| Auditar quién publicó qué y cuándo | Planificación (monitores/logs) |

### 5.5 Riesgos y mitigaciones

| Riesgo | Mitigación |
|---|---|
| Doble credencial/mundo para el docente | Sesión única del colegio + accesos directos cruzados; futuro SSO |
| Confusión del representante («¿dónde veo notas vs. tareas?») | Mensaje único: notas/documentos = SAE; clases/tareas = Aula Invertida |
| Deriva: alguien empieza a subir contenido a SAE | Regla 5.3-1 en el manual del docente + verificación en supervisión |
| Dependencia externa para notas | Exportaciones periódicas (Excel/PDF) como respaldo; migración futura evaluada contra el orden de prioridades del análisis |

---

*Documentos relacionados: `dualidad-aula-virtual.html` · `comparativo-aulas-virtuales.html` · Fuente de verdad SAE: `blueprint/otherSystemReferenc/analisisyValidacion.md` · Revisión: 2026-08-25*
