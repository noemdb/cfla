# PRODUCT.md — NoDoz / SAEFL

> Documento estratégico de producto. Define **quién usa el sistema, para qué, y qué principios guían las decisiones de interfaz**. No es un manual visual ni una especificación técnica.

## 1. Producto

**NoDoz / SAEFL** es el sistema de gestión escolar del colegio. Cubre el ciclo académico y administrativo: censo, matrícula, pagos, prosecución, evaluación, planificación docente, horarios, LMS, diagnósticos, votación y comunicación institucional.

Este documento se enfoca en la superficie de **planificación y revisión académica**, donde conviven cuatro roles con permisos y necesidades distintas:

- **Planning / Planificación:** administra la estructura académica (Peducativos, Planes de Estudio, Grados, Secciones, Pensum, Carga Académica, Lapsos) y supervisa la calidad de los planes de evaluación.
- **Coordinación:** revisa y acompaña el trabajo docente de los Peducativos que gestiona; registra observaciones y da seguimiento a actividades y lecciones.
- **Leadership / Jefes de Área:** supervisa las asignaturas de las áreas que lidera; necesita una vista acotada, de solo lectura en la mayoría de los casos, con foco en pendientes y observaciones.
- **Dirección:** observa la operación académica de forma agregada y detecta riesgos; no opera el día a día.
- **Profesor:** construye sus planes de evaluación, actividades y lecciones.
- **Estudiante:** consume lecciones y contenidos LMS.

## 2. Registro de producto

**Register: product.** La interfaz sirve a una tarea concreta; el usuario está trabajando, no explorando una marca. La familiaridad es una virtud: tablas, filtros, formularios y estados deben comportarse como el usuario espera en una herramienta profesional.

El éxito no se mide por cuánto llama la atención la interfaz, sino por:

- cuánto tarda el usuario en encontrar lo que busca,
- cuántos errores evita,
- cuánta confianza siente al revisar y guardar cambios,
- y cuánto contexto conserva al moverse entre roles y pantallas.

## 3. Trabajos principales

### Planificación

1. Configurar y mantener la estructura académica.
2. Asignar profesores a áreas, secciones y lapsos.
3. Revisar la calidad de los planes de evaluación.
4. Copiar actividades entre planes sin duplicar trabajo.
5. Detectar planes incompletos, sin actividades o con observaciones.

### Coordinación

1. Revisar las actividades de los Peducativos que gestiona.
2. Identificar actividades pendientes, aprobadas y observadas.
3. Registrar y actualizar observaciones para Planificación y docentes.
4. Verificar la publicación de lecciones LMS.
5. Trabajar por lapso y por plan de estudio sin perder el alcance asignado.

### Leadership

1. Supervisar las asignaturas de sus áreas.
2. Detectar pendientes y observaciones.
3. Consultar planes y actividades sin modificar la operación.
4. Entender rápidamente qué le corresponde y qué no.

### Dirección

1. Ver el estado agregado de la operación académica.
2. Detectar riesgos y tendencias.
3. Comparar lapsos, planes y áreas sin entrar en el detalle operativo.

## 4. Personalidad de marca

Institucional, serena, precisa y confiable. Una herramienta que da la sensación de estar bien mantenida: ordenada, legible, rápida y sin ruido.

- **Serena:** no compite con el contenido ni usa decoración gratuita.
- **Precisa:** cada filtro, contador y estado dice la verdad.
- **Confiable:** las acciones destructivas se confirman; los cambios se confirman visualmente.
- **Cercana:** el lenguaje es claro, en español, sin jerga innecesaria.
- **Moderna sin exhibirse:** la tecnología se nota en la fluidez, no en los efectos.

## 5. Anti-referencias

Evitar explícitamente:

- Dashboards genéricos de “AI SaaS” con gradientes neón, glassmorphism excesivo y métricas decorativas.
- Interfaces que priorizan la estética sobre la legibilidad de datos.
- Filtros que parecen funcionar pero no filtran, o que muestran opciones imposibles.
- Acciones visibles que el rol no puede ejecutar.
- Mensajes de error técnicos o ambiguos.
- Dependencia de hover para descubrir acciones importantes.
- Animaciones largas o bloqueantes que interrumpen el trabajo.
- Inconsistencias de vocabulario entre pantallas del mismo flujo.

## 6. Principios estratégicos de diseño

### 6.1 La tarea primero

La jerarquía visual debe responder: **¿dónde estoy, qué estoy viendo, qué puedo hacer ahora?** Títulos, alcance, filtros activos y resultados deben leerse en ese orden.

### 6.2 Un vocabulario para todos los roles

Los mismos conceptos deben llamarse igual en Planning, Coordinación, Leadership y Dirección:

- Plan de Estudio
- Grado / Año
- Sección
- Lapso / Momento
- Pensum
- Pevaluación / Plan de Evaluación
- Actividad
- Lección
- Observación

Las diferencias entre roles deben estar en el alcance y las acciones, no en el nombre de las cosas.

### 6.3 El alcance debe ser visible

Un usuario de Coordinación o Leadership no ve toda la institución. La interfaz debe comunicar el alcance (“Peducativos que gestionas”, “Áreas asignadas”) para evitar la falsa impresión de que faltan datos.

### 6.4 Los filtros son promesas

Cada filtro debe:

- tener un efecto real y comprensible,
- mostrar qué está activo,
- poder limpiarse de forma evidente,
- y persistir si el usuario vuelve a la pantalla.

Si una opción no puede cumplirse con el modelo de datos actual, se elimina o se redefine; no se deja como decoración.

### 6.5 Resultados y contexto siempre presentes

Toda lista debe responder:

- cuántos resultados hay,
- qué rango se está viendo,
- en qué lapso,
- con qué filtros,
- y qué hacer si no hay resultados.

### 6.6 Acciones reversibles y feedback claro

- Confirmar acciones destructivas.
- Mostrar estado de guardado.
- Evitar pantallas bloqueantes cuando una operación rápida puede resolverse con un indicador inline.
- Diferenciar “no hay datos” de “los filtros no devuelven resultados”.

### 6.7 Accesibilidad por defecto

- Etiquetas asociadas a controles.
- `aria-label` en botones de icono.
- Foco visible y navegación por teclado.
- Contraste suficiente.
- Áreas táctiles de al menos 44×44 px.
- No depender solo del color para comunicar estado.

### 6.8 Consistencia sobre novedad

Antes de inventar un patrón, reutilizar el existente. Los componentes compartidos (filtros, tablas, tarjetas, estados vacíos, toggles de vista) deben comportarse igual en todas las pantallas.

### 6.9 Rendimiento percibido

La interfaz debe sentirse inmediata:

- debounce en búsquedas,
- indicadores de carga locales,
- esqueletos o atenuación en lugar de overlays de pantalla completa,
- paginación server-side,
- evitar cargar “todos” los registros cuando no es necesario.

## 7. Señales de éxito

- El usuario encuentra un plan o actividad en menos de tres interacciones.
- El usuario entiende por qué una lista está vacía.
- El usuario sabe qué filtros tiene activos sin abrir un panel.
- El usuario de Leadership no ve acciones que no puede ejecutar.
- El usuario de Coordinación no ve filtros redundantes o imposibles.
- Los cambios de filtro no bloquean toda la pantalla.
- Las métricas de soporte por “no encuentro X” o “el filtro no funciona” disminuyen.

## 8. Foco actual (Fase 1)

Mejoras de UX en las listas de actividades de Planning, Coordinación y Leadership:

1. Búsqueda global por profesor, asignatura, sección y tema.
2. Botón “Limpiar filtros” y chips de filtros activos.
3. Corrección de filtros engañosos en Coordinación.
4. Contador de resultados y contexto del lapso.
5. Persistencia de filtros, vista y tamaño de página.
6. Estados vacíos con acción de recuperación.
7. Indicadores de carga no bloqueantes.
8. Unificación de etiquetas y del toggle Tarjetas/Tabla.

## 9. Restricciones

- Laravel 10 + Livewire 3 + Alpine.js + Tailwind CSS + WireUI.
- La base de datos de producción no se reconstruye; los tests usan transacciones.
- Los permisos y el alcance por rol se aplican en backend; la UI debe reflejarlos, no sustituirlos.
- El sistema es multi-rol y multi-institución; no asumir que un usuario ve todo.
- El idioma principal es español.

## 10. Fuera de alcance de este documento

- Tokens de color, tipografía y espaciado (ver `DESIGN.md` si existe).
- Especificaciones de API, modelos o migraciones.
- Decisiones de infraestructura.
- Roadmap comercial.
