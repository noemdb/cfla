# Guía de Estados de Lecciones LMS

> Documentación para usuarios de **planificación, supervisión y seguimiento** sobre los estados de publicación de lecciones en el sistema LMS.

---

## Ciclo de Vida de una Lección

```
  Borrador ──┬──→ Publicado ──→ Archivado
             │
             └──→ Programado ──→ Publicado (automático)
```

Toda lección en el LMS atraviesa un ciclo de vida definido por su estado de publicación.
Comprender estos estados permite evaluar el avance docente, identificar oportunidades
de mejora y garantizar que el contenido digital esté disponible para los estudiantes
cuando lo necesiten.

---

## 1. Publicado (`PUBLISHED`)

### ¿Qué significa?

Una lección en estado **Publicado** está completamente visible y accesible para los
estudiantes en su aula virtual. Pueden ver su contenido, descargar recursos, acceder
a enlaces externos y participar en las actividades diseñadas por el docente.

**Características:**
- ✓ Visible para estudiantes
- ✓ Contenido completo
- ✓ Recursos descargables
- ✓ Participación activa

### Significado para la supervisión

Es el indicador principal de que el docente ha completado su flujo de trabajo y el
contenido está siendo consumido.

**Qué observar:**
1. **Auditar la calidad** del contenido publicado: ¿está completo? ¿tiene recursos? ¿las secciones están bien estructuradas?
2. **Verificar cobertura curricular**: ¿todas las asignaturas tienen lecciones publicadas? ¿hay algún grado o sección con rezago?
3. **Monitorear la proporción** entre borradores y publicaciones: un alto número de borradores puede indicar contenido estancado.
4. **Dar acompañamiento** a nuevas publicaciones para mantener el seguimiento pedagógico.

### Transiciones posibles

| Acción | Resultado | Descripción |
|--------|-----------|-------------|
| **Archivar** | → `ARCHIVED` | La lección deja de ser visible. Útil al finalizar un lapso o cuando el contenido debe ser retirado. |
| **Revertir a borrador** | → `DRAFT` | Permite editar el contenido. La lección deja de estar visible hasta que se publique nuevamente. |

### Ejemplo práctico

> El profesor de **Matemáticas** completa y publica la lección **«Ecuaciones de primer grado»**.
> La lección se vuelve visible para **5to año, sección A** — 32 estudiantes.
> El supervisor ingresa al monitor, ubica la lección y hace clic en **Vista previa** para auditar el contenido.
> Tras la revisión, ajusta la **configuración** para permitir descargas y comentarios.

---

## 2. Programado (`SCHEDULED`)

### ¿Qué significa?

Una lección en estado **Programado** tiene una fecha y hora específicas para publicarse
automáticamente. El contenido ya está listo pero permanece invisible para los estudiantes
hasta la fecha programada. Opcionalmente puede tener una fecha de **despublicación automática**.

> La lección ya fue creada y configurada por el docente. El temporizador se encarga del resto.
> No requiere intervención manual al llegar la fecha.

### Significado para la supervisión

Es un excelente indicador de **planificación docente**. Muestra que el profesor no solo
creó el contenido sino que también definió cuándo debe estar disponible.

**Qué observar:**
1. **Aprovecha para auditar** el contenido **antes** de que se publique. Usa la vista previa para verificar que esté completo y correcto.
2. **Verifica las fechas**: ¿la publicación está alineada con el cronograma académico? ¿la fecha de despublicación (si aplica) es adecuada?
3. **Identifica docentes** que usan la programación como práctica regular — es señal de buena organización pedagógica.
4. **Monitorea la anticipación**: contenidos programados con poca antelación pueden indicar planificación reactiva.

### Transiciones posibles

| Acción | Resultado | Descripción |
|--------|-----------|-------------|
| **Publicar (automático)** | → `PUBLISHED` | Al llegar la fecha programada, el sistema publica automáticamente sin intervención manual. |
| **Revertir a borrador** | → `DRAFT` | Cancela la programación. El contenido vuelve a borrador y no se publicará en la fecha prevista. |
| **Archivar** | → `ARCHIVED` | Descarta la programación y archiva el contenido. |

### Ejemplo práctico

> El profesor de **Ciencias Naturales** programa la lección **«La Célula»** para el viernes a las 8:00 AM.
> Establece una despublicación automática para el viernes siguiente (1 semana de visibilidad).
> El supervisor ingresa al monitor el jueves, filtra por **"Programado"** y usa la **Vista previa** para auditar el contenido **antes** de que se publique.
> Si el contenido está aprobado, no hace nada — el sistema lo publicará automáticamente el viernes a las 8:00 AM.

---

## 3. Borrador (`DRAFT`)

### ¿Qué significa?

Una lección en estado **Borrador** está siendo creada o editada. Tiene un registro de
publicación pero **no es visible** para los estudiantes. El docente puede estar agregando
secciones, recursos, enlaces o ajustando el contenido. Es un estado **transitorio**.

**Características:**
- ✗ No visible para estudiantes
- ✗ Contenido parcial o en edición
- ✗ Sin acceso para estudiantes
- ⟳ En proceso de construcción

### Significado para la supervisión

Es el estado más común durante la fase de creación y representa una **oportunidad**
para acompañar el proceso antes de la publicación.

**Qué observar:**
1. **Identifica contenido estancado**: lecciones en borrador por más de 1-2 semanas pueden indicar dificultades del docente o falta de tiempo.
2. **Audita borradores** para ofrecer retroalimentación **temprana**: es más fácil ajustar antes de publicar.
3. **Mide el progreso general**: un volumen alto de borradores frente a publicaciones indica que el contenido está en preparación pero aún no disponible.
4. **Contacta al docente** si observas borradores que deberían estar publicados según la planificación académica.

### Transiciones posibles

| Acción | Resultado | Descripción |
|--------|-----------|-------------|
| **Publicar ahora** | → `PUBLISHED` | La lección se vuelve visible inmediatamente. Se crea un registro de publicación. |
| **Programar** | → `SCHEDULED` | Define una fecha futura para publicación automática. |

> **Nota:** Desde "Borrador" no se puede archivar directamente. Primero debe publicarse o programarse; luego sí puede archivarse.

### Ejemplo práctico

> El profesor de **Inglés** comienza a crear la lección **«Present Simple»** — queda automáticamente en borrador.
> Agrega 2 secciones, un video embebido y 3 ejercicios interactivos, pero aún no publica.
> El supervisor revisa los borradores, encuentra la lección, la previsualiza y nota que le faltan recursos descargables.
> Se comunica con el docente para sugerir la inclusión de una guía de estudio descargable **antes de publicar**.

---

## 4. Archivado (`ARCHIVED`)

### ¿Qué significa?

Una lección en estado **Archivado** fue publicada previamente pero ha sido despublicada.
Ya no es visible para los estudiantes. A diferencia de "Borrador" (que nunca fue visible),
el contenido archivado tiene un **historial de publicación** y la lección permanece en
el sistema con todo su contenido intacto para consulta o republicación futura.

> **No se pierde nada.** El contenido, secciones, recursos y configuraciones se conservan íntegramente.
> Puedes republicarlo en cualquier momento.

### Significado para la supervisión

Representa contenido que cumplió su ciclo o fue retirado por decisión pedagógica.

**Qué observar:**
1. **Revisa el volumen de archivados**: muchas lecciones archivadas repentinamente pueden indicar un problema (cambio de planificación, error masivo, etc.).
2. **Consulta el historial**: las lecciones archivadas mantienen su contenido. Úsalas como referencia para planificar el siguiente período.
3. **Republica si es necesario**: si un contenido archivado sigue siendo relevante (ej. lección de repaso transversal), puedes volver a publicarlo.
4. **Identifica el contexto**: ¿se archivó por fin de lapso? ¿por decisión del docente? ¿por error? Cada caso tiene implicaciones distintas.

### Transiciones posibles

| Acción | Resultado | Descripción |
|--------|-----------|-------------|
| **Publicar** | → `PUBLISHED` | Reactivación: el contenido vuelve a estar visible para los estudiantes. |
| **Revertir a borrador** | → `DRAFT` | Permite editar el contenido archivado antes de republicar. |
| **Eliminar publicación** | *(irreversible)* | Elimina permanentemente el registro de publicación. La actividad base queda intacta. ⚠️ Irreversible. |

### Ejemplo práctico

> La lección **«Geometría básica»** del Lapso 1 estuvo publicada y visible durante todo el período.
> Al iniciar el Lapso 2, el docente archiva la lección para dejar espacio al nuevo contenido.
> El supervisor puede consultarla en el monitor filtrando por **"Archivado"** para usarla como referencia.
> Si un estudiante necesita repasar ese contenido, el supervisor puede **republicarlo temporalmente** con un solo clic.

---

## Resumen visual del ciclo de vida

```
┌──────────┐   Publicar / Programar   ┌───────────┐   Archivar   ┌───────────┐
│ BORRADOR │ ───────────────────────→ │ PUBLICADO │ ───────────→ │ ARCHIVADO │
└──────────┘                          └───────────┘              └───────────┘
       │                                    │                          │
       │                                    │                          │
       └── Programado ─→ Publicado          └── Revertir a borrador    │
           (automático)                                                 │
                                                                        └── Revertir a borrador
                                                                            → Publicar
```

- Las lecciones transitan de izquierda a derecha en el flujo principal.
- Desde **Publicado** y **Archivado** se puede revertir a **Borrador** para editar y reiniciar el ciclo.
- **Programado** es un estado intermedio que transiciona automáticamente a **Publicado**.

---

## Pautas para la supervisión

1. **Monitoreo regular**: Revisa al menos una vez por semana la distribución de estados en el monitor.
2. **Detección temprana**: Los borradores estancados (>2 semanas) son la principal señal de alerta.
3. **Auditoría preventiva**: Aprovecha el estado "Programado" para auditar antes de la publicación.
4. **Contexto primero**: Ante un volumen inusual de archivados, investiga la causa antes de actuar.
5. **Acompañamiento**: Usa la información de estados para orientar conversaciones pedagógicas con los docentes.
6. **Cobertura**: Verifica que todas las asignaturas y grados tengan contenido publicado de acuerdo al cronograma académico.
