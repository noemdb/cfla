/**
 * Lazy loaders for heavy JavaScript libraries.
 *
 * These functions dynamically import large libraries only when needed,
 * reducing the initial bundle size for pages that don't use them.
 *
 * Usage in Blade/Livewire templates:
 *   if (window.loadSwiper) { await window.loadSwiper(); }
 *   new window.Swiper(...)
 */

/**
 * Dynamically import Mermaid (diagrams) — ~1.5 MB of diagram chunks
 * Used only in: profesor lesson-wizard, student activity-view
 */
export async function loadMermaid() {
    if (window._mermaidPromise) return window._mermaidPromise;

    window._mermaidPromise = import('mermaid').then((m) => {
        const mermaid = m.default || m;
        window.mermaid = mermaid;
        return mermaid;
    });

    return window._mermaidPromise;
}

/**
 * Dynamically import Swiper (carousels) — ~89 kB JS + CSS
 * Used only in: home hero, home information-highlighted, home important-information, lesson-wizard
 */
export async function loadSwiper() {
    if (window._swiperPromise) return window._swiperPromise;

    window._swiperPromise = Promise.all([
        import('swiper'),
        import('swiper/modules'),
        import('swiper/css'),
        import('swiper/css/navigation'),
        import('swiper/css/pagination'),
        import('swiper/css/effect-fade'),
    ]).then(([SwiperModule, modules]) => {
        const Swiper = SwiperModule.default || SwiperModule;
        window.Swiper = Swiper;
        window.SwiperNavigation = modules.Navigation;
        window.SwiperPagination = modules.Pagination;
        window.SwiperAutoplay = modules.Autoplay;
        window.SwiperEffectFade = modules.EffectFade;
        return Swiper;
    });

    return window._swiperPromise;
}

/**
 * Dynamically import Chart.js — ~198 kB
 * Currently not used in any template; kept for future use.
 */
export async function loadChart() {
    if (window._chartPromise) return window._chartPromise;

    window._chartPromise = import('chart.js/auto').then((c) => {
        const Chart = c.default || c;
        window.Chart = Chart;
        return Chart;
    });

    return window._chartPromise;
}
