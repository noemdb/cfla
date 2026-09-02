# Análisis del Sistema de Referencia Externo — AsistEscolar (Sistemas SAE)

> Síntesis y análisis de los documentos de referencia en `blueprint/otherSystemReferenc/`:
> - `funcionalidades-asistescolar-sae.md` (ecosistema general)
> - `sae-evaluacion-detallado.md` (módulo académico núcleo)
> - `sae-representante-detallado.md` (perfil representante / app móvil)
> - `infoPlus.md` (validación contra capturas oficiales + validación cruzada pública)
>
> **Fuente de verdad**: `analisisyValidacion.md`. Este análisis fue ajustado a su dictamen: separa capacidades verificadas públicamente de inferencias razonables y corrige niveles de certeza.
>
> Objetivo: identificar capacidades del sistema de referencia y contrastarlas con el estado actual de este proyecto (cfla) para detectar brechas funcionales.

---

## 1. Qué es

**AsistEscolar (Sistemas SAE)** — software de gestión escolar venezolano que declara más de 25 años de experiencia; la cifra de **+900 instituciones** proviene de su comunicación comercial (afirmación del proveedor, no métrica auditada). Adaptado al **MPPE** y **Zonas Educativas (CDCE)**; la homologación **SENIAT** figura en su material comercial pero requiere evidencia específica o fuente oficial.

- **Modelo comercial**: contrato anual ✅ confirmado (mantenimiento, revisiones, mejoras y asistencia técnica; no licencia perpetua). El soporte «24/7» **no está respaldado** por las fuentes públicas verificadas (hablan de asistencia telefónica/remota durante el período escolar).
- **Arquitectura**: el proveedor comercializa **módulos integrables**; SAE WEB declara compatibilidad e intercambio de datos (importación/exportación) con Evaluación y Pagos. No está demostrado públicamente que exista una base de datos única ni sincronización automática en tiempo real.

---

## 2. Ecosistema modular

| Módulo | Función |
|---|---|
| **SAE Evaluación** | Núcleo académico: notas, control de estudios, planillas legales MPPE |
| **SAE Pagos** | Facturación/cobranza multicuenta, multimoneda (USD/EUR), IGTF (homologación SENIAT: pendiente de evidencia) |
| **SAE WEB** | Portal + intranet multiperfil + apps móviles + capa de aula virtual |
| **SAE Comunica** | Push, chat/mensajería interna y correo confirmados vía SAE WEB; SMS y WhatsApp requieren verificación directa |
| **Suite Administrativo** | Nómina, compras, asistencias, ingresos, control de acceso QR, eventos — atribuir cada función a su módulo/fuente concreta; no asumir una sola UI o licencia |
| **Migración de Datos** | Traspaso garantizado de históricos (datos personales + certificaciones de calificaciones) desde otros sistemas |

---

## 3. Capacidades clave de SAE Evaluación (verificadas)

### 3.1 Legal y normativa
- Emisión de todas las planillas exigidas por el MPPE.
- Resúmenes Finales con estados normativos oficiales: **01–20, NC (No Cursó), P (Pendiente), Art. 109, IN (Incompleto)**.
- Títulos oficiales y Acta de Revisión de Documentos.
- Modificaciones de planillas/formatos/títulos exigidas por el Ministerio durante la vigencia del contrato sin cobro adicional → **compromiso comercial del proveedor**, no garantía regulatoria jurídicamente demostrada.

### 3.2 Control académico
- Históricos de notas de planes **vigentes y derogados**, con redacción literal «Conversión Automática de notas históricas a planes derogados» — la bidireccionalidad entre cualquier plan vigente y derogado **no está precisada públicamente**.
- Migración garantizada de datos desde otros sistemas escolares.
- **Prosecución automática** al siguiente año escolar.
- Carga automática de escolaridad (regulares / repitientes).

### 3.3 Reportes
- Boletines cualitativos (inicial/primaria) y cuantitativos (secundaria, escala 01–20).
- Constancias y certificación de calificaciones automáticas.
- Fichas, carnets y foto-nómina (listado con fotografía por sección).

### 3.4 Estadística institucional
- Control exacto de inscritos y repitientes.
- Análisis demográfico: edad, sexo, nacionalidad.
- Cuadro de Honor Final y promedios por sección.

### 3.5 Modalidades cubiertas
Régimen Regular, Régimen para Adultos, Escuelas Técnicas y coexistencia de pénsums vigentes/derogados.

---

## 4. Perfil Representante (portal web + app iOS/Android)

- **Académico**: notas en tiempo real (evaluación continua + boletines), planes de evaluación, tareas pendientes/entregadas con calificación, comentario del docente y tiempo restante para entrega, recursos educativos, clases online vía calendario, incidencias.
- **Financiero**: registro y consulta de pagos, facturas, estados de cuenta **por familia o por estudiante**, cartón de pagos digital, solicitud de documentos/solvencias.
- **Comunicación**: mensajería en tiempo real, notificaciones push nativas, chats con docentes, correos masivos, cartelera.
- **Evolución reciente del producto**: foco en mensajería, notificaciones nativas e incidencias (versiones 2.2–2.5).

### Privacidad y metadatos (formulación cautelar)
Las tiendas móviles publican metadatos y declaraciones de privacidad que pueden diferir entre plataformas: en App Store el desarrollador declara que **no recopila datos**; en Google Play la ficha indica que podría **compartir información personal, financiera y otros datos con terceros**, que los datos **no están cifrados** y que no se pueden solicitar eliminaciones desde la ficha. Son declaraciones autodeclaradas por el desarrollador y pueden cambiar con actualizaciones. Las valoraciones de usuarios (p. ej., «2.6/5») se omiten por ser volátiles y no auditables sin fecha, URL exacta y captura preservada.

---

## 5. Fiabilidad de la información (`infoPlus.md`)

El documento valida comentarios de un trabajador contra capturas oficiales, y ahora incluye una **sección 4 de validación cruzada contra las páginas públicas**:

| Estatus | Afirmaciones |
|---|---|
| ✅ Confirmado | Soporte legal MPPE/Zonas Educativas; asistencia diaria; conversión automática (alcance literal limitado); compromiso de actualización de formatos MPPE; títulos + Acta de Revisión de Documentos |
| ⚠️ Parcial | Notificaciones filtradas por solvencia (filtro inferido); estadística mensual de asistencia (asistencia diaria confirmada, estadística no); Hoja/Acta de Registro de Título (existen Títulos y Acta de Revisión, pero no esos formatos específicos) |
| ❌ Sin evidencia pública | Cuadre de plantillas para impresión de títulos; reportes OPSU |

---

## 6. Aula virtual — alcance real (no es un LMS completo)

Lo verificable públicamente es una **capa de e-learning integrado a la gestión escolar**, no un LMS tipo Moodle/Canvas/Google Classroom.

### Capacidades confirmadas
Contenido educativo (carga de contenidos y publicación de recursos), recursos institucionales (útiles, guías, horarios, planes de evaluación, circulares), tareas (pendientes/entregadas), corrección docente, retroalimentación directa y consulta de correcciones, actividades interactivas (sin especificar tipo), chat con representantes y foros de dudas, clases online vía calendario (sin proveedor de videollamada identificado), seguimiento de notas/progreso y acceso móvil.

### Flujo pedagógico inferible
Planificación/cronograma/plan de evaluación → publicación de recursos → asignación de tareas → entrega del estudiante → corrección y feedback → calificación visible → consulta por estudiante/representante → complemento con calendario, foros, chats y notificaciones.

### Límites: NO evidenciado públicamente
Cuestionarios autocorregibles, banco de preguntas, rúbricas, evaluación por criterios/competencias, SCORM/xAPI/LTI, videollamada nativa (Zoom/Meet/Teams/Jitsi), grabación de clases, control de plagio, versionado de entregas, entregas grupales, penalizaciones automáticas, analítica de aprendizaje, learning paths, gamificación, accesibilidad WCAG, modo offline, API/webhooks, gestión granular de archivos y auditoría de calificaciones/comentarios. → Clasificar como **«no verificadas públicamente», no como ausentes**.

---

## 7. Planificación docente — capacidad más amplia de lo asumido

No es solo «planes de evaluación visibles al representante». Confirmado: planes de evaluación, **planificadores de lapso**, **cronogramas de evaluación**, distribución de contenidos por materia, calendario escolar, publicación a estudiantes/representantes, planificación desde la app del profesor y continuidad operativa planificar→evaluar→calificar.

### Modelo funcional recomendado para cfla
`Año escolar → Lapso → Curso/Sección → Asignatura → Planificación → Unidad → Actividad/Evaluación`, con entidades de primera clase: planificación docente (estado, versión, aprobación), unidades de aprendizaje, actividades (fechas, peso, evidencia), instrumentos de evaluación (rúbrica, criterios, ponderaciones), plan de evaluación (validación al 100%), evidencia estudiantil (versiones, estados) y notificaciones.

### Reglas de negocio críticas
- No publicar sin asignatura/lapso/sección/docente y ≥1 unidad o actividad.
- Validar límites y precisión de ponderaciones antes de publicar.
- Separar plan aprobado de borrador modificado; versionar cambios publicados y notificar afectados.
- No eliminar actividades con notas/entregas: archivar o versionar.
- Auditoría de publicación, edición, aprobación, carga de notas y cambios de fecha.
- Impedir que docentes no asignados publiquen o califiquen en una sección.
- Visibilidad del representante limitada a sus representados activos; conservar historial ante cambios de sección, retiro o promoción.
- Definir tratamiento de actividades tardías, reprogramadas, anuladas o recuperativas.
- Escala de nota asociada al régimen/nivel (cualitativa primaria ≠ numérica secundaria).
- Restricciones de solvencia solo donde la institución lo defina formalmente; nunca ocultar indebidamente información académica.

---

## 8. Mapa vs. este proyecto (cfla)

| Capacidad SAE | Estado en cfla | Detalle |
|---|---|---|
| Prosecución automática | ✅ Existe | `Learner\Estudiant` con trait `Prosecucions` |
| Censo / admisión online | ✅ Existe | Módulos Catchment + Enrollment (wizards Livewire); SAE también confirma cupo e inscripción digital con seguimiento |
| Pagos multimoneda/bancos | ⚠️ Parcial | `Admon\Payment`, `Banco`, `ExchangeRate`; falta facturación SENIAT/IGTF |
| Votaciones / asambleas virtuales | ✅ Existe (ventaja) | Módulo voting propio: anónimo, tokens, QR, fingerprint |
| Notas / boletines MPPE | ❌ No existe | Núcleo regulado de SAE: escala 01–20, NC/P/Art.109/IN, boletines, resúmenes finales |
| Planificación docente | ❌ No existe | Lapso, cronogramas, contenidos programáticos, planes de evaluación |
| Aula virtual | ❌ No existe | SAE ofrece contenidos, tareas, corrección y feedback integrados (no LMS completo) |
| Asistencia diaria | ❌ No existe | SAE: registro diario de puntualidad/faltas con notificaciones automáticas al representante |
| Comunica (push/SMS/WhatsApp) | ⚠️ Parcial | Email jobs + Reverb; SAE confirma push/chat/correo (SMS/WhatsApp sin verificar) |
| Constancias / solvencias automáticas | ⚠️ Parcial | PDFs vía dompdf; falta solvencia ligada a pagos y cartón de pagos digital |
| Estadística institucional | ⚠️ Parcial | Pulse monitorea técnica; falta analítica académica (inscritos, repitientes, demografía, honor) |

---

## 9. Brechas principales identificadas

1. **Núcleo académico regulado** — la mayor brecha: evaluación, notas, boletines, control de estudios y documentación MPPE (escala 01–20, estados NC/P/Art.109/IN).
2. **Aula virtual y planificación docente — brecha crítica.** SAE WEB declara carga de contenidos, publicación de recursos, recepción de tareas, corrección y retroalimentación directa; además planes de evaluación, planificadores por lapso, cronogramas, distribución de contenidos, calendario, foros y chat. Las capacidades de LMS avanzado (rúbricas, bancos de preguntas, autocalificación, SCORM/LTI, analítica, videollamada) deben catalogarse como «no verificadas públicamente», no inexistentes. Para cfla el objetivo no es replicar «subir archivos y recibir tareas», sino un módulo académico integrado que conecte planificación, contenidos, actividades, entregas, feedback, calificaciones, boletines, asistencia, notificaciones y analítica — trazabilidad pedagógica, no un documento estático.
3. **Asistencia diaria** — registro de puntualidad/faltas con notificación automática a representantes.
4. **Comunicación multicanal** — WhatsApp/SMS/push nativos complementando email + WebSockets actuales.
5. **Documentos y solvencias** — condicionar entrega según política institucional formal (integración pagos ↔ documentos).
6. **Estados normativos de títulos** (hoja/acta de registro, cuadre de plantillas, reportes OPSU) — sin evidencia pública; validar requerimiento con el colegio antes de priorizar.

---

## 10. Conclusión

cfla ya tiene activos valiosos en captación, matrícula, pagos base y votaciones; pero para competir con un producto como SAE en el segmento institucional venezolano el orden de prioridad debe ser:

**modelo académico y evaluativo → planificación docente → tareas/aula virtual → asistencia → documentos y solvencias → comunicaciones multicanal → analítica institucional**

Esta secuencia reduce reprocesos porque tareas, aula virtual y boletines dependen de una definición sólida de períodos, secciones, asignaturas, escalas, criterios y políticas de evaluación. Aula virtual, planificación docente y asistencia deben implementarse como **extensiones directamente conectadas a ese núcleo académico**, evitando módulos aislados que dupliquen datos o procesos.
