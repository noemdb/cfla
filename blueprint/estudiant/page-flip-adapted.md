# 📋 Especificación Técnica (Adaptada al Proyecto): Visor de Lecciones en Formato Libro

**Versión:** 1.1 (adaptada)
**Estado:** Propuesta para implementación
**Stack real:** Laravel 10.48.x, Livewire 3.x, Vite 5.x, Tailwind 3.x, Alpine.js 3.x, `page-flip` (StPageFlip)

> ⚠️ **Esta es la adaptación de la propuesta genérica (`page-flip.md` v1.0) al esquema real
> de este repositorio.** La v1.0 describía modelos `Lesson`, `Section`, `Resource` que
> **no existen** en este proyecto. Aquí se mapea todo a los modelos reales del área estudiante
> (ver §2 y §9). Cualquier cambio de UI debe respetar los invariantes de seguridad de
> `math-text.blade.php` (DOMPurify + KaTeX CVE-2025-1390) — ver §10.

---

## 1. Objetivo

Implementar un **modo de lectura tipo libro (flipbook)** para una lección (`Activity`) en el
área estudiante (`/app/estudiante/activity/{activity}`), donde cada **sección**
(`LmsActivitySection`) es una página del libro, con navegación fluida, responsive y compatible
con el ecosistema existente (WireUI, Mermaid, Alpine + Livewire).

**Decisión de alcance:** el flipbook es un **modo de vista alternativo** dentro de la vista
existente `ActivityView`, **no** un componente Livewire nuevo que duplique toda la carga de
datos. Reutiliza el HTML de renderizado ya probado de `activity-view.blade.php`.

---

## 2. Contexto Arquitectónico · Mapeo a Modelos Reales

| Propuesta genérica v1.0 | Realidad en este proyecto |
|------------------------|---------------------------|
| `Lesson` | `App\Models\app\Academy\Activity` (una lección publicada) |
| `Lesson.sections` | `Activity::lmsSections()` → `LmsActivitySection` ordenadas por `sort_order` |
| `Section` | `LmsActivitySection` (`title`, `description`, `sort_order`, `is_visible`) |
| `Section.content` (HTML simple) | **No existe un campo `content`.** El contenido son `LmsActivityContent` (tipos `TEXT`/`IMAGE`/`VIDEO`/`AUDIO`/`EMBED`/`PRESENTATION`/`FILE_PREVIEW`/`HTML`) vía `section->visibleContents()`, con plantillas de render, Mermaid y KaTeX |
| `Section.resources` | `LmsActivityResource` + `LmsActivityLink` + `LmsHtmlEmbed` (cada uno con `section_id` vinculable) |
| Modelo `Resource` único | Son **tres colecciones separadas** con su propio `sort_order` y `section_id` (nullable = global) |
| Columna `order` | `sort_order` en las tablas LMS |

### Relaciones clave (reales)

```
Activity (1) → (N) LmsActivitySection         (lmsSections(), orderBy sort_order)
LmsActivitySection (1) → (N) LmsActivityContent (visibleContents(), orderBy sort_order)
Activity (1) → (N) LmsActivityResource        (lmsResources(); section_id nullable)
Activity (1) → (N) LmsActivityLink            (lmsLinks(); section_id nullable)
Activity (1) → (N) LmsHtmlEmbed               (lmsHtmlEmbeds(); section_id nullable)
Activity (1) → (1) LmsActivityPublication     (lmsPublication(); isPreviewToStudents, allow_comments)
```

**Regla de página:** el flipbook renderiza **una página por sección visible** (`is_visible=true`,
filtradas ya en `ActivityView::mount()`). Los recursos/enlaces/embeds **globales**
(`section_id` null) se excluyen del flipbook: siguen mostrándose en sus secciones propias fuera
del cuerpo de secciones (invariante para no perder funcionalidad).

---

## 3. Contrato de Datos (adaptado)

El flipbook **no carga datos**: consume los de `ActivityView`. `mount()` ya produce:

- `$this->activity` (`Activity`, con relaciones)
- `$this->sections` — `LmsActivitySection` con `visibleContents.media` (y preview trimeado a 1 sección)
- `$this->resources`, `$this->links`, `$this->htmlEmbeds`
- `$this->isPreview`, `$this->completed`, `$this->modoLectura`, `$this->showMascot`, `$this->mascotEmphasis`, `$this->isCommented`, `$this->hasDownload`

### Guardas de activación

```php
public bool $flipEnabled = false;   // true si count($sections) >= 2 && !$isPreview
public string $viewMode = 'scroll'; // 'scroll' | 'book' (estado de UI, no persiste)
```

- **Modo lectura (5–8, `modo_lectura=true`):** flipbook oculto; ese público sigue en `scroll`
  (la metáfora de volteo no es apta para 5–8, alineado con la spec «ui-mejoras-5-15»).
- **Preview (1 sola sección):** sin flipbook (no hay nada que voltear).
- **`viewMode`** es UI efímera; sin recalculo en servidor (§5 `wire:ignore.self`).

---

## 4. Firma del Componente (integrado en `ActivityView`)

```php
// app/Livewire/Student/Lms/ActivityView.php — NO se crea LessonBook, NO ruta nueva
public Activity $activity;
public $sections = [];      // ya cargada
public $resources = [];     // ya cargada
public $links = [];         // ya cargada
public $htmlEmbeds = [];    // ya cargada

public string $viewMode  = 'scroll';
public bool   $flipEnabled = false;

protected $queryString = []; // NO exponer viewMode en URL

public function mount(Activity $activity): void
{
    // ... (carga existente intacta)

    $this->flipEnabled = $this->sections->count() >= 2 && ! $this->isPreview
        && ! $this->modoLectura;
}
```

---

## 5. Vista Blade (integración en `activity-view.blade.php`)

> El archivo mide **1256 líneas**. NO se reemplaza: el flipbook se añade como bloque alternativo
> de renderizado, reutilizando el mismo switch de plantillas de contenido ya existente.

### 5.1 Envoltorio — coexistencia con `readingNav`

La raíz ya usa `x-data="readingNav()"`. Para no mezclar dos componentes de página, el flipbook se
**aisla** en un sub-árbol `wire:ignore.self` con su propio `x-data="lessonBook()"`. Así Livewire
no morfee ni destruya la instancia de StPageFlip, y el scroll-spy/progreso del modo scroll no se
rompen.

```blade
<div class="max-w-3xl mx-auto ..." x-data="readingNav()">
    {{-- Toggle de vista (solo si $flipEnabled) --}}
    @if($flipEnabled)
        <div class="flex items-center justify-end gap-1 mb-4" x-data>
            <button wire:key="vm-scroll"
                    @click="window.__lmsSetView && window.__lmsSetView('scroll')"
                    :class="window.__lmsView === 'scroll' ? 'bg-emerald-600 text-white' : ''">Desplazamiento</button>
            <button wire:key="vm-book"
                    @click="window.__lmsSetView && window.__lmsSetView('book')"
                    :class="window.__lmsView === 'book' ? 'bg-emerald-600 text-white' : ''">Libro</button>
        </div>
    @endif

    {{-- MODO DESPLAZAMIENTO (actual, intacto) --}}
    <div x-show="window.__lmsView !== 'book'" wire:key="view-scroll">
        @forelse($sections as $section)
            {{-- switch actual (TEXT/IMAGE/VIDEO/AUDIO/EMBED/FILE_PREVIEW/HTML) ...
                 NO TOCADO --}}
        @endforelse
    </div>

    {{-- MODO LIBRO (flipbook) --}}
    @if($flipEnabled)
    <div x-show="window.__lmsView === 'book'" x-cloak
         wire:ignore.self wire:key="view-book" x-data="lessonBook()"
         x-init="initFlipbook()">
        <div id="lms-flipbook-root">
            @foreach($sections as $index => $section)
                @include('livewire.student.lms._flipbook-page', [
                    'section'     => $section,
                    'resources'   => $resources,
                    'links'       => $links,
                    'htmlEmbeds'  => $htmlEmbeds,
                    'pageIndex'   => $loop->iteration,
                    'totalPages'  => $sections->count(),
                ])
            @endforeach
        </div>
    </div>
    @endif

    {{-- comentarios, footer, celebración — SIN CAMBIOS --}}
</div>
```

> `x-show` + `x-cloak` para no instanciar StPageFlip hasta que el usuario elija el modo libro.

### 5.2 `_flipbook-page.blade.php` (partial nuevo)

Una página = una `LmsActivitySection`. Reutiliza el **mismo switch de tipos extraído** a un
partial `_content-renderer.blade.php` (recomendado, para no duplicar ~18 plantillas); si se
prefiere no refactorizar, se incluye el partial con el mismo cuerpo que el bucle de scroll.

Estructura de página:

1. **Cabecera**: número de página + título de sección + badge INICIO/DESARROLLO/CIERRE
   (lógica existente) + título/descripción.
2. **Contenido**: `visibleContents` (+ media) con las plantillas existentes.
3. **Recursos/enlaces/embeds vinculados** (`section_id` coincide con la sección).
4. **Footer de página**: "Página X / N".

**⚠️ Mermaid + media dentro del flipbook:** StPageFlip **mueve** los nodos al contenedor
`html` layer; esto desmonta los SVGs Mermaid/KaTeX y reinicia `MutationObserver`. Para AC-07 y
AC-06 **no se incrusta** Mermaid/iframe dentro del flipbook: se muestra un placeholder
"Ver diagrama en modo desplazamiento" con `@click` que cambia a modo scroll y hace
`scrollIntoView` a `#seccion-{id}` (`readingNav.goTo`).

---

## 6. Especificación JavaScript (Alpine + `page-flip`)

### 6.1 Lazy loader — patrón de `resources/js/loaders.js`

```js
// resources/js/loaders.js
export async function loadPageFlip() {
    if (window._pageFlipPromise) return window._pageFlipPromise;
    window._pageFlipPromise = import('page-flip').then((m) => {
        window.PageFlip = m.PageFlip || m.default;
        return window.PageFlip;
    });
    return window._pageFlipPromise;
}
```

```js
// vite.config.js — rollupOptions.output.manualChunks
if (id.includes('node_modules/page-flip')) {
    return 'page-flip';   // AC-08: chunk propio
}
```

### 6.2 Registro Alpine

```js
// resources/js/lms-student-preview.js (+ @once script en la vista)
if (window.__lmsBookRegistered) return;
window.__lmsBookRegistered = true;

window.__lmsView = 'scroll';
window.__lmsSetView = function (v) {
    window.__lmsView = v;
    document.dispatchEvent(new Event('lms-view-changed'));
};

Alpine.data('lessonBook', () => ({
    book: null, current: 0, total: 0, ready: false,

    async initFlipbook() {
        await window.loadPageFlip();
        const root = document.getElementById('lms-flipbook-root');
        if (!root) return;

        // CDP: respetar prefers-reduced-motion & 2 páginas mínimas (sin cover de más)
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const pages = Array.from(root.querySelectorAll('.lb-page'));
        this.total = pages.length;
        if (this.total < 2) return;

        this.book = new window.PageFlip(root, {
            width: 1000, height: 700, size: 'fixed',
            showCover: false,
            drawShadow: !reduce,
            flippingTime: reduce ? 200 : 900,
            minWidth: 0, maxWidth: 1180, minHeight: 0, maxHeight: 800,
            mobileScrollSupport: true,
            autoSize: true,
            usePortrait: false,
        });

        this.book.loadFromHTML(root.querySelectorAll('.lb-page'));
        this.book.on('flip', (e) => {
            this.current = e.data;      // número de página (orientación)
            this.syncControls();
        });
        this.book.on('changeState', () => {
            // Re-sincronizar placeholders → enlaces a modos scroll ya resuelto
            this.syncControls();
        });

        // Recálculo al volverse visible (x-show congiere la instancia)
        document.addEventListener('lms-view-changed', () => {
            if (window.__lmsView === 'book' && this.book) {
                window.setTimeout(() => {
                    this.book.update();
                    this.book.turnToPage(Math.min(this.current, this.total - 1));
                }, 30);
            }
        });

        // CDP: reducir animaciones al cambiar de tema/pantalla
        window.addEventListener('resize', () => this.book?.update());
        this.ready = true;
    },

    syncControls() {
        // Pintar contador "Página X de N" + habilitar botones prev/next
    },

    next() { this.book.flipNext(); },
    prev() { this.book.flipPrev(); },
    goTo(i) { this.book.turnToPage(i); },
}));

// CDP + AC-09: limpiar scroll al desactivar modo libro
window.__lmsSetView('scroll'); // reset defensivo si el toggle se re-render
```

### 6.3 Limpieza en re-render de Livewire

Toda la instancia vive en un `x-data` con `wire:ignore.self`. Si un evento Livewire re-renderiza
la vista (p.ej. `saveComment`, `markComplete`), el sub-árbol `view-book` se conserva intacto; el
toggle `x-show` sigue apuntando a `window.__lmsView`. No se requiere teardown manual.

---

## 7. Estilos y Diseño (D2/Tailwind)

- Se añade el CSS de StPageFlip vía `aspect-ratio` media-safe. Se importa con `@import` en
  `resources/css/app.css` **desde `page-flip/dist/book_styles.css`** (o se copia un subset
  ajustado a la paleta). No romper el CSS global de `.lms-content` (ver §10).
- Paleta de página del libro: seguir la **paleta sujeto existente** (sky/emerald/amber/indigo/
  purple por `col_area`/`materiaKey`) para el marco y tema de la página.
- Tipografía: `font-display` (Nunito), `text-[17px] leading-7`, igual que `.lms-content`.
- **Dark mode** (`darkMode: 'class'`): define `.lb-page` y `.flipbook-shell` con variantes
  `dark:` consistentes (fondo `#0f172a`←`#1e293b`, texto `slate-100`). El CSS de StPageFlip usa
  colores fijos → sobrescribir con selectores propios bajo `html.dark .flipbook-shell`.
- Spinner de carga sobre el shell mientras `this.ready === false`.
- `prefers-reduced-motion`: `flippingTime` corto + `drawShadow:false` + desactivar animación de
  `page-flip` (CDP, ver §11 E2).

---

## 8. Navegación y UX

- **Navegación táctil/ratón:** la que da StPageFlip (flechas laterales, corner corners + drag).
- **Controles overlay**: `<` prev / `N/M` contador / `>` next + botón "Volver al inicio",
  "Modo desplazamiento", y **no** mostrar botones de completar dentro del flipbook (sigue fuera,
  en el footer común).
- **Toggle modo libro/scroll** por pestañas; el estado se guarda en `localStorage` (opcional) y
  siempre conserva `window.__lmsView` en memoria.
- Filtro del scroll-spy: en modo libro, desactivar la auto-sección activa del `readingNav` (la
  página se selecciona por índice, no por scroll).
- La **marquesina/mascota (5–8)** y la celebración siguen funcionando: al completar en modo
  libro se dispara el mismo `celebrate()` del footer común (sin confeti en reduced-motion).

---

## 9. Despliegue y Dependencias

1. `npm install page-flip` (paquete `page-flip` de StPageFlip).
2. `vite.config.js`: añadir el chunk `page-flip`.
3. `resources/js/loaders.js`: añadir `loadPageFlip()` + exponer `window.loadPageFlip`.
4. `resources/js/lms-student-preview.js` (o `@once` en la vista): registrar `lessonBook`.
5. `activity-view.blade.php`: toggle + `x-data="lessonBook()"` + bloque `view-book`.
6. `_flipbook-page.blade.php` (y, opcional, extraer `_content-renderer.blade.php`).
7. `npm run build` y verificar que `page-flip` sale en chunk separado.

---

## 10. Seguridad (AC)

- **AC-06 / XSS:** todo el contenido HTML (`.lms-content`, `html_content`, `body`) se renderiza
  **ya sanitizado** por DOMPurify y KaTeX vía los partials existentes (`math-text`). El flipbook
  **no reintroduce** ningún insertado crudo. Los enlaces llevan `rel="noopener noreferrer"
  target="_blank"` (como el código actual). No usar `page-flip` para incrustar HTML dinámico.
- **AC-07 (KaTeX CVE-2025-1390):** versiones de KaTeX/`math-text` ya parcheadas. El flipbook no
  añade numeración, no ejecuta `ans` custom; reutiliza `x-lms.math-text` idéntico al scroll.
- Verificar el CSP sin `unsafe-eval` (StPageFlip no lo necesita; no introducir `v-html`).

---

## 11. Accesibilidad y Movimiento (E2 / AC-05)

- **prefers-reduced-motion:** desactivar sombras/animaciones de volteo, `flippingTime` corto,
  navegación por `space`/`arrow` con `scroll-behavior:auto`.
- **Teclado:** flechas izq/der (`flipNext/flipPrev`), `Home`→inicio, `End`→última página,
  `Esc`→volver a modo scroll. Foco visible en controles.
- **Contraste altos** en headers/body (no solo color): badges INICIO/DESARROLLO/CIERRE con icono
  + texto.
- La navegación lateral se oculta hasta **≥2 páginas** (si una lección en preview se vuelve libro,
  desactivar).

---

## 12. Criterios de Aceptación (garantías funcionales y de regresión)

| ID | Criterio |
|----|----------|
| CA-01 | Los modos scroll y libro muestran **las mismas secciones** (mismo orden `sort_order`, misma visibilidad `is_visible`) |
| CA-02 | Recursos/enlaces/embeds vinculados se muestran en su página de sección; los **globales** (section_id null) **no** se duplican en el flipbook |
| CA-03 | Con <2 secciones, preview o `modo_lectura`, el toggle **no se muestra** (no hay guarda rota) |
| CA-04 | Cambiar a modo libro no requiere petición al servidor; `wire:ignore.self` aísla el sub-árbol |
| CA-05 | En modo libro el scroll-spy del `readingNav` **no** se envía a una sección del modo scroll |
| CA-06 | Mermaid/KaTeX no se incrustan en el flipbook; el placeholder enlaza al modo scroll (sin re-renderizar SVGs) |
| CA-07 | XSS: el flipbook no introduce renderizado crudo nuevo; sanitización delegada a DOMPurify/`math-text` |
| CA-08 | `page-flip` compila en chunk separado (`page-flip`) que se carga solo al entrar a modo libro |
| CA-09 | Completar la lección desde modo libro dispara la misma celebración/mascota del footer común |
| CA-10 | Dark-mode y paleta de sujeto se mantienen en el shell del libro |
| CA-11 | `prefers-reduced-motion`: sin sombras ni animación de volteo, contador accesible |

---

## 13. Fuera de Alcance (post-MVP)

- Volteo "z-depth"/3D real y página doble sincronizada (requiere rediseñar el layout por sección).
- Guardar posición de página en la progresión (`lms_activity_progress`) por lección.
- Mascota "guía de lectura" dentro del libro (más allá del placeholder actual).

---

## 14. Implementación en Producción (Playbook de Deploy)

> **Objetivo:** desplegar el visor en formato libro sin romper las funcionalidades existentes del área estudiante (modo scroll, comentarios, completar+celebración/mascota, Mermaid, KaTeX, WireUI) y con la capacidad de revierte el cambio de forma segura. **El paso crítico es la compilación del chunk `page-flip` (14.4) y la regresión de lo existente ANTES de validar lo nuevo (14.6).**

### 14.1 Pre-requisitos

- [ ] Acceso SSH/sudo al servidor (permisos sobre `php`, `composer`, `npm`/`node`, `artisan`, y el grupo PHP-FPM p.ej. `www-data`).
- [ ] Staging idéntico probado con CA-01…CA-11 y los playbooks 14.2→14.7.
- [ ] Ventana de mantenimiento en horario de bajo tráfico (fuera de 08:00–18:00 escolar).
- [ ] Tag de release anterior + comandos de rollback (14.8) probados en staging.

### 14.2 Backup de seguridad (SIEMPRE primero)

```bash
# 1) Backup de código (consistente con el tag actual)
cp -a /ruta/al/proyecto /backup/proyecto-$(date +%Y%m%d%H%M%S)

# 2) Backup de BD — método del proyecto: mysqldump, o el mecanismo interno /admin/database/backup
mysqldump -u USER -p PASSWORD DB > /backup/db-$(date +%Y%m%d%H%M%S).sql

# 3) Verificar que el backup NO está vacío y pesa lo esperado
ls -lh /backup/*.sql && head -10 /backup/*.sql

# 4) (Opcional) copia del .env por si cambia
cp .env /backup/.env-$(date +%Y%m%d%H%M%S)
```

### 14.3 Despliegue de código y PHP

```bash
cd /ruta/al/proyecto

git pull origin <rama-de-produccion>          # o tu CI/CD actual

# PHP en modo producción (sin dev deps)
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Esta feature NO añade migraciones nuevas.
# Si en el futuro las hay, son: 
#   php artisan migrate --force

# Cachés de Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan optimize
```

### 14.4 Compilación de assets (Vite) — PASO CRÍTICO

> El chunk **`page-flip`** debe salir como archivo **separado** en `public/build/assets/` y no mezclado con `mermaid`/`flowbite` (CA-08). Si el build falla o no aparece el chunk por separado, **NO continuar**.

```bash
cd /ruta/al/proyecto

npm ci                 # dependencias exactas
npm run build          # compila resources → public/build

# VERIFICAR el resultado:
ls -lash public/build/assets/
# Debe aparecer, p. ej.:  page-flip-<hash>.js
# y NO estar embebido dentro de  app-<hash>.js

# sanity del manifest
cat public/build/manifest.json | grep -i page-flip
```

### 14.5 Permisos y ownership

```bash
cd /ruta/al/proyecto

chown -R www-data:www-data storage bootstrap/cache public/build 2>/dev/null
chmod -R u+rwX,go+rX storage bootstrap/cache public/build 2>/dev/null
php artisan storage:link                 # enlaces storage públicas si existen
```

### 14.6 Verificación post-deploy (nuevo + regresión)

> **Regla de oro:** probar primero lo EXISTENTE y al final lo nuevo.

```bash
# 1) Health-check
curl -sI https://tu-dominio.com | head -5
php artisan about | head -20

# 2) Logs sin errores nuevos
tail -n 50 storage/logs/laravel.log
```

**Regresión de funcionalidades existentes (NO deben romperse):**

- [ ] Vista de lección por desplazamiento (scroll) se renderiza igual.
- [ ] Mermaid se sigue dibujando en modo scroll.
- [ ] Matemáticas KaTeX (`x-lms.math-text`) siguen renderizando.
- [ ] Comentarios: lista + guardar/enviar funcionan.
- [ ] Botón "Marcar como completada" + celebración + mascota (5–8) funcionan.
- [ ] `readingNav` (scroll-spy / progreso) en la vista scroll intacto.
- [ ] WireUI (modales/toasts) y enlaces con `rel="noopener noreferrer"` intactos.
- [ ] Resto del sitio: votos, censo, matrícula, blog (públicos) sin regresión.

**Validación de la nueva feature (flipbook):**

- [ ] Una lección con ≥2 secciones muestra el toggle "Libro".
- [ ] CA-01: mismas secciones en scroll y libro (orden y visibilidad).
- [ ] CA-03: sin toggle si <2 secciones, preview o modo lectura.
- [ ] CA-04: cambiar a modo libro sin petición al servidor (wire:ignore.self).
- [ ] CA-06: placeholders de Mermaid enlazan a modo scroll (sin SVGs rotos).
- [ ] Volteo responsive desktop + móvil (CA-04/CA-11).
- [ ] Dark-mode y paleta de sujeto correctos (CA-10).

### 14.7 Limpieza / hardening

```bash
cd /ruta/al/proyecto
composer dump-autoload --no-dev --optimize          # sin dev deps en prod
php artisan config:clear && php artisan config:cache
php artisan route:clear && php artisan route:cache

# Reiniciar workers de cola si tu release altera jobs
exit
```

### 14.8 Plan de ROLLBACK

> La causa más probable de regresión visual es el paso 14.4 (build de assets). Conocer la reversa evita caídas largas.

```bash
# 1) Código: volver al tag anterior
git checkout <tag-anterior>
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
php artisan config:cache && php artisan route:cache && php artisan view:cache

# 2) Assets: restaurar public/build del release anterior (o recompilar el tag anterior)
#    npm run build   (si el código anterior quedó)

# 3) BD: rollback solo migrations que añadiste en este deploy
#    php artisan migrate:rollback --step=<n>   (0 para esta feature)

# 4) .env si cambiaste variables
cp /backup/.env-<fecha> .env && php artisan config:cache

# 5) Health-check + logs limpios antes de dar por cerrado el reverse.
```

### 14.9 Checklist mínimo de planificación

- [ ] Ventana de mantenimiento acordada
- [ ] Backup de código + BD verificado (14.2)
- [ ] Staging probado con CA-01…CA-11
- [ ] Tag de release anterior etiquetado
- [ ] Comandos de rollback (14.8) probados en staging
- [ ] Notificación al equipo / usuarios del mantenimiento

---

## 15. Referencias a Archivos Clave

- `app/Livewire/Student/Lms/ActivityView.php` — datos existentes (`sections`, `resources`, `links`, `htmlEmbeds`, `isPreview`, `modoLectura`, `mascotEmphasis`, `markComplete`, `saveComment`).
- `resources/views/livewire/student/lms/activity-view.blade.php` — renderizado del cuerpo, Mermaid, KaTeX, `readingNav`, `celebration`, footer común (1256 líneas; **no reemplazar**).
- `app/Models/app/Academy/Activity.php` y `app/Models/app/Academy/Lms/*` — modelos reales.
- `resources/js/loaders.js` + `lms-student-preview.js` + `app.js` — patrón de lazy-load y registro Alpine.
- `vite.config.js` — `manualChunks` (mermaid, flowbite, tw-elements); añadir `page-flip`.
- `resources/views/components/lms/math-text.blade.php` — sanitización KaTeX/DOMPurify (invariante).
- `routes/web.php:432-445` — grupo `student.lms.` (sin ruta nueva).
