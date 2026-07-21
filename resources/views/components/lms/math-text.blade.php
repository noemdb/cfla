@props([
    'content' => '',
    'as' => 'div',
])

{{-- Auto‑registro del Alpine component y bootstrap de KaTeX —        --}}
{{-- hace que el componente funcione incluso sin lms-student-preview.js --}}
@once
    <script>
        {{-- KaTeX bootstrap — espera por Vite (npm + dynamic import) --}}
        {{-- El módulo lms-student-preview.js carga KaTeX vía           --}}
        {{-- import() dinámico. Este script espera a que                 --}}
        {{-- renderMathInElement esté disponible y resuelve la promesa.  --}}
        {{-- Si Vite no carga en 5s, resuelve igual (degradación        --}}
        {{-- gradual — el contenido se muestra sin símbolos).           --}}
        window._mathKatexReady = window._mathKatexReady || new Promise(function (resolve) {
            if (typeof renderMathInElement !== 'undefined') { resolve(); return; }

            var timeout = setTimeout(function () {
                if (window.console) console.warn('[KATEX] Vite chunk timed out — math will not render');
                resolve();
            }, 5000);

            (function pollKatex() {
                if (typeof renderMathInElement !== 'undefined') {
                    clearTimeout(timeout);
                    resolve();
                    return;
                }
                setTimeout(pollKatex, 50);
            })();
        });

        // ── Alpine component mathContent ───────────────────────────
        document.addEventListener('alpine:init', function () {
            if (Alpine._mathContentRegistered) return;
            Alpine._mathContentRegistered = true;

            Alpine.data('mathContent', function () {
                return {
                    _content: '',
                    _observer: null,

                    init: function () {
                        this._content = this.$el.getAttribute('data-math-content') || '';
                        var self = this;
                        this._ensureKatex().then(function () {
                            self.$nextTick(function () { self.render(); });
                        });

                        this._observer = new MutationObserver(function () {
                            var newContent = self.$el.getAttribute('data-math-content') || '';
                            if (newContent !== self._content) {
                                self._content = newContent;
                                self.$nextTick(function () { self.render(); });
                            }
                        });
                        this._observer.observe(this.$el, {
                            attributes: true,
                            attributeFilter: ['data-math-content'],
                        });
                    },

                    _ensureKatex: async function () { return window._mathKatexReady; },

                    render: function () {
                        var target = this.$refs && this.$refs.target;
                        if (!target) return;

                        // Sanitizar antes de inyectar al DOM (XSS prevention)
                        // Usa DOMPurify si está disponible (Vite bundle) o un
                        // fallback regex si el componente se usa sin el bundle.
                        var __html = this._content;
                        if (window.DOMPurify) {
                            __html = window.DOMPurify.sanitize(__html);
                        } else {
                            // Fallback: más robusto que solo <script> tags
                            __html = __html
                                .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '')
                                .replace(/<iframe\b[^>]*>.*?<\/iframe\s*>/gi, '')
                                .replace(/<object\b[^>]*>.*?<\/object\s*>/gi, '')
                                .replace(/<embed\b[^>]*\/?>/gi, '')
                                .replace(/ on\w+\s*=\s*(?:"[^"]*"|'[^']*'|[^\s>]+)/gi, '')
                                .replace(/ javascript\s*:/gi, '')
                                .replace(/<form\b[^>]*>.*?<\/form\s*>/gi, '');
                        }

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

                    destroy: function () {
                        if (this._observer) this._observer.disconnect();
                    },
                };
            });
        });
    </script>
@endonce

<{{ $as }}
    x-data="mathContent()"
    data-math-content="{{ $content }}"
    {{ $attributes }}
>
    <div wire:ignore>
        <div x-ref="target"></div>
    </div>
</{{ $as }}>
