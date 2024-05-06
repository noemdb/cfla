<div>
    <x-card>
    
        @slot('header')
            <h3 class=" bg-green-100 mb-4 mt-6 p-2 text-3xl font-bold text-neutral-800 dark:text-neutral-200">
                Experiencias
            </h3>
        @endslot
    
        <div class="mx-auto text-center md:max-w-xl lg:max-w-3xl">
            
            <h3 class="mb-2 text-3xl font-bold text-neutral-800 dark:text-neutral-200">
                Testimonios
            </h3>
            <p class="mb-2 pb-2 md:mb-12 md:pb-0">
                Le invitamos a conocer lo que nuestros padres y representantes tienen que decir!
            </p>
        </div>
    
        <!-- Container for the Testimonials -->
        @php $col = $testimonials->count() @endphp
        <div class="grid text-center md:grid-cols-{{$col ?? 1}} gap-6">

            @forelse ($testimonials->shuffle() as $item)
            <div class="mb-12 md:mb-0">
                <div class="mb-6 flex justify-center">
                    @include('svg.person-fill',['w'=>'64','h'=>'64'])
                </div>
                <h5 class="mb-4 text-xl font-semibold">{{$item->full_name}}</h5>
                {{-- <h6 class="mb-4 font-semibold text-primary dark:text-primary-500"> --}}
                    {{-- Lcda. Administración --}}
                {{-- </h6> --}}
                <p class="mb-4 ">
                    @include('svg.quotationOpen')
                    <div title="{{$item->alltext ?? null}}">
                        @php
                            $text_sm = Str::words($item->alltext,40);
                            $text = Str::words($item->alltext,150);
                        @endphp

                        <div x-data="{ isExpanded: false }">
                            <p>
                                <span x-show="!isExpanded">{{$text_sm}}</span>
                                <span x-show="isExpanded">{{$text}}</span>
                            </p>
                            <button class=" font-bold border border-gray-200 rounded shadow-sm p-2 m-2" x-on:click="isExpanded = !isExpanded">
                                <span x-show="!isExpanded">Leer más</span>
                                <span x-show="isExpanded">Leer menos</span>
                            </button>
                        </div>

                    </div>
                    @include('svg.quotationClose')
                </p>
    
                <ul class="mb-0 flex items-center justify-center">
                    <li>
                        @include('svg.start')
                    </li>
                    <li>
                        @include('svg.start')
                    </li>
                    <li>
                        @include('svg.start')
                    </li>
                    <li>
                        @include('svg.start')
                    </li>
                    <li>
                        @include('svg.start')
                    </li>
                </ul>
            </div>
            @empty
                <div>No hay testimonios</div>
            @endforelse
            <!-- First Testimonial -->
            
    

        </div>
    
    </x-card>
</div>
