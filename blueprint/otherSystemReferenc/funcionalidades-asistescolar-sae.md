# Funcionalidades de AsistEscolar (Sistemas SAE)

> Análisis basado en el sitio oficial [asistescolar.com](https://asistescolar.com/), con foco en **Control de Estudios**, **Informe de Notas / Boletines**, **Aula Virtual**, **Registro y Control de Estudiantes** y **Formatos de Notas**.
>
> **Fuente de verdad**: documento ajustado conforme a `blueprint/otherSystemReferenc/analisisyValidacion.md`, que separa capacidades verificadas públicamente de inferencias razonables.
>
> Sistemas SAE es un software de gestión escolar venezolano que **declara más de 25 años** de experiencia; la cifra de **más de 900 instituciones** proviene de su comunicación comercial (afirmación del proveedor, no métrica auditada) y abarca segmentos públicos, privados, AVEC, ANDIEP, Fe y Alegría, parroquiales, universidades y academias. Está adaptado a las exigencias del **MPPE (Ministerio del Poder Popular para la Educación)** y **Zonas Educativas (CDCE)**; la **homologación SENIAT** figura en su material comercial, pero las páginas públicas de Evaluación y SAE WEB no la acreditan por sí solas (requiere evidencia específica o fuente oficial del SENIAT).

---

## 1. Arquitectura general del ecosistema

AsistEscolar no es un producto único, sino un **ecosistema modular** compuesto por varios sistemas que se integran entre sí:

| Módulo | Enfoque principal |
|---|---|
| **SAE Evaluación** | Control de estudios, notas, planillas legales y estadísticas académicas |
| **SAE Pagos** | Facturación, cobranza y control financiero (homologado SENIAT) |
| **SAE WEB** | Portal institucional, intranet, apps móviles y **Aulas Virtuales** |
| **SAE Comunica** | Mensajería masiva (notificaciones, chat, correo, SMS, WhatsApp) |
| **Suite adicional** | Nómina, Compras, Control de Acceso, Carnet Digital, Eventos |
| **Migración de Datos** | Traspaso de históricos desde otros sistemas |

El proveedor comercializa **módulos integrables**: SAE WEB declara **compatibilidad e intercambio de datos** (importación/exportación) con SAE Evaluación y SAE Pagos, por lo que la información cargada en un módulo (por ejemplo, notas en SAE Evaluación) puede verse reflejada en otro (por ejemplo, boletines en SAE WEB). No está demostrado públicamente que exista **una sola base de datos** ni sincronización automática en tiempo real; evitar presentar esa integración como reflejo automático garantizado.

---

## 2. SAE Evaluación — Control de Estudios

Es el núcleo académico del sistema: **evaluación y control de estudio**, adaptado a la normativa vigente del MPPE, incluyendo planes de estudio **vigentes y derogados**, y disponible tanto para **Régimen Regular** como **Régimen para Adultos**, además de **Escuelas Técnicas**.

### 2.1 Cumplimiento legal y normativo
- Emisión de **todas las planillas exigidas por el MPPE**.
- Generación de **Resúmenes Finales** con las categorías oficiales de calificación: **Notas 01–20, NC (No Cursó), P (Pendiente), Art. 109, IN (Incompleto)**.
- Emisión de **Títulos** y **Acta de Revisión de Documentos**.
- Adaptación constante a la **Nueva Ley de Educación**: cualquier modificación en planillas, formatos o títulos exigida por el Ministerio se incorpora sin costos adicionales durante la vigencia del contrato.

### 2.2 Control académico e histórico
- Gestión de **históricos de notas**, tanto de planes **vigentes** como **derogados**.
- **Conversión Automática de notas históricas a planes derogados** (redacción literal del sitio público). El sitio no precisa que la conversión sea bidireccional entre cualquier plan vigente y derogado; evitar afirmar ese alcance extendido sin verificación directa.
- **Migración garantizada** de datos personales y notas certificadas desde otros sistemas escolares.

### 2.3 Automatización de procesos
- **Prosecución automática** de estudiantes al siguiente año escolar (evita recarga manual de matrícula año a año).
- Carga automática de condición de **escolaridad** (regulares, repitientes).
- **Exportación completa a Microsoft Excel** de cualquier reporte o listado.

### 2.4 Reportes e impresión (formatos de notas)
- **Boletines cualitativos y cuantitativos** (según el nivel: inicial/primaria suele manejar boletín cualitativo descriptivo, mientras que secundaria maneja notas cuantitativas 01–20).
- **Constancias** y **Certificación de Calificaciones** generadas automáticamente.
- Reportes adicionales: **fichas de inscripción, carnets, foto-nómina** (listado con fotografía de cada estudiante).

### 2.5 Estadística institucional
- Control exacto de **inscritos y repitientes**.
- Análisis demográfico por **edad, sexo y nacionalidad**.
- **Cuadro de Honor Final** y promedios por sección.

### 2.6 Soporte y respaldo
- Asistencia telefónica y remota.
- Formación continua: foro-chats, webinars y talleres.
- Requiere **contrato anual** de mantenimiento, revisiones, mejoras y asistencia técnica (modelo de suscripción/soporte recurrente, no venta de licencia perpetua).

---

## 3. SAE WEB — Portal, Intranet y Aulas Virtuales

SAE WEB es la capa web/móvil que conecta a toda la comunidad educativa (institución, docentes, representantes y estudiantes) e integra la información de SAE Evaluación y SAE Pagos mediante compatibilidad e intercambio de datos declarados por el proveedor.

### 3.1 Experiencia por tipo de usuario

**Institución (control total)**
- Gestión administrativa: inscripciones 100% online, gestión de pagos y **solvencia automática**.
- Gestión académica: generación de boletines, estadísticas de rendimiento, control de nóminas.
- Comunicación: portal web autoadministrable, envío masivo de correos y avisos directos.
- Liderazgo: **asambleas virtuales** con resultados en tiempo real (votaciones).

**Personal docente**
- **Aula Virtual**: carga de contenidos, recepción y corrección de tareas con retroalimentación (feedback) directa.
- Carga de notas y creación de **planes de evaluación**.
- Chats con representantes, foros de dudas y manejo del calendario escolar.

**Representantes**
- Autogestión: registro de pagos, solicitud de documentos, actualización de datos desde casa.
- Monitoreo en tiempo real de notas, tareas entregadas y estatus de calificación de su representado.
- Participación en votaciones de asambleas y comunicación directa con el colegio.

**Estudiantes**
- Entrega de asignaciones, acceso a guías, videos y materiales de apoyo.
- Consulta de horarios, listas de útiles y planes de evaluación.
- Consulta inmediata de correcciones y calificaciones.

### 3.2 Módulos por categoría

**Aplicaciones móviles**
- App Gerencia: estadísticas, reportes financieros y métricas institucionales en tiempo real.
- App Docentes: registro de asistencia, carga de notas, planificación académica, comunicación con representantes.
- App Familia y Estudiantes: consulta de notas, asistencia, horarios, pagos, recursos de clase y comunicación directa.

> Nota de validación: SAE WEB menciona apps para Gerente, Profesor y Representantes/Estudiantes; la disponibilidad real por sistema operativo, versión y módulo contratado debe validarse por separado (fichas de tienda).

**Administrativos**
- Registro de pagos: conciliación bancaria, historial de pagos, estados de cuenta con trazabilidad completa.
- Estadísticas y reportes: gráficas dinámicas e informes financieros/académicos.
- Gestión de personal: datos del personal, permisos, asignación de aulas, listados por sección.
- Sub-organizaciones: manejo de grupos académicos, deportivos y pastorales.
- Constancias y solvencias: emisión online instantánea, incluyendo cartón de pagos digital.

**Académicos**
- **Control de asistencia**: registro diario de puntualidad y faltas, con notificaciones automáticas a representantes vía app.
- **Gestión de notas y boletines**: carga de calificaciones online, boletines y certificados finales con **firma electrónica** (confirmado a nivel comercial; el tipo de firma, su trazabilidad, cumplimiento legal y alcance documental no están especificados públicamente).
- Planificación académica: creación de planes de evaluación, planificadores de lapso, cronogramas de evaluación, distribución de contenidos programáticos por materia y manejo del calendario escolar.
- Recursos compartidos: listas de útiles, guías, horarios, planes de evaluación y circulares.
- **Aula Virtual (e-Learning)**: carga de contenidos, publicación de recursos educativos, recepción/corrección de tareas con retroalimentación directa y actividades interactivas. «Como en una universidad» es lenguaje comercial del proveedor; no implica equivalencia funcional con un LMS completo (Moodle, Canvas, etc.).

**Comunicacionales**
- Portal web autoadministrable: cartelera informativa, calendario institucional, galerías de eventos y noticias.
- Intranet multiperfil: portales dedicados y seguros para cada rol.
- Correos masivos automatizados hacia representantes.
- Mensajería interna en tiempo real y notificaciones push.
- Galería y cartelera digital multimedia.

**Admisión**
- Solicitud de cupo online integrada al sistema (captación de datos del representante).
- Inscripción digital auto-gestionada.
- Panel de seguimiento del estado de cada solicitud (aprobaciones y comunicación automática de resultados).

**Seguridad**
- Control de acceso ID+: carnets digitales con código QR.
- Permisos granulares por rol/perfil de usuario.
- Protección de datos: backup automático y cifrado de contraseñas.

### 3.3 Productos web específicos (según página de Soluciones)
- **SAE Web Profesor**: portal exclusivo para docentes de instituciones públicas — gestión académica, registro de evaluaciones en línea y comunicación con la comunidad.
- **SAE Web Representante**: portal para representantes de instituciones públicas — consulta de notas, asistencia, pagos y comunicados.
- **Aulas Virtuales SAE**: plataforma de aprendizaje en línea integrada — contenidos y material didáctico, tareas y evaluaciones en línea, totalmente integrada con SAE WEB.

---

## 4. SAE Comunica — Comunicación institucional

- **Confirmado vía SAE WEB**: mensajería interna/en tiempo real, notificaciones push y correos masivos.
- **Requiere verificación directa** en la página de SAE Comunica o material oficial específico: los canales **SMS y WhatsApp** no están descritos explícitamente en las páginas públicas revisadas de Evaluación y SAE WEB.
- Flujo de "Envía – Consulta – Recibe – Responde" para mantener comunicación bidireccional con representantes y estudiantes.

---

## 5. SAE Pagos (contexto financiero, referencia)

Aunque el foco solicitado es académico, es relevante mencionarlo porque se integra con el control de estudios (por ejemplo, la solvencia condiciona la entrega de boletines/constancias):

- Control de pagos, **facturación** y cobranza.
- Ajuste e indexación automática de pagos.
- Entorno **multimoneda** (USD, pesos, euros) y manejo de multicuentas.
- Gestión de **IGTF** y reportes financieros.
- **Homologado por el SENIAT**.

---

## 6. Suite adicional SAE

| Módulo | Función |
|---|---|
| **SAE Nómina** | Cálculo automático de salarios, deducciones y beneficios del personal; reportes de nómina |
| **SAE Compras** | Gestión de proveedores, retenciones de I.V.A. e I.S.L.R., control de egresos |
| **SAE Control de Acceso** | Registro de entradas/salidas de estudiantes y personal, alertas a representantes, historial de accesos |
| **SAE Carnet Digital** | Carnet con código QR, verificación electrónica instantánea, integrado con Control de Acceso |
| **SAE Eventos** | Boletos y credenciales digitales, control de acceso con QR, certificados digitales para eventos institucionales |

> Nota de validación: nómina, compras, asistencias, ingresos, control de acceso QR, pagos y gestión administrativa se mencionan a nivel de ecosistema general; conviene atribuir cada función a su módulo/fuente concreta y no asumir que toda la suite opera bajo una sola UI o licencia.

---

## 7. Migración de datos

Servicio dedicado para trasladar desde otros sistemas:
- **Datos personales** de estudiantes y representantes.
- **Notas certificadas** (certificación de calificaciones) e históricos académicos.

Se presenta como una garantía explícita del proveedor para instituciones que cambian de plataforma.

---

## 8. Modelo comercial y soporte

- Los sistemas SAE se contratan bajo **modalidad de contrato anual**, que incluye mantenimiento, revisiones, mejoras y asistencia técnica.
- Soporte técnico **24/7**: ⚠️ no suficientemente respaldado por las fuentes públicas verificadas; las páginas oficiales hablan de asistencia telefónica/remota durante el período escolar, mantenimiento y formación. Evitar afirmar 24/7 sin fuente contractual u oficial que lo precise.
- Capacitación presencial y remota, adaptada al rol de cada usuario (gerencial, docente, etc.), con material de apoyo incluido.
- Implementación con **asesoría gratuita** y configuración personalizada.
- Respaldo de datos en la nube con backups automáticos y programados.

---

## 9. Síntesis: cobertura frente a los puntos solicitados

| Requerimiento | Dónde lo cubre AsistEscolar |
|---|---|
| **Control de estudios** | SAE Evaluación (núcleo del sistema): históricos, planes vigentes/derogados, prosecución automática, escolaridad |
| **Informe de notas / boletines** | Boletines cualitativos y cuantitativos, resúmenes finales (01–20, NC, P, Art. 109, IN), certificación de calificaciones, firma electrónica en SAE WEB |
| **Aula virtual** | Módulo "Aula Virtual / e-Learning" dentro de SAE WEB y producto independiente "Aulas Virtuales SAE": carga de contenidos, tareas, corrección con feedback, materiales interactivos |
| **Registro y control de estudios** | Inscripción/admisión online, ficha de inscripción, foto-nómina, carga de escolaridad (regulares/repitientes), control de asistencia diaria |
| **Formatos de notas** | Planillas oficiales exigidas por el MPPE, categorías normativas de calificación (01–20, NC, P, Art. 109, IN), conversión automática de notas históricas a planes derogados (bidireccionalidad no precisada públicamente), actualización continua ante cambios de la Ley de Educación |

---

### Fuentes consultadas
- https://asistescolar.com/
- https://asistescolar.com/sae/sae-evaluacion.html
- https://asistescolar.com/sae/sae-web.html
- https://asistescolar.com/sae/soluciones.html
