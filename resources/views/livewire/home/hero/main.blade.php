<div class="relative diagnostic-card rounded-xl overflow-hidden shadow-2xl h-[500px] sm:h-[600px] md:h-[700px] group"
    wire:ignore>

    <!-- Swiper -->
    <div class="swiper heroSwiper w-full h-full">
        <div class="swiper-wrapper">
            @foreach ($posts->take(4) as $item)
                @php $category = $item->category @endphp
                <div class="swiper-slide relative w-full h-full bg-gray-100 dark:bg-gray-900">

                    <!-- Background Image -->
                    <div class="absolute inset-0">
                        <img src="{{ asset($item->category_image_url) }}"
                            class="block w-full h-full object-cover opacity-90 transition-transform duration-[10s] ease-linear transform scale-100 swiper-slide-active:scale-110"
                            alt="{{ $item->title }}" />

                        <!-- Gradient Overlay -->
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-gray-100/30 via-gray-100/5 to-transparent dark:from-gray-900/30 dark:via-gray-900/5">
                        </div>
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-gray-100/60 to-transparent dark:from-gray-900/60">
                        </div>
                    </div>

                    <!-- Content Container -->
                    <div
                        class="absolute inset-0 flex flex-col justify-center px-4 sm:px-8 md:px-16 lg:px-24 max-w-5xl z-10 pointer-events-none">
                        <div class="pointer-events-auto">
                            <!-- Title Area -->
                            <div class="mb-3 sm:mb-6 opacity-0 translate-y-8 transition-all duration-700 delay-300"
                                data-hero-animate>
                                <div
                                    class="inline-flex items-center gap-2 px-3 py-1 mb-4 text-xs font-bold tracking-wider uppercase rounded-full backdrop-blur-md shadow-lg bg-emerald-100/90 text-emerald-800 border border-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-500/30">
                                    <span
                                        class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse"></span>
                                    Destacado
                                </div>
                                <div class="text-gray-900 dark:text-white drop-shadow-2xl">
                                    @include('livewire.home.hero.title')
                                </div>
                            </div>

                            <!-- Body Preview -->
                            <div class="mb-4 sm:mb-8 max-w-2xl opacity-0 translate-y-8 transition-all duration-700 delay-500"
                                data-hero-animate>
                                <div
                                    class="p-3 sm:p-6 rounded-xl sm:rounded-2xl shadow-2xl text-sm sm:text-lg leading-snug sm:leading-relaxed line-clamp-2 sm:line-clamp-3 backdrop-blur-lg bg-white/60 text-gray-800 border border-gray-200 dark:bg-gray-900/20 dark:text-gray-100 dark:border-white/10">
                                    {!! strip_tags($item->body) !!}
                                </div>
                            </div>

                            <!-- Footer / Actions -->
                            <div class="opacity-0 translate-y-8 transition-all duration-700 delay-700"
                                data-hero-animate>
                                @include('livewire.home.hero.footer')
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="swiper-pagination !bottom-4 sm:!bottom-8 !left-auto !right-4 sm:!right-8 !w-auto flex gap-2"></div>

        <!-- Navigation -->
        <div
            class="swiper-button-prev !left-8 !text-emerald-400 hover:!text-emerald-300 transition-colors hidden md:flex">
        </div>
        <div
            class="swiper-button-next !right-8 !text-emerald-400 hover:!text-emerald-300 transition-colors hidden md:flex">
        </div>
    </div>

</div>

@includeWhen($modalShow, 'livewire.home.modal.post')

<!-- Initialize Swiper -->
<script>
    document.addEventListener('livewire:initialized', () => {
        initHeroSwiper();
    });

    // Also init on load just in case Livewire isn't the trigger
    document.addEventListener('DOMContentLoaded', () => {
        initHeroSwiper();
    });

    function initHeroSwiper() {
        if (document.querySelector('.heroSwiper') && !document.querySelector('.heroSwiper').swiper) {
            new window.Swiper(".heroSwiper", {
                modules: [window.SwiperNavigation, window.SwiperPagination, window.SwiperAutoplay, window
                    .SwiperEffectFade
                ],
                effect: "fade",
                fadeEffect: {
                    crossFade: true
                },
                speed: 1000,
                autoplay: {
                    delay: 6000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                    renderBullet: function(index, className) {
                        return '<span class="' + className +
                            ' !bg-emerald-500 !w-3 !h-3 !opacity-70 hover:!opacity-100 transition-all"></span>';
                    },
                },
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                on: {
                    slideChangeTransitionStart: function() {
                        // Reset animations
                        document.querySelectorAll('[data-hero-animate]').forEach(el => {
                            el.classList.remove('opacity-100', 'translate-y-0');
                            el.classList.add('opacity-0', 'translate-y-8');
                        });
                    },
                    slideChangeTransitionEnd: function() {
                        // Trigger animations for active slide
                        const activeSlide = this.slides[this.activeIndex];
                        activeSlide.querySelectorAll('[data-hero-animate]').forEach(el => {
                            el.classList.remove('opacity-0', 'translate-y-8');
                            el.classList.add('opacity-100', 'translate-y-0');
                        });
                    },
                    init: function() {
                        // Trigger initial animation
                        setTimeout(() => {
                            const activeSlide = this.slides[this.activeIndex];
                            if (activeSlide) {
                                activeSlide.querySelectorAll('[data-hero-animate]').forEach(el => {
                                    el.classList.remove('opacity-0', 'translate-y-8');
                                    el.classList.add('opacity-100', 'translate-y-0');
                                });
                            }
                        }, 100);
                    }
                },
            });
        }
    }
</script>
