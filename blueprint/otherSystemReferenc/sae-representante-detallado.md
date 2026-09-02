# SAE Representante — Portal y App para el Representante

> Análisis extenso basado en el sitio oficial [asistescolar.com](https://asistescolar.com/), la página de soluciones ([asistescolar.com/sae/soluciones.html](https://asistescolar.com/sae/soluciones.html)), el portal de acceso ([asistescolar.com/cae/representante](https://asistescolar.com/cae/representante/login.php)) y las fichas de la app **SAE Representante** en Google Play y App Store.
>
> **Fuente de verdad**: documento ajustado conforme a `blueprint/otherSystemReferenc/analisisyValidacion.md`. Las valoraciones de tiendas (reseñas, calificaciones, número de instalaciones) se omiten por ser volátiles y dependientes de región/plataforma; se conserva únicamente el contraste de declaraciones de privacidad entre tiendas.
>
> **SAE Representante** es el perfil/rol del ecosistema Sistemas SAE diseñado para que el **representante** (padre, madre o responsable legal del estudiante) haga seguimiento en tiempo real de la información académica y administrativa de su(s) representado(s), desde **cualquier lugar del mundo**, a través de dos canales: el **portal web** (dentro de SAE WEB / Intranet) y la **app móvil dedicada** (iOS y Android).

---

## 1. Qué es SAE Representante y cómo encaja en el ecosistema

SAE Representante no es un producto aislado, sino el **perfil de usuario "Representante"** dentro del ecosistema SAE WEB, que se nutre de la información cargada por la institución en **SAE Evaluación** (notas, planes de evaluación) y **SAE Pagos** (facturación y cobranza). Existen dos formas de acceso para el representante:

1. **Acceso Web**: a través del portal `asistescolar.com/cae/representante/` (login con usuario y contraseña, normalmente la cédula del representante como usuario por defecto), integrado a la Intranet multiperfil de SAE WEB.
2. **App móvil "SAE Representante"**: aplicación nativa disponible para **iOS** y **Android**, desarrollada por B y C Computación, C.A. (empresa detrás de AsistEscolar.com). La disponibilidad real por sistema operativo, versión y módulo contratado debe validarse por separado.

Adicionalmente, para instituciones del sector **público**, el catálogo de soluciones de AsistEscolar contempla un producto específico llamado **SAE Web Representante**, orientado exclusivamente a ese segmento.

---

## 2. SAE Web Representante (instituciones públicas)

Según la página de soluciones de AsistEscolar, este es un producto diferenciado dentro del portafolio, pensado específicamente para representantes de **instituciones educativas públicas**:

- **Segmento**: instituciones públicas (a diferencia del acceso genérico de representante, que aplica también a instituciones privadas, AVEC, ANDIEP, Fe y Alegría, parroquiales, etc.).
- **Acceso en tiempo real** a:
  - **Notas**.
  - **Asistencia**.
  - **Pagos**.
  - **Comunicados** institucionales.
- Se solicita a través de asesoría directa con el equipo comercial de AsistEscolar (no tiene autoservicio de registro abierto).

En instituciones públicas venezolanas también se ha documentado el uso de este portal para procesos puntuales como **asambleas o votaciones virtuales** de representantes, donde el representante ingresa con su cédula, confirma o actualiza sus datos y participa en el proceso de votación institucional — una funcionalidad que conecta con la característica de "asambleas virtuales con resultados en tiempo real" descrita en SAE WEB para el rol "Institución".

---

## 3. Funcionalidades académicas (consulta para el representante)

Este es el bloque central de valor para el representante: **monitoreo en tiempo real** del desempeño académico de su(s) representado(s).

### 3.1 Consulta de notas académicas
- **Evaluación Continua**: consulta de las calificaciones parciales/continuas conforme el docente las va cargando durante el lapso escolar (no solo al cierre).
- **Boletines**: consulta de los boletines finales generados automáticamente por SAE Evaluación (cualitativos o cuantitativos según el nivel educativo).

### 3.2 Consulta de tareas y asignaciones
- **Tareas Pendientes / Entregadas**: visualización del estado de las asignaciones del estudiante.
- Al abrir el detalle de una actividad, el representante puede ver:
  - **Información** de la tarea.
  - **Calificación** obtenida.
  - **Comentario del Docente** (retroalimentación específica).
  - **Tiempo Restante** para la entrega (cuenta regresiva de plazo).

### 3.3 Consulta de planes de evaluación
- Acceso a los **Planes de Evaluación** definidos por cada docente/materia, es decir, el desglose de cómo se compone la calificación del lapso (instrumentos, ponderaciones, fechas), lo cual da transparencia al representante sobre cómo se construye la nota final.

### 3.4 Recursos educativos y clases online
- **Consulta de Recursos Educativos**: acceso a materiales de apoyo publicados por los docentes (guías, documentos, etc.), en línea con el enfoque "recursos tipo universidad" que describe SAE WEB.
- **Acceso a Clases Online** a través de un **Calendario de Eventos**, que permite al representante conocer y hacer seguimiento a la programación de actividades académicas virtuales de su representado.

### 3.5 Módulo de incidencias
- **Consulta de Incidencias** del estudiante (módulo incorporado en actualizaciones recientes de la app), que permite al representante enterarse de novedades conductuales o situacionales reportadas por la institución.

---

## 4. Funcionalidades administrativas y financieras

Aunque el foco de este documento es académico, el representante gestiona también, desde el mismo perfil, procesos financieros vinculados directamente al estudiante:

- **Registro y Consulta de Pagos** emitidos.
- **Consulta de Facturas y Productos Facturados**.
- **Estados de Cuenta**, disponibles tanto **por familia** (si tiene más de un representado) como **por estudiante individual**.
- Desde el portal web general de SAE WEB, el representante también puede: **solicitar documentos** (constancias, solvencias), **actualizar sus datos** y los del estudiante, y **descargar el cartón de pagos de mensualidades** en formato digital.

---

## 5. Comunicación y notificaciones

- **Mensajería en tiempo real**: canal de comunicación directa entre el representante y la institución/docentes, incorporado como mejora reciente de la app.
- **Notificaciones nativas**: alertas push del sistema operativo (iOS/Android) para avisos importantes, incorporadas como mejora de la app en versiones recientes.
- **Chats con representantes**: desde el lado docente, los profesores cuentan con chats directos hacia los representantes, cerrando el canal de comunicación bidireccional.
- **Correos masivos** y **cartelera informativa**: el representante recibe también comunicados generales de la institución (eventos, calendario escolar, reuniones, actividades programadas, galería de imágenes, lista de útiles, horarios), previa autorización y configuración de la institución.

---

## 6. Evolución de la app móvil SAE Representante (historial de versiones)

El historial público de versiones en App Store permite reconstruir cómo ha evolucionado el producto, lo cual es útil para entender la madurez y dirección del desarrollo:

| Versión | Novedades |
|---|---|
| **1.0.0** | Lanzamiento inicial de la app |
| **1.0.1** | Corrección de errores en la descarga de archivos |
| **2.0** | Nueva presentación general de la app |
| **2.1** | Nueva interfaz y funcionalidades |
| **2.2** | Manejo de mensajería en tiempo real; detalle de actividades en Tareas (información, calificación, comentario del docente, tiempo restante) |
| **2.3** | Nuevo módulo de incidencias; nuevo manejo de notificaciones nativas; optimizaciones |
| **2.4** | Notificaciones nativas (refuerzo) |
| **2.5** | Optimización y mejoras de pantallas (versión más reciente registrada) |

Esta cadencia de actualizaciones (aprox. mensual en el período reciente) sugiere un desarrollo activo del producto, con foco particular en **mensajería/notificaciones** e **incidencias** como las incorporaciones más recientes.

---

## 7. Requisitos técnicos y disponibilidad

- **Plataformas**: iOS (requiere iOS 15.1 o superior; también compatible con iPadOS, y macOS 12+ en Mac con chip Apple M1 o superior) y Android (Google Play).
- **Costo**: aplicación gratuita para el representante (el costo del sistema recae en el contrato anual que la institución mantiene con AsistEscolar).
- **Idioma**: interfaz en español, orientada al mercado venezolano.
- **Clasificación de edad**: 18+ en App Store (clasificación asociada al contenido de mensajería/chat de la app, no al uso exclusivo por adultos).
- **Privacidad**: el desarrollador declara en App Store que **no recopila datos** del dispositivo del usuario a través de la app (según ficha de privacidad de Apple); en Google Play se indica que la app puede compartir datos como información personal, financiera y de archivos/documentos con terceros, y recogerlos, según la ficha de seguridad de datos de esa tienda — existe una diferencia entre lo declarado en ambas tiendas que vale la pena tener presente.

---

## 8. Declaraciones de privacidad y metadatos de las tiendas

Las tiendas móviles publican metadatos y declaraciones de privacidad que pueden diferir entre plataformas. En App Store, el desarrollador declara que **no recopila datos** del dispositivo del usuario para SAE Representante; en Google Play, la ficha indica que la app **podría compartir información personal, financiera y otros tipos de datos con terceros**, que podría recopilar información personal, que **los datos no están cifrados** y que **no se pueden solicitar eliminaciones** desde la ficha. Estas declaraciones son autodeclaradas por el desarrollador en cada tienda y pueden cambiar con actualizaciones de la aplicación.

> Nota de validación: se retiraron las referencias a calificaciones de usuarios (p. ej., «2.6/5») y conteos de descargas por ser volátiles, dependientes de región/plataforma y no auditables; solo deben citarse con fecha, URL exacta de la ficha y captura preservada.

---

## 9. Cuadro resumen de funcionalidades de SAE Representante

| Categoría | Funcionalidades clave |
|---|---|
| **Académico** | Consulta de notas (evaluación continua y boletines), planes de evaluación, tareas (pendientes/entregadas) con detalle de calificación y comentario del docente, recursos educativos, acceso a clases online vía calendario de eventos, incidencias |
| **Administrativo/Financiero** | Registro y consulta de pagos, consulta de facturas y productos facturados, estados de cuenta por familia o por estudiante, solicitud de documentos, cartón de pagos digital |
| **Comunicación** | Mensajería en tiempo real, notificaciones push nativas, chats con docentes, correos masivos, cartelera informativa |
| **Acceso especial (sector público)** | SAE Web Representante: notas, asistencia, pagos y comunicados en tiempo real; uso también para procesos institucionales como asambleas/votaciones virtuales |
| **Plataformas** | Web (portal/intranet SAE WEB) y app móvil nativa iOS/Android, gratuita para el representante |
| **Evolución del producto** | Mejoras recientes centradas en mensajería en tiempo real, notificaciones nativas y módulo de incidencias |

---

### Fuentes consultadas
- https://asistescolar.com/
- https://asistescolar.com/sae/sae-web.html
- https://asistescolar.com/sae/soluciones.html
- https://asistescolar.com/cae/representante/login.php
- https://apps.apple.com/us/app/sae-representante/id6450141097
- https://play.google.com/store/apps/details?id=com.asistEscolar.SaeRepresentanteMovil&hl=es
