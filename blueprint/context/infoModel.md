# Sistema Automatizado de Gestión Escolar – EDUSYS.VE

**Tipo de documento:** Contexto operativo para modelos de Inteligencia Artificial  
**Versión:** 1.2  
**Fecha de actualización:** 2026-01-10  
**Alcance:** Gestión académica, administrativa, financiera y socio–comunitaria  
**Institución base de análisis:** Colegio Fray Luis Amigó  

---

## 1. Propósito del documento

Este documento define el **contexto estructural, funcional y operativo** del sistema **EDUSYS**, con el objetivo de permitir que modelos de inteligencia artificial comprendan con precisión:

- Entidades principales del sistema
- Roles institucionales
- Procesos académicos, administrativos y financieros
- Módulos complementarios (pases estudiantiles y acción comunitaria)
- Reglas de negocio críticas
- Relaciones entre módulos

El contenido está diseñado para **minimizar ambigüedades semánticas** y facilitar la extracción de conocimiento estructurado.

---

## 2. Contexto general: gestión educativa moderna

La gestión escolar moderna exige la integración coherente de múltiples dimensiones:

- Académica
- Administrativa
- Financiera
- Normativa
- Comunicacional
- Socio–comunitaria

La fragmentación de estos procesos genera ineficiencias, pérdida de trazabilidad y riesgos institucionales.  
**EDUSYS** centraliza y automatiza estos procesos en una plataforma única, confiable y auditable.

---

## 3. Descripción general del sistema

**EDUSYS** es una plataforma integral de gestión escolar diseñada para:

- Automatizar procesos operativos
- Centralizar información institucional
- Garantizar trazabilidad completa
- Facilitar la toma de decisiones basada en datos

Adopta un **enfoque modular y escalable**, adaptable al marco normativo y operativo de cada institución.

---

## 4. Principios de diseño

1. Centralización de la información  
2. Automatización con control humano  
3. Trazabilidad de eventos  
4. Separación de roles y responsabilidades  
5. Cumplimiento normativo verificable  
6. Escalabilidad funcional y técnica  

---

## 5. Entidades principales del sistema

### 5.1 Roles humanos

- Estudiante  
- Representante  
- Docente  
- Profesor Tutor  
- Personal Administrativo  
- Coordinador  
- Directivo  
- Operador del sistema  

Cada rol posee permisos, responsabilidades y accesos definidos.

---

### 5.2 Entidades académicas

- Año Escolar  
- Nivel / Grado / Sección  
- Asignatura  
- Plan de Evaluación  
- Indicador  
- Momento de Evaluación  
- Calificación  
- Promedio  
- Condición Académica  

---

### 5.3 Entidades administrativas

- Inscripción Administrativa  
- Inscripción Académica  
- Expediente Digital  
- Incidencia  
- Entrevista  
- Pase Estudiantil  
- Restricción Administrativa  

---

### 5.4 Entidades financieras

- Plan de Pago  
- Cuota  
- Pago  
- Abono  
- Ingreso  
- Recargo por Morosidad  
- Solvencia  
- Conciliación Bancaria  

---

### 5.5 Entidades socio–comunitarias

- Actividad Comunitaria  
- Servicio Ejecutado  
- Horas Comunitarias  
- Certificación Comunitaria  

---

## 6. Arquitectura modular

### 6.1 Módulos académicos

- Gestión de estudiantes  
- Carga académica docente  
- Planificación docente (MPPE)  
- Plan de evaluación  
- Carga y discusión de notas  
- Informes académicos  
- Certificaciones y constancias  
- Rendimiento académico y reconocimientos  
- Materias pendientes y diferidas  

---

### 6.2 Módulos administrativos

- Inscripciones administrativas y académicas  
- Gestión de representantes  
- Control de asistencia (incluye biometría)  
- Gestión de pases estudiantiles  
- Bienestar estudiantil  
- Gestión de estudiantes no matriculados  

---

### 6.3 Módulos financieros

- Procesamiento y registro de pagos  
- Gestión de planes de pago  
- Control de ingresos  
- Gestión de morosidad  
- Conciliación bancaria  
- Reportes financieros  

---

### 6.4 Módulos de comunicación e IA

- Correos electrónicos automatizados  
- Mensajería WhatsApp  
- Debates académicos asistidos por IA  
- Gamificación educativa asistida por IA  
- Entrevistas interactivas  

---

### 6.5 Módulos socio–comunitarios

- Gestión de Horas Comunitarias  
- Gestión de Servicios Ejecutados  
- Certificaciones comunitarias  

---

## 7. Procesos críticos

### 7.1 Registro y control de pagos

1. Generación de cuota  
2. Registro del pago  
3. Asociación bancaria  
4. Validación de trazabilidad  
5. Aplicación de recargos si corresponde  
6. Actualización de solvencia  

**Regla de recargo:**
- Interés simple
- 1% mensual
- Tope 12%
- Sin redondeo

---

### 7.2 Evaluación académica

1. Definición del plan  
2. Registro de indicadores  
3. Carga de calificaciones  
4. Cálculo de promedios sin redondeo  
5. Publicación condicionada a solvencia  

---

## 8. Módulo de Gestión de Pases Estudiantiles

### 8.1 Definición

Documento institucional que autoriza al estudiante a entrar o salir del aula o institución bajo condiciones controladas y por un período definido.

---

### 8.2 Actores

- Estudiante  
- Representante  
- Profesor Tutor  
- Asistente de Coordinación  
- Coordinador  

---

### 8.3 Motivos de pase

- Citas médicas  
- Emergencias familiares  
- Actividades extracurriculares  
- Reuniones académicas  
- Otros motivos institucionales  

---

### 8.4 Proceso de pase

1. Solicitud en coordinación  
2. Registro en EDUSYS  
3. Autorización (representante / profesor)  
4. Impresión del pase  
5. Notificación automática  
6. Registro histórico  

---

### 8.5 Certificado de pase

Incluye:
- Nombre del estudiante  
- Tipo y motivo  
- Descripción y destino  
- Fecha, hora y duración  
- Docente responsable  

---

## 9. Módulo de Horas Comunitarias  
### Gestión de Servicios Ejecutados – Acción Comunitaria

---

### 9.1 Definición

Módulo destinado a registrar, controlar, auditar y certificar las **actividades de acción comunitaria** realizadas por los estudiantes como parte de su formación integral.

---

### 9.2 Objetivo

Garantizar el seguimiento preciso y transparente de las horas comunitarias cumplidas por cada estudiante, con evidencia verificable.

---

### 9.3 Actores

- Estudiante  
- Profesor Tutor  
- Representante  
- Personal Administrativo  
- Directivos  

---

### 9.4 Funcionalidades

#### Registro de actividades
- Título
- Descripción
- Fecha
- Duración
- Asignación por grado
- Archivos adjuntos

#### Registro de asistencia
- Control por estudiante
- Horas cumplidas
- Observaciones

#### Consultas y reportes
- Progreso por estudiante
- Filtros por grado, actividad y fecha
- Reportes PDF
- Visualización tipo calendario

---

### 9.5 Reglas de negocio

- Las horas se acumulan por estudiante  
- Solo actividades validadas son certificables  
- Las observaciones forman parte del expediente  
- La información es auditable  

---

### 9.6 Certificaciones generadas

#### Certificación individual
Incluye:
- Estudiante
- Grado
- Actividades
- Horas por actividad
- Total acumulado
- Firma del tutor

#### Resumen por profesor tutor
- Horas requeridas
- Horas cumplidas
- Porcentaje de cumplimiento

---

## 10. Transparencia y control institucional

- Auditoría completa de eventos  
- Historial inalterable  
- Acceso segmentado por rol  
- Evidencia documental verificable  

---

## 11. Cumplimiento normativo

- Lineamientos MPPE  
- Formatos oficiales  
- Control de asistencia biométrico  
- Inferencia secuencial de jornadas múltiples  

---

## 12. Valor institucional de EDUSYS

EDUSYS actúa como un **sistema nervioso institucional**, integrando información académica, administrativa, financiera y socio–comunitaria en tiempo real.

---

## 13. Tecnologías utilizadas

- Laravel  
- Livewire  
- Tailwind CSS  
- Bootstrap  
- Alpine.js  
- MySQL / MariaDB  
- Redis  
- API WhatsApp Business  
- Integraciones bancarias y POS  
- Modelos de Inteligencia Artificial  

---

## 14. Conclusión

**EDUSYS** es una infraestructura digital de gobernanza escolar que fortalece la transparencia, el control institucional, la calidad educativa y la formación integral del estudiante, integrando procesos académicos, administrativos, financieros y comunitarios bajo un marco unificado, auditable y escalable.

