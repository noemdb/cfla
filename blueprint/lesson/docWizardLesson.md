# 🧠 Wizard de Lecciones — Documentación de ayuda

> **Propósito:** Guía completa del wizard de lecciones LMS: pasos, herramientas IA, estados, gestión de secciones y consejos.
>
> **Componente:** `app/Livewire/Profesor/Lms/LessonWizard.php`
> **Vista:** `resources/views/livewire/profesor/lms/lesson-wizard.blade.php`

---

## 📋 Visión General del Wizard

El wizard de lecciones guía al usuario a través de **4 pasos** para crear, organizar y publicar contenido educativo interactivo.

### 🔵 Paso 1 — Título y Descripción

Define el **título** de la lección, una **descripción** general, el **nivel educativo** (a quién va dirigida) y los **objetivos de aprendizaje**. La IA puede generar todo este contenido automáticamente con un solo clic.

| Característica | Descripción |
|---|---|
| **IA: generar título** | Genera un título atractivo basado en el tema |
| **IA: generar descripción** | Redacta una descripción detallada |
| **objetivos** | Enumera los objetivos de aprendizaje |

### 🟣 Paso 2 — Diapositivas (Secciones)

Crea y organiza el contenido de la lección. Cada **sección** es una diapositiva independiente que puede contener texto, imágenes, diagramas e ilustraciones.

- Agrega secciones con título y contenido textual
- Genera contenido con IA para cada sección
- Reordena secciones arrastrando o con botones
- Activa/desactiva la visibilidad de cada sección

### 🟡 Paso 3 — Recursos y Enlaces

Adjunta **archivos** (PDF, Word, Excel, imágenes, audio, video) y **enlaces externos** para complementar la lección. Los recursos se muestran al estudiante junto al contenido.

| Tipo | Formato |
|---|---|
| Documentos | PDF, DOC, XLS |
| Imágenes | JPG, PNG, GIF |
| Video | MP4 |
| Audio | MP3 |
| Enlaces | URL externas |

### 🟢 Paso 4 — Publicación

Revisa el contenido, programa una fecha de publicación o publica directamente.

- Los **planificadores** puede publicar de inmediato
- Los **profesores** requieren una fecha de programación
- La lección se vuelve visible para estudiantes automáticamente al publicarse
- **Las lecciones publicadas no se pueden editar**

---

## 🤖 Herramientas de Inteligencia Artificial

El wizard integra IA para acelerar la creación de contenido. Cada herramienta está disponible en el contexto adecuado.

### ✏️ Generar texto de diapositiva
**Botón:** `✨ Generar texto` *(disponible en cada sección)*

Genera el contenido textual completo de una sección a partir del título. La IA redacta párrafos explicativos, listas y ejemplos adaptados al nivel educativo seleccionado.

### 🖼️ Generar imagen
**Botón:** `✨ Generar imagen`

Genera una imagen ilustrativa para la diapositiva basada en el título y contenido de la sección. La IA crea una imagen original que refuerza visualmente el tema.

### 🎨 Generar ilustración decorativa
**Botón:** `✨ Generar ilustración`

Crea una ilustración decorativa única para la diapositiva. A diferencia de la imagen estándar, la ilustración tiene un estilo más artístico y personalizado, ideal para captar la atención.

### 📊 Generar diagrama (Mermaid)
**Botón:** `✨ Generar diagrama` *(incluye zoom + pantalla completa)*

Genera un diagrama conceptual usando **Mermaid.js** (diagramas de flujo, mapas conceptuales, líneas de tiempo). Puedes refinar el diagrama con instrucciones adicionales y visualizarlo en pantalla completa.

### ⚡ Generar lección completa (Paso 1)
**Disponible solo en Paso 1**

En el Paso 1, la IA puede generar automáticamente el título, la descripción, los objetivos de aprendizaje y las diapositivas completas a partir de un tema o palabra clave.

---

## 📊 Estados de la Lección

Cada lección pasa por diferentes estados que determinan su visibilidad y capacidad de edición.

### 🟡 Borrador (DRAFT)
```
Estado por defecto al crear una lección
```

| Propiedad | Valor |
|---|---|
| Edición | ✅ Completa |
| Visible para estudiantes | ❌ No |
| Se puede programar | ✅ Sí |
| Se puede publicar | ✅ Sí |

**Características:** Edición completa de todos los campos y secciones. Solo el autor y admins pueden verla.

### 🟣 Programada (SCHEDULED)
```
Se establece cuando se asigna una fecha de publicación futura
```

| Propiedad | Valor |
|---|---|
| Edición | ✅ Habilitada (hasta la fecha de publicación) |
| Visible para estudiantes | ❌ No (hasta la fecha programada) |
| Publicación automática | ✅ En la fecha programada |

**Características:** La lección se publicará automáticamente en la fecha y hora indicadas. Los controles de edición siguen habilitados hasta la publicación. Se puede cancelar la programación.

### 🟢 Publicada (PUBLISHED)
```
La lección está en producción y visible para estudiantes
```

| Propiedad | Valor |
|---|---|
| Edición | ❌ **Deshabilitada** |
| Visible para estudiantes | ✅ Sí (según configuración de acceso) |

**⚠️ Importante:**
- No se puede editar el contenido ni las secciones
- Solo visible para estudiantes si cumple las condiciones de publicación (fecha, período académico)
- Para modificar una lección publicada, debe archivarse primero

---

## 📐 Gestión de Secciones

Las secciones son las diapositivas que componen la lección.

### ➕ Agregar secciones
Usa el botón **"Agregar sección"** para añadir una nueva diapositiva. Cada sección tiene un título y contenido. Puedes generar el contenido automáticamente con IA.

### ↕️ Reordenar diapositivas
Usa los botones **▲** y **▼** junto a cada sección para cambiar su orden. La diapositiva 1 es la primera que verán los estudiantes.

### 👁️ Visibilidad individual
Cada sección tiene un toggle de visibilidad (**ojo**). Las secciones ocultas no se muestran a los estudiantes pero se conservan en el editor.

### 🗑️ Eliminar secciones
El botón **🗑️ Eliminar** remueve la sección permanentemente. También puedes usar **"Reiniciar secciones"** para limpiar todas las diapositivas y empezar de nuevo.

---

## 💡 Consejos y Atajos

### 🔍 Navegación

- Usa los **pasos del wizard** en la parte superior para navegar entre las etapas de creación
- El **panel lateral** te permite saltar entre diapositivas rápidamente
- Las flechas **◀ ▶** en el panel lateral navegan entre diapositivas consecutivas

### 👁️ Vista previa estudiante

Usa el botón **👁️ Vista estudiante** (icono de ojo) en la esquina inferior derecha para previsualizar cómo verán la lección los estudiantes.

> **💡 Tip:** Usa la vista estudiante frecuentemente durante la edición para asegurarte de que el contenido se vea bien.

### 📅 Programación y publicación

- Los **planificadores** pueden publicar directamente o programar una fecha
- Los **profesores** deben establecer una fecha de programación; la lección será revisada y publicada por Planificación
- Una vez publicada, la lección **no se puede editar**

### ✅ Buenas prácticas

- **Guarda frecuentemente** usando el botón de guardar en la esquina inferior derecha
- **Usa la IA** para generar contenido base y luego ajústalo a tu gusto
- **Organiza las secciones** en un orden lógico para facilitar la comprensión
- **Previsualiza** antes de publicar para detectar errores
- Las secciones sin contenido no afectan la publicación, pero **no se mostrarán** a los estudiantes si están vacías

---

## 📁 Estructura técnica

```
app/Livewire/Profesor/Lms/LessonWizard.php          # Componente Livewire (~5000 líneas)
resources/views/livewire/profesor/lms/lesson-wizard.blade.php  # Vista Blade (~4500 líneas)
app/Services/Lms/LmsPublicationService.php           # Servicio de publicación
app/Models/app/Academy/Lms/LmsActivityPublication.php # Modelo de publicación
```
