<div class="container-fluid mx-auto px-2 sm:px-2 lg:px-2 py-2">
    <div
        class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center bg-white dark:bg-gray-900/40 rounded-[2.5rem] p-6 sm:p-10 shadow-xl border border-emerald-100/50 dark:border-emerald-900/20">

        <!-- Left Column: Image Slider -->
        <div class="relative rounded-3xl overflow-hidden shadow-2xl h-[350px] sm:h-[450px] md:h-[550px] group border border-gray-100 dark:border-white/5 bg-gray-50 dark:bg-gray-800/50"
            wire:ignore>

            @if (count($images) > 0)
                <div class="swiper importantSwiper w-full h-full">
                    <div class="swiper-wrapper">
                        @foreach ($images as $image)
                            <div class="swiper-slide relative w-full h-full flex items-center justify-center p-4">
                                <img src="{{ $image }}"
                                    class="max-w-full max-h-full object-contain drop-shadow-2xl transition-transform duration-700 group-hover:scale-[1.03]"
                                    alt="Comunicado Resaltado" />
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if (count($images) > 1)
                        <div class="swiper-pagination important-pagination !bottom-4"></div>
                        <!-- Navigation Controls -->
                        <div
                            class="swiper-button-prev important-prev !left-4 !w-10 !h-10 !bg-white/90 dark:bg-gray-800/90 backdrop-blur-md !rounded-full !text-emerald-600 after:!text-lg shadow-lg hover:!bg-emerald-500 hover:!text-white transition-all duration-300 opacity-0 group-hover:opacity-100 flex items-center justify-center">
                        </div>
                        <div
                            class="swiper-button-next important-next !right-4 !w-10 !h-10 !bg-white/90 dark:bg-gray-800/90 backdrop-blur-md !rounded-full !text-emerald-600 after:!text-lg shadow-lg hover:!bg-emerald-500 hover:!text-white transition-all duration-300 opacity-0 group-hover:opacity-100 flex items-center justify-center">
                        </div>
                    @endif
                </div>
            @else
                <!-- Placeholder / Empty State -->
                <div
                    class="w-full h-full flex flex-col items-center justify-center bg-emerald-50 dark:bg-emerald-950/20 p-8 text-center">
                    <div
                        class="w-24 h-24 mb-4 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-500">
                        <i class="bx bx-image-alt text-5xl"></i>
                    </div>
                    <h3
                        class="text-xl font-bold text-gray-900 dark:text-white mb-2 underline decoration-emerald-500 underline-offset-4">
                        Comunicados Próximamente</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-xs">En este espacio compartiremos la
                        información más relevante para nuestra comunidad.</p>
                </div>
            @endif

            <!-- Floating Badge -->
            {{-- <div class="absolute top-4 left-4 z-20 pointer-events-none">
                <div
                    class="flex items-center gap-2 px-4 py-2 text-xs font-bold tracking-widest uppercase rounded-full backdrop-blur-xl bg-emerald-500 text-white shadow-xl border border-white/20">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-200 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                    </span>
                    Información Al Día
                </div>
            </div> --}}
        </div>

        <!-- Right Column: Complementary Text -->
        <div class="space-y-6 lg:space-y-8">
            <div class="space-y-2">
                <h6 class="text-emerald-600 dark:text-emerald-400 font-bold tracking-widest text-sm uppercase">Comunidad
                    Amigoniana</h6>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 dark:text-white leading-tight">
                    PAZ Y <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500">BIEN</span>
                </h2>
                <div class="h-1.5 w-24 bg-gradient-to-r from-emerald-500 to-teal-400 rounded-full"></div>
            </div>

            <div class="prose dark:prose-invert max-w-none">
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Estimado Padre y Representante:</p>
                <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                    A partir del <span class="font-bold text-gray-900 dark:text-white">LUNES 02 DE MARZO DE 2026</span>
                    el uso de teléfonos celulares está
                    <span
                        class="text-yellow-600 dark:text-yellow-400 font-bold uppercase underline decoration-2 underline-offset-4">estrictamente
                        prohibido</span>
                    en todos los espacios del colegio, incluyendo clases y recesos.
                </p>

                <div
                    class="mt-4 p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-white/5">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3 text-center">Protocolo por
                        Incumplimiento</p>
                    <div class="grid grid-cols-1 gap-3">
                        <div
                            class="flex items-center gap-3 p-2 rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-emerald-100 dark:border-emerald-900/20">
                            <div
                                class="w-8 h-8 flex-shrink-0 flex items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-600 dark:text-emerald-400">
                                <i class="bx bx-notification text-lg"></i>
                            </div>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Notificación inmediata al
                                representante.</span>
                        </div>
                        <div
                            class="flex items-center gap-3 p-2 rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-emerald-100 dark:border-emerald-900/20">
                            <div
                                class="w-8 h-8 flex-shrink-0 flex items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-600 dark:text-emerald-400">
                                <i class="bx bx-user-voice text-lg"></i>
                            </div>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Retiro del equipo por
                                parte del representante.</span>
                        </div>
                        <div
                            class="flex items-center gap-3 p-2 rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-emerald-100 dark:border-emerald-900/20">
                            <div
                                class="w-8 h-8 flex-shrink-0 flex items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-600 dark:text-emerald-400">
                                <i class="bx bx-edit text-lg"></i>
                            </div>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Firma de convenio de
                                cumplimiento.</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-white/5 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase tracking-tighter">Atentamente,</p>
                        <p class="text-xs font-bold text-gray-900 dark:text-white">Lcda. Escarleth López</p>
                        <p class="text-[10px] text-emerald-600 font-semibold">Directora Académica</p>
                    </div>
                    <div class="opacity-20 flex-shrink-0">
                        <i class="bx bxs-check-shield text-4xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .important-pagination .swiper-pagination-bullet {
        width: 8px;
        height: 8px;
        background: #10b981 !important;
        opacity: 0.3;
        transition: all 0.3s ease;
    }

    .important-pagination .swiper-pagination-bullet-active {
        width: 24px;
        border-radius: 4px;
        opacity: 1;
    }

    .important-prev,
    .important-next {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
</style>

<script>
    document.addEventListener('livewire:initialized', () => initImportantSwiper());
    document.addEventListener('DOMContentLoaded', () => initImportantSwiper());

    function initImportantSwiper() {
        const swiperEl = document.querySelector('.importantSwiper');
        if (swiperEl && !swiperEl.swiper && window.Swiper) {
            const slides = swiperEl.querySelectorAll('.swiper-slide');
            const slideCount = slides.length;
            console.log('ImportantSwiper: Found ' + slideCount + ' slides');

            if (slideCount > 0) {
                const canLoop = slideCount > 1;
                new window.Swiper(".importantSwiper", {
                    modules: [
                        window.SwiperNavigation,
                        window.SwiperPagination,
                        window.SwiperAutoplay,
                        window.SwiperEffectFade
                    ],
                    effect: "fade",
                    fadeEffect: {
                        crossFade: true
                    },
                    speed: 800,
                    loop: canLoop,
                    autoplay: canLoop ? {
                        delay: 5000,
                        disableOnInteraction: false,
                    } : false,
                    pagination: canLoop ? {
                        el: ".important-pagination",
                        clickable: true,
                    } : false,
                    navigation: canLoop ? {
                        nextEl: ".important-next",
                        prevEl: ".important-prev",
                    } : false,
                });
            }
        } else if (!window.Swiper) {
            console.error('ImportantSwiper: Library not found');
        }
    }
</script>
