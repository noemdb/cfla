<div id="feature-category" class="space-y-8">

    {{-- @foreach ($posts->take(4) as $item) --}}
    @foreach ($posts as $item)
        @php $category = $item->category; @endphp
        @php $category_image_url = $item->category_image_url; @endphp

        <div
            class="group diagnostic-card relative bg-white/60 dark:bg-gray-900/40 backdrop-blur-sm border border-emerald-200 dark:border-emerald-500/30 rounded-xl overflow-hidden hover:border-emerald-500/60 transition-all duration-500 hover:shadow-2xl hover:shadow-emerald-500/10 hover:-translate-y-1">

            <div class="flex flex-col md:flex-row">

                <div class="p-6 flex-1 relative z-10">
                    <div class="flex items-center mb-4">
                        <div
                            class="flex-shrink-0 p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg border border-emerald-200 dark:border-emerald-500/20 group-hover:bg-emerald-200 dark:group-hover:bg-emerald-500/20 transition-colors duration-300">
                            @if ($category->icon_svg)
                                <img src="{{ asset('image/categories/svg/' . $category->icon_svg) }}"
                                    class="w-8 h-8 filter brightness-0 dark:invert group-hover:scale-110 transition-transform duration-300"
                                    alt="{{ $category->name }}" />
                            @else
                                <x-icon name="collection"
                                    class="w-8 h-8 text-emerald-600 dark:text-emerald-400 group-hover:rotate-12 transition-transform duration-300" />
                            @endif
                        </div>
                        <h3
                            class="ml-4 text-xl font-bold text-emerald-800 dark:text-emerald-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors duration-300">
                            {{ $category->name }}</h3>
                    </div>

                    <div class="text-gray-600 dark:text-gray-300">
                        @include('livewire.home.featured.index')
                    </div>
                </div>

                @if ($item->file_exist)
                    <div class="md:w-1/3 lg:w-1/4 relative min-h-[250px] md:min-h-0 overflow-hidden">
                        <img src="{{ asset($item->saefl_image_url) }}"
                            class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out"
                            alt="{{ $item->title }}" />
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-gray-50 via-gray-50/40 dark:from-gray-900 dark:via-gray-900/40 to-transparent md:bg-gradient-to-l md:via-gray-50/20 dark:md:via-gray-900/20">
                        </div>

                        <!-- Decoration line -->
                        <div
                            class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 to-transparent transform scale-x-0 group-hover:scale-x-100 transition-transform duration-700 origin-left">
                        </div>
                    </div>
                @endif

            </div>
        </div>
    @endforeach

    <div class="mt-8">
        {{ $posts->links(data: ['scrollTo' => '#feature-category']) }}
    </div>

    @includeWhen($modalShow, 'livewire.home.featured.modal.post')

</div>
