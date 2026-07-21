/**
 * LMS Student Preview — Alpine.js components.
 *
 * Self-contained registration of the Alpine components needed by
 * the <x-lms.student-preview> Blade component.  Imported from app.js
 * so they are globally available — the student-preview component
 * works on any page without the caller having to register anything.
 *
 * Components registered:
 *   • lessonPreviewSwiper  — Swiper carousel for slide-by-slide navigation
 *   • mermaidEmbed         — Renders Mermaid diagrams on demand
 */

// ── DOMPurify — sanitiza HTML antes de inyectarlo al DOM ──────────
import DOMPurify from 'dompurify';

// ── Mermaid lazy initializer ─────────────────────────────────────────
// Ensures the mermaid library is loaded and initialized exactly once.
// Returns a promise that resolves when mermaid is ready to render.
window._ensureMermaidReady = function _ensureMermaidReady() {
    if (window._mermaidReadyPromise) return window._mermaidReadyPromise;

    window._mermaidReadyPromise = new Promise((resolve) => {
        if (typeof mermaid !== 'undefined' && window._mermaidInitialized) {
            resolve();
            return;
        }

        if (window.loadMermaid) {
            window.loadMermaid().then(() => {
                if (!window._mermaidInitialized && typeof mermaid !== 'undefined') {
                    mermaid.initialize({
                        startOnLoad: false,
                        suppressErrorRendering: true,
                        theme: 'base',
                        themeVariables: { fontFamily: 'inherit', fontSize: '14px' },
                    });
                    window._mermaidInitialized = true;
                }
                resolve();
            });
        } else {
            // loadMermaid not available — poll for mermaid
            const poll = setInterval(() => {
                if (typeof mermaid !== 'undefined') {
                    clearInterval(poll);
                    if (!window._mermaidInitialized) {
                        mermaid.initialize({
                            startOnLoad: false,
                            suppressErrorRendering: true,
                            theme: 'base',
                            themeVariables: { fontFamily: 'inherit', fontSize: '14px' },
                        });
                        window._mermaidInitialized = true;
                    }
                    resolve();
                }
            }, 50);
        }
    });

    return window._mermaidReadyPromise;
};

// ── Alpine component registrations ────────────────────────────────────
// Alpine.js is loaded by Livewire's script bundle, which executes AFTER
// this module (imported via @vite).  We use the `alpine:init` event to
// register components after Alpine is available — this makes the module
// self-contained regardless of script-load order.
// ── KaTeX via Vite (npm + dynamic import) ────────────────────────
// No CDN needed — KaTeX is installed via npm and loaded via Vite's
// dynamic import(). This creates a separate chunk that's only fetched
// when a page has math content — and once cached, loads instantly.
// The `@once` inline script in math-text.blade.php polls for
// renderMathInElement and resolves window._mathKatexReady.
(async function setupKatex() {
    try {
        await import('katex');
        var autoRender = await import('katex/dist/contrib/auto-render');
        // ESM no contamina window — asignamos manualmente para que el
        // @once inline script (polling) y las comprobaciones en render()
        // puedan detectar que está disponible.
        window.renderMathInElement = autoRender.default;
        // DOMPurify para sanitización — el @once inline también lo usará
        window.DOMPurify = window.DOMPurify || DOMPurify;
        await import('katex/dist/katex.min.css');
    } catch (e) {
        console.warn('[KATEX] Dynamic import via Vite failed:', e);
    }
})();

document.addEventListener('alpine:init', () => {

// ── Lesson Preview Swiper ────────────────────────────────────────
Alpine.data('lessonPreviewSwiper', () => ({
    currentSlide: 1,
    totalSlides: 1,
    _wait: null,

    init() {
        if (window.loadSwiper) window.loadSwiper();
        this._wait = setInterval(() => {
            const el = this.$el.querySelector('.swiper');
            if (el && el.querySelectorAll('.swiper-slide').length > 0 && window.Swiper) {
                clearInterval(this._wait);
                this._wait = null;
                this.totalSlides = el.querySelectorAll('.swiper-slide').length;
                requestAnimationFrame(() => this.initSwiper(el));
            }
        }, 50);
    },

    initSwiper(el) {
        if (!el || el.swiper) return;
        try {
            const self = this;
            new window.Swiper(el, {
                modules: [window.SwiperNavigation, window.SwiperPagination],
                slidesPerView: 1,
                spaceBetween: 0,
                direction: 'horizontal',
                speed: 350,
                autoHeight: true,
                keyboard: { enabled: true },
                noSwiping: true,
                allowTouchMove: false,
                on: {
                    slideChange() {
                        self.currentSlide = this.activeIndex + 1;
                        self.$nextTick(() => self.triggerMermaid());
                    },
                    init() {
                        self.currentSlide = 1;
                        self.$nextTick(() => self.triggerMermaid());
                    },
                },
            });
        } catch (e) {
            console.warn('[LESSON_PREVIEW] Swiper init error:', e);
        }
    },

    _getSwiper() {
        return this.$refs.swiperContainer?.swiper || null;
    },

    prev() {
        const s = this._getSwiper();
        if (s) s.slidePrev();
    },

    next() {
        const s = this._getSwiper();
        if (s) s.slideNext();
    },

    triggerMermaid() {
        const s = this._getSwiper();
        if (!s) return;
        const active = s.slides[s.activeIndex];
        if (active) {
            active.querySelectorAll('[data-mermaid-delay]').forEach((el) => {
                el.dispatchEvent(new CustomEvent('slide-active'));
            });
        }
    },

    toggleFullscreen() {
        const container = this.$el.closest('.student-preview-modal');
        if (!document.fullscreenElement) {
            container?.requestFullscreen?.();
        } else {
            document.exitFullscreen?.();
        }
    },

    destroy() {
        if (this._wait) clearInterval(this._wait);
        const s = this._getSwiper();
        s?.destroy?.();
        if (document.fullscreenElement) {
            document.exitFullscreen?.();
        }
    },
}));

// ── Mermaid Embed ────────────────────────────────────────────────────
Alpine.data('mermaidEmbed', () => ({
    zoom: 1,
    panX: 0,
    panY: 0,
    _container: null,
    _svg: null,
    _toolbar: null,
    _zoomDisplay: null,
    _isDragging: false,
    _dragStartX: 0,
    _dragStartY: 0,
    _dragPanX: 0,
    _dragPanY: 0,
    _touchDist: 0,
    _isFullscreen: false,

    init() {
        const code = this.$el.getAttribute('data-mermaid-code') || '';

        // Self-healing handler: waits for mermaid to be ready before rendering
        const handleSlideActive = async () => {
            await window._ensureMermaidReady();
            this.render(code);
        };

        if (this.$el.hasAttribute('data-mermaid-delay')) {
            this.$el.addEventListener('slide-active', handleSlideActive, { once: true });
        } else {
            window._ensureMermaidReady().then(() => this.$nextTick(() => this.render(code)));
        }
    },

    async render(code) {
        try {
            const id = 'mermaid-' + Date.now() + '-' + Math.random().toString(36).slice(2, 6);
            const { svg } = await mermaid.render(id, code);
            if (this.$refs.target) {
                this.$refs.target.innerHTML = svg;
                this.$nextTick(() => this.setupUI());
            }
        } catch (e) {
            console.warn('[MERMAID] Render error:', e.message || e);
        }
    },

    setupUI() {
        const target = this.$refs.target;
        if (!target) return;
        const svgEl = target.querySelector('svg');
        if (!svgEl) return;

        svgEl.style.display = 'block';
        svgEl.style.maxWidth = '100%';
        svgEl.style.height = 'auto';
        svgEl.style.margin = '0 auto';
        svgEl.style.cursor = 'grab';
        svgEl.style.userSelect = 'none';
        svgEl.style.pointerEvents = 'all';

        const container = svgEl.parentNode;
        container.style.position = 'relative';
        container.style.overflow = 'hidden';
        container.style.cursor = 'grab';
        this._container = container;
        this._svg = svgEl;

        container.addEventListener('mousedown', (e) => this._startDrag(e));
        document.addEventListener('mousemove', (e) => this._onDrag(e));
        document.addEventListener('mouseup', () => this._stopDrag());
        container.addEventListener('wheel', (e) => this._onWheel(e), { passive: false });
        container.addEventListener('dblclick', (e) => this._onDblClick(e));
        container.addEventListener('touchstart', (e) => this._onTouchStart(e), { passive: false });
        container.addEventListener('touchmove', (e) => this._onTouchMove(e), { passive: false });
        container.addEventListener('touchend', () => this._onTouchEnd());

        this._createToolbar();
        this._applyTransform();
    },

    _createToolbar() {
        const target = this.$refs.target;
        if (!target || target.parentNode.querySelector('.mermaid-zoom-toolbar')) return;

        const isDark = target.closest('.bg-slate-800, .bg-slate-900');
        const themeClass = isDark
            ? 'bg-slate-700/80 border-slate-600/50 text-slate-200'
            : 'bg-white/90 border-slate-200/80 text-slate-700 shadow-sm';

        const toolbar = document.createElement('div');
        toolbar.className =
            'mermaid-zoom-toolbar flex items-center gap-1 px-2 py-1.5 rounded-lg border ' +
            themeClass +
            ' absolute top-2 right-2 z-10 opacity-0 hover:opacity-100 transition-opacity duration-200';

        target.parentNode.addEventListener('mouseenter', () => {
            toolbar.style.opacity = '1';
        });
        target.parentNode.addEventListener('mouseleave', () => {
            if (this.zoom === 1 && this.panX === 0 && this.panY === 0)
                toolbar.style.opacity = '0';
        });

        toolbar.innerHTML =
            '<button class="zoom-act p-1 rounded hover:bg-black/10 dark:hover:bg-white/10 transition-colors" data-action="out" title="Reducir zoom (Ctrl+scroll)">' +
            '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>' +
            '</button>' +
            '<span class="zoom-pct text-[10px] font-mono min-w-[32px] text-center select-none">100%</span>' +
            '<button class="zoom-act p-1 rounded hover:bg-black/10 dark:hover:bg-white/10 transition-colors" data-action="in" title="Aumentar zoom (Ctrl+scroll)">' +
            '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>' +
            '</button>' +
            '<span class="w-px h-4 mx-0.5 bg-current opacity-20"></span>' +
            '<button class="zoom-act p-1 rounded hover:bg-black/10 dark:hover:bg-white/10 transition-colors zoom-fit" title="Ajustar al ancho">' +
            '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>' +
            '</button>' +
            '<button class="zoom-act p-1 rounded hover:bg-black/10 dark:hover:bg-white/10 transition-colors zoom-fs" title="Pantalla completa">' +
            '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>' +
            '</button>' +
            '<span class="w-px h-4 mx-0.5 bg-current opacity-20"></span>' +
            '<button class="zoom-act p-1 rounded hover:bg-black/10 dark:hover:bg-white/10 transition-colors zoom-reset" title="Restablecer zoom">' +
            '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 4v6h6m16 10v-6h-6M3.51 9a9 9 0 0114.85-3.36M20.49 15a9 9 0 01-14.85 3.36"/></svg>' +
            '</button>';

        toolbar.querySelector('[data-action="out"]').onclick = () => this._stepZoom(-1);
        toolbar.querySelector('[data-action="in"]').onclick = () => this._stepZoom(1);
        toolbar.querySelector('.zoom-fit').onclick = () => this._fitToWidth();
        toolbar.querySelector('.zoom-fs').onclick = () => this._toggleFullscreen();
        toolbar.querySelector('.zoom-reset').onclick = () => this._resetTransform();
        this._zoomDisplay = toolbar.querySelector('.zoom-pct');

        target.parentNode.appendChild(toolbar);
        this._toolbar = toolbar;

        if (this.zoom !== 1 || this.panX !== 0 || this.panY !== 0)
            toolbar.style.opacity = '1';
    },

    _applyTransform() {
        if (!this._svg || !this._container) return;
        const t = `translate(${this.panX}px, ${this.panY}px) scale(${this.zoom})`;
        this._svg.style.transform = t;
        this._svg.style.transformOrigin = 'top left';
        this._container.style.cursor =
            this.zoom > 1 || this.panX !== 0 || this.panY !== 0
                ? this._isDragging
                    ? 'grabbing'
                    : 'grab'
                : 'default';
        if (this._zoomDisplay) this._zoomDisplay.textContent = Math.round(this.zoom * 100) + '%';
        if (this._toolbar && this.zoom === 1 && this.panX === 0 && this.panY === 0) {
            this._toolbar.style.opacity = '0';
        }
    },

    _onWheel(e) {
        if (!e.ctrlKey && !e.metaKey) return;
        e.preventDefault();
        const dir = e.deltaY > 0 ? -1 : 1;
        const step = this.zoom * 0.12;
        const newZoom = Math.max(0.25, Math.min(6, this.zoom + dir * step));
        if (this._container && this._svg) {
            const rect = this._container.getBoundingClientRect();
            const mx = e.clientX - rect.left;
            const my = e.clientY - rect.top;
            const ratio = newZoom / this.zoom;
            this.panX = mx - (mx - this.panX) * ratio;
            this.panY = my - (my - this.panY) * ratio;
        }
        this.zoom = newZoom;
        this._applyTransform();
    },

    _startDrag(e) {
        if (e.button !== 0) return;
        if (this.zoom <= 1 && this.panX === 0 && this.panY === 0) return;
        this._isDragging = true;
        this._dragStartX = e.clientX;
        this._dragStartY = e.clientY;
        this._dragPanX = this.panX;
        this._dragPanY = this.panY;
        this._svg.style.cursor = 'grabbing';
        this._container.style.cursor = 'grabbing';
        this._container.style.userSelect = 'none';
    },

    _onDrag(e) {
        if (!this._isDragging) return;
        const dx = e.clientX - this._dragStartX;
        const dy = e.clientY - this._dragStartY;
        if (Math.abs(dx) < 3 && Math.abs(dy) < 3) return;
        this.panX = this._dragPanX + dx;
        this.panY = this._dragPanY + dy;
        this._applyTransform();
    },

    _stopDrag() {
        if (!this._isDragging) return;
        this._isDragging = false;
        if (this._svg) this._svg.style.cursor = 'grab';
        if (this._container) this._container.style.userSelect = '';
    },

    _onDblClick(e) {
        e.preventDefault();
        if (this._container && this._svg) {
            const rect = this._container.getBoundingClientRect();
            const mx = e.clientX - rect.left;
            const my = e.clientY - rect.top;
            const targetZoom = this.zoom >= 1.5 ? 1 : 2.5;
            const ratio = targetZoom / this.zoom;
            this.panX = mx - (mx - this.panX) * ratio;
            this.panY = my - (my - this.panY) * ratio;
            this.zoom = targetZoom;
            this._applyTransform();
        }
    },

    _onTouchStart(e) {
        if (e.touches.length === 1) {
            this._isDragging = true;
            this._dragStartX = e.touches[0].clientX;
            this._dragStartY = e.touches[0].clientY;
            this._dragPanX = this.panX;
            this._dragPanY = this.panY;
        } else if (e.touches.length === 2) {
            this._isDragging = false;
            const dx = e.touches[0].clientX - e.touches[1].clientX;
            const dy = e.touches[0].clientY - e.touches[1].clientY;
            this._touchDist = Math.sqrt(dx * dx + dy * dy);
        }
    },

    _onTouchMove(e) {
        e.preventDefault();
        if (e.touches.length === 1 && this._isDragging) {
            const dx = e.touches[0].clientX - this._dragStartX;
            const dy = e.touches[0].clientY - this._dragStartY;
            this.panX = this._dragPanX + dx;
            this.panY = this._dragPanY + dy;
            this._applyTransform();
        } else if (e.touches.length === 2) {
            const dx = e.touches[0].clientX - e.touches[1].clientX;
            const dy = e.touches[0].clientY - e.touches[1].clientY;
            const dist = Math.sqrt(dx * dx + dy * dy);
            if (this._touchDist > 0) {
                const ratio = dist / this._touchDist;
                const newZoom = Math.max(0.25, Math.min(6, this.zoom * ratio));
                if (this._container && this._svg) {
                    const rect = this._container.getBoundingClientRect();
                    const mx =
                        (e.touches[0].clientX + e.touches[1].clientX) / 2 - rect.left;
                    const my =
                        (e.touches[0].clientY + e.touches[1].clientY) / 2 - rect.top;
                    const r = newZoom / this.zoom;
                    this.panX = mx - (mx - this.panX) * r;
                    this.panY = my - (my - this.panY) * r;
                }
                this.zoom = newZoom;
                this._applyTransform();
            }
            this._touchDist = dist;
        }
    },

    _onTouchEnd() {
        this._isDragging = false;
    },

    _stepZoom(dir) {
        const step = this.zoom * 0.2;
        this.zoom = Math.max(0.25, Math.min(6, this.zoom + dir * step));
        this._applyTransform();
    },

    _fitToWidth() {
        if (!this._svg || !this._container) return;
        const cw = this._container.clientWidth;
        const origMaxW = this._svg.style.maxWidth;
        this._svg.style.maxWidth = 'none';
        const natW =
            this._svg.scrollWidth ||
            this._svg.getBoundingClientRect().width ||
            cw;
        this._svg.style.maxWidth = origMaxW || '100%';
        if (natW > 0 && cw > 0) {
            this.zoom = Math.max(0.25, Math.min(2, (cw - 32) / natW));
        } else {
            this.zoom = 1;
        }
        this.panX = 0;
        this.panY = 0;
        this._applyTransform();
    },

    _toggleFullscreen() {
        const fsEl = this.$el;
        if (!document.fullscreenElement) {
            fsEl?.requestFullscreen?.();
            this._isFullscreen = true;
        } else {
            document.exitFullscreen?.();
            this._isFullscreen = false;
        }
    },

    _resetTransform() {
        this.zoom = 1;
        this.panX = 0;
        this.panY = 0;
        this._applyTransform();
        if (this._toolbar) this._toolbar.style.opacity = '0';
    },
}));

// ── Math Content (KaTeX) — Mermaid-like pattern ────────────────────
// Renders LaTeX on the client using KaTeX, following the same
// architecture as mermaidEmbed: wire:ignore protects the rendered
// output, data-math-content stores the raw HTML, and a MutationObserver
// detects attribute changes from Livewire morphs.
Alpine.data('mathContent', () => ({
    _content: '',
    _observer: null,

    init() {
        this._content = this.$el.getAttribute('data-math-content') || '';
        this._ensureKatex().then(() => this.$nextTick(() => this.render()));

        // MutationObserver: detecta cambios en data-math-content
        // (Misma tónica que el patrón Mermaid: wire:ignore protege el
        // renderizado, y el observer reacciona a cambios del atributo.)
        this._observer = new MutationObserver(() => {
            var newContent = this.$el.getAttribute('data-math-content') || '';
            if (newContent !== this._content) {
                this._content = newContent;
                this.$nextTick(() => this.render());
            }
        });
        this._observer.observe(this.$el, {
            attributes: true,
            attributeFilter: ['data-math-content'],
        });
    },

    async _ensureKatex() {
        return window._mathKatexReady;
    },

    render() {
        var target = this.$refs?.target;
        if (!target) return;

        // Sanitizar antes de inyectar al DOM (XSS prevention)
        var __html = DOMPurify.sanitize(this._content);

        // Parchar vulnerabilidad conocida de KaTeX:
        // CVE-2025-1390 — macros \htmlData, \htmlClass, \htmlStyle
        // permiten inyectar atributos HTML arbitrarios (como onclick)
        // en el output renderizado. DOMPurify no puede proteger contra
        // esto porque son macros de LaTeX, no HTML.
        // Parche: eliminar estas macros del HTML antes de que KaTeX las procese.
        // KaTeX 0.16.21+ corrige esto; parche removible al actualizar.
        __html = __html.replace(/\\html(?:Data|Class|Style)\s*\{[^}]*\}\s*\{[^}]*\}/g, '');

        target.innerHTML = __html;

        if (!window.renderMathInElement) return;

        try {
            renderMathInElement(target, {
                delimiters: [
                    {left:'\\(', right:'\\)', display:false},
                    {left:'$$',   right:'$$',   display:true},
                    {left:'\\[',  right:'\\]',  display:true},
                    {left:'$',    right:'$',    display:false},
                ],
                throwOnError: false,
            });
        } catch (e) {
            if (window.console) console.warn('[KATEX]', e);
        }
    },

    destroy() {
        if (this._observer) this._observer.disconnect();
    },
}));

// Flag para que el @once inline script del componente sepa
// que ya está registrado y no duplique el Alpine.data().
Alpine._mathContentRegistered = true;
}); // end alpine:init
