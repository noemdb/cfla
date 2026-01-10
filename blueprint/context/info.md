# EDUSYS.VE  
## Documento Descriptivo Profesional (Contexto Maestro para IA)

**Tipo de documento:** Descripción técnica–funcional en Markdown (para referencia de IA y stakeholders)  
**Versión:** 1.0  
**Fecha de actualización:** 2026-01-10  
**Producto:** EDUSYS – Sistema Automatizado de Gestión Escolar  
**Ámbito:** Instituciones educativas (Primaria y Media General)  
**Nota:** Este documento describe el sistema, sus módulos, entidades, procesos, reglas y entregables documentales con un enfoque profesional y explicativo.

---

## 1. Visión general

**EDUSYS** es una plataforma integral de gestión escolar orientada a **automatizar**, **centralizar** y **controlar** las operaciones clave de una institución educativa. Su propósito es reducir la carga administrativa, fortalecer la transparencia institucional y mejorar la trazabilidad operativa, integrando en un solo ecosistema digital:

- Procesos **académicos** (planificación, evaluación, calificaciones, reportes)
- Procesos **administrativos** (inscripción, control, expedientes, incidencias)
- Procesos **financieros** (pagos, morosidad, solvencia, reportes)
- Procesos **comunicacionales** (notificaciones, mensajería institucional)
- Procesos **socio–comunitarios** (horas comunitarias, certificaciones)

EDUSYS se estructura como una solución **modular y escalable**, permitiendo incorporar funcionalidades progresivamente según las necesidades de la institución y su marco normativo.

---

## 2. Principios operativos del sistema

EDUSYS está concebido bajo principios que garantizan consistencia y control institucional:

1. **Centralización de información:** la institución reduce dependencias de archivos dispersos, hojas de cálculo y archivos físicos.
2. **Trazabilidad:** cada evento relevante (pago, nota, pase, asistencia, horas comunitarias) genera registros auditables.
3. **Control por roles:** cada actor accede únicamente a datos y funciones correspondientes a su responsabilidad.
4. **Automatización con supervisión:** el sistema automatiza tareas repetitivas sin perder control humano en acciones críticas.
5. **Evidencia documental:** el sistema emite constancias, certificados y reportes verificables.
6. **Compatibilidad institucional:** el sistema se adapta a formatos oficiales y procesos internos del colegio.

---

## 3. Actores del sistema (roles) y responsabilidades

Los roles determinan el acceso, los permisos y los flujos operativos.

### 3.1 Roles principales

- **Estudiante:** sujeto principal del registro académico; participa en actividades y puede solicitar trámites según habilitaciones.
- **Representante:** responsable de seguimiento académico–administrativo; consulta desempeño y estados; participa en procesos de autorización.
- **Docente / Profesor Tutor:** registra notas, participa en evaluación, supervisa procesos académicos y ciertos flujos de autorización.
- **Personal Administrativo / Operador:** realiza registros administrativos y financieros; gestiona trámites institucionales.
- **Coordinador:** supervisa áreas académicas o administrativas, valida procesos, consulta históricos, gestiona control institucional.
- **Directivo:** accede a reportes estratégicos e indicadores para toma de decisiones.

---

## 4. Entidades del dominio (modelo conceptual)

EDUSYS opera sobre un conjunto de entidades transversales.

### 4.1 Entidades académicas

- **Año Escolar:** periodo lectivo; habilita cohortes, reportes y cierres.
- **Grado / Sección:** organización curricular del estudiante.
- **Asignatura:** unidad académica; se relaciona con evaluación y calificación.
- **Plan de Evaluación:** define indicadores y momentos; estructura el proceso evaluativo.
- **Indicador:** criterio evaluativo; se vincula a actividades, registros y notas.
- **Momento de Evaluación:** corte temporal del rendimiento (por ejemplo: Momento I, II, III).
- **Calificación / Promedio:** resultado del proceso; sujeto a reglas institucionales (por ejemplo: cálculo sin redondeo).
- **Rendimiento Académico:** lectura consolidada del desempeño; soporte para reconocimientos y alertas.

### 4.2 Entidades administrativas

- **Inscripción Administrativa:** registro base institucional (documentos, aceptación, estatus).
- **Inscripción Académica:** ubicación formal del estudiante en grado/sección para el año escolar.
- **Expediente Digital:** consolidación de información de estudiante, incidencias, entrevistas y soportes.
- **Incidencia:** evento relevante de disciplina o bienestar; puede generar correctivos.
- **Entrevista:** registro de reuniones y acuerdos institucionales.
- **Pase Estudiantil:** autorización formal para entrada/salida bajo condiciones especiales.
- **Restricción Administrativa:** condicionamiento de acceso a ciertos servicios por reglas (por ejemplo: morosidad).

### 4.3 Entidades financieras

- **Plan de Pago:** estructura de obligaciones (cuotas y vencimientos).
- **Cuota:** obligación específica; base de cálculo y solvencia.
- **Pago / Abono:** operación financiera aplicada a cuotas.
- **Ingreso:** registro de entrada de dinero, asociado a banco o medio de pago.
- **Recargo por Morosidad:** cálculo adicional por vencimiento.
- **Solvencia:** estado administrativo asociado a obligaciones canceladas.
- **Conciliación Bancaria:** validación cruzada entre registros del sistema y movimientos bancarios.

### 4.4 Entidades socio–comunitarias

- **Actividad Comunitaria:** evento social/comunitario planificado y registrado.
- **Asistencia Comunitaria:** evidencia de participación en actividad.
- **Horas Comunitarias:** acumulado por estudiante, validado y auditable.
- **Certificación Comunitaria:** documento oficial de cumplimiento de horas.

---

## 5. Arquitectura modular (descripción funcional)

EDUSYS se organiza en módulos, cada uno con responsabilidades claras y relaciones definidas.

### 5.1 Módulos académicos

Incluyen funcionalidades para:

- Gestión de estudiantes (datos, historial, estatus académico)
- Carga académica docente (asignación de materias y secciones)
- Planificación docente (formatos institucionales, MPPE u otros)
- Plan de evaluación (indicadores, momentos, criterios)
- Carga de notas (registro y cálculo)
- Discusión de notas (revisión y control de cambios)
- Informes académicos (boletines, cortes, certificados)
- Histórico de notas, certificaciones, constancias
- Promociones, títulos y resumen final (según alcance institucional)

### 5.2 Módulos administrativos

Incluyen:

- Inscripción administrativa y académica
- Renovación de matrícula y aseguramiento de matrícula
- Gestión de estudiantes no matriculados
- Gestión de materias pendientes/diferidas
- Bienestar estudiantil (incidencias, entrevistas, acuerdos)
- Gestión de pases estudiantiles (documentación y control)
- Control de asistencia (incluye biometría y reglas de jornada)
- Gestión de restricciones administrativas (por reglas de negocio)

### 5.3 Módulos financieros

Incluyen:

- Procesamiento y registro de pagos
- Control de ingresos y conciliación
- Gestión de morosidad
- Dashboard financiero e indicadores
- Reportes para auditoría y dirección administrativa

### 5.4 Módulos de comunicación e integración

Incluyen:

- Envío de correos electrónicos institucionales
- Mensajería automatizada por WhatsApp (notificaciones y recordatorios)
- Tableros de interacción (estado de envíos y respuestas)
- Integraciones con POS y bancos (según disponibilidad y configuración)

### 5.5 Módulos socio–comunitarios

Incluyen:

- Registro de actividades comunitarias
- Registro de asistencia y horas
- Reportes y certificaciones
- Seguimiento por estudiante y por profesor tutor
- Visualización calendario de actividades

---

## 6. Procesos operativos clave (explicación detallada)

### 6.1 Proceso financiero: registro y control de pagos

**Objetivo:** garantizar que cada pago quede correctamente registrado, aplicado a obligaciones y trazable.

1. **Generación de obligación:** el sistema crea cuotas según el plan de pago.
2. **Registro del pago:** el operador registra el pago/abono asociado al estudiante o representante.
3. **Asociación al ingreso:** se vincula el pago con el banco y el medio utilizado.
4. **Validación de trazabilidad:** se conserva evidencia cruzada (registro interno + soporte + referencia bancaria).
5. **Cálculo y aplicación de recargos:** si existe morosidad, se aplican recargos según reglas.
6. **Actualización de solvencia:** el sistema habilita o restringe servicios según el estatus.

**Regla institucional típica:**  
- Recargo por morosidad = **interés simple 1% mensual** con **tope 12%**.

### 6.2 Proceso académico: evaluación y publicación

**Objetivo:** organizar y auditar la evaluación por periodos e indicadores.

1. Definición del plan de evaluación por asignatura/grado.
2. Registro de indicadores y ponderaciones.
3. Carga de calificaciones por el docente.
4. Cálculo automático de promedios (según reglas; por ejemplo, **sin redondeo**).
5. Generación de informes por momento de evaluación.
6. Publicación condicionada (si aplica) por reglas institucionales, por ejemplo: solvencia.

---

## 7. Módulo de Gestión de Pases Estudiantiles (descripción completa)

### 7.1 Propósito

Gestionar de forma automatizada el proceso de **solicitud, registro, autorización y emisión** de pases estudiantiles, con trazabilidad y evidencia documental.

### 7.2 Definición de Pase Estudiantil

Documento institucional que acredita a un estudiante para **entrar o salir** del salón o de la institución bajo condiciones especiales, por un tiempo definido. Su objetivo central es:

- Seguridad del estudiante
- Prevención de riesgo y disciplina
- Control institucional verificable

### 7.3 Actores y responsabilidades

- **Estudiante:** solicita el pase.
- **Asistente de coordinación:** registra, imprime y entrega instrucciones.
- **Representante:** autoriza cuando aplique.
- **Profesor / Tutor:** aprueba cuando aplique.
- **Coordinador:** audita y consulta históricos.

### 7.4 Motivos comunes

- Citas médicas u odontológicas
- Emergencias familiares
- Actividades extracurriculares
- Reuniones con consejeros o profesores
- Otros

### 7.5 Flujo operativo (solicitud y emisión)

1. Estudiante solicita pase en coordinación.
2. Asistente inicia sesión en EDUSYS.
3. Accede al módulo de pases.
4. Registra la información requerida.
5. Imprime el certificado.
6. Entrega el pase al estudiante.
7. El sistema ejecuta acciones posteriores:
   - Notificaciones (correo u otros canales)
   - Generación de código de verificación (si aplica)
   - Almacenamiento y auditoría del registro

### 7.6 Datos obligatorios del certificado de pase

- Nombre del estudiante
- Tipo de pase
- Motivo
- Descripción
- Destino
- Duración
- Fecha
- Hora
- Profesor responsable

### 7.7 Históricos y exportación

El coordinador o asistente puede consultar históricos por:

- Plan educativo
- Estudiante
- Profesor
- Área de formación

Y exportar listados a formatos de hoja de cálculo.

---

## 8. Módulo de Horas Comunitarias  
### Gestión de Servicios Ejecutados – Acción Comunitaria (descripción completa)

### 8.1 Propósito

Gestionar y dar seguimiento al programa de acción comunitaria mediante el registro sistemático de:

- Actividades comunitarias
- Participación estudiantil
- Horas cumplidas
- Observaciones
- Certificaciones oficiales

### 8.2 Objetivo

Facilitar la gestión y seguimiento de las actividades de acción comunitaria realizadas por los estudiantes, así como el control de horas cumplidas por cada uno, aportando métricas y evidencia verificable.

### 8.3 Beneficios institucionales

- Seguimiento preciso del cumplimiento por estudiante
- Transparencia para actores involucrados
- Información útil para toma de decisiones (participación, efectividad)
- Reducción de errores administrativos por registros manuales

### 8.4 Metas del módulo

1. **Aumentar participación:** campañas, diversidad de actividades, reconocimientos.
2. **Mejorar precisión:** asistencia automatizada, capacitación de tutores, auditorías.
3. **Fortalecer transparencia:** publicación de actividades, informes a representantes, consulta en línea.

### 8.5 Funcionalidades principales

#### 8.5.1 Registro de actividades
- Título, descripción, fecha, duración
- Asignación por grado
- Adjuntos (imágenes/documentos)

#### 8.5.2 Registro de asistencia
- Registro de asistencia por estudiante
- Horas cumplidas por estudiante
- Observaciones sobre participación

#### 8.5.3 Consultas y reportes
- Progreso por estudiante
- Filtros por grado, estudiante, fecha, actividad
- Reportes en PDF
- Vista tipo calendario

### 8.6 Reglas de negocio relevantes

- Las horas se acumulan por estudiante.
- Las actividades deben estar registradas para ser consideradas.
- Solo horas validadas son certificables.
- Observaciones y evidencias forman parte del expediente.
- Los datos deben ser auditables y exportables para control institucional.

### 8.7 Certificaciones y formatos emitidos

#### 8.7.1 Certificación individual de horas comunitarias
Incluye:
- Nombre del estudiante
- Grado
- Fecha de emisión
- Listado de actividades con horas y observaciones
- Total de horas cumplidas
- Firma/identificación del profesor tutor

**Uso:** reconocimientos internos y soporte ante entidades externas.

#### 8.7.2 Resumen por profesor tutor
Incluye:
- Tutor y grado asignado
- Total de horas requeridas vs cumplidas
- Porcentaje de cumplimiento
- Fecha y responsable de emisión

---

## 9. Transparencia, auditoría y control institucional

EDUSYS garantiza:

- Registro histórico de eventos relevantes
- Evidencia verificable (reportes y certificados)
- Control de acceso por roles
- Exportación para análisis y auditoría
- Reducción de conflictos mediante datos claros

---

## 10. Cumplimiento normativo y compatibilidad institucional

El sistema se adapta a formatos y prácticas institucionales, y puede alinearse con:

- Normativas educativas vigentes
- Requisitos de control administrativo
- Formatos oficiales y procesos de certificación

---

## 11. Tecnologías (vista general)

EDUSYS utiliza tecnologías modernas para asegurar estabilidad y escalabilidad:

- Laravel
- Livewire
- Tailwind CSS
- Bootstrap
- Alpine.js
- MySQL / MariaDB
- Redis
- Integración WhatsApp Business
- Integración con POS y bancos (según configuración)
- Modelos de IA para contenido y análisis

---

## 12. Conclusión

EDUSYS es una plataforma de **gobernanza escolar digital** que unifica la operación académica, administrativa, financiera y socio–comunitaria, incrementando la eficiencia institucional, la trazabilidad de eventos y la transparencia ante la comunidad educativa.

Su enfoque modular permite implementaciones progresivas y adaptaciones a la realidad operativa de cada institución, manteniendo consistencia, evidencia documental y control institucional.

