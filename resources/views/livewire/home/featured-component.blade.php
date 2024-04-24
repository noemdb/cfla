<div id="feature-category">

    {{-- <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div id="div_iz" class="p-4 bg-gray-200">
            <p class="text-lg font-medium">Tu texto aquí</p>
        </div>
        <div id="div_der" class="p-4 bg-gray-200">
            <img src="https://picsum.photos/200/300" alt="Imagen de ejemplo" class="w-full h-auto">
        </div>
    </div> --}}

    {{-- @foreach ($posts->take(4) as $item) --}}
    @foreach ($posts as $item)

        @php $category = $item->category; @endphp

        <!-- Contenedor principal -->
        <div class="flex flex-col sm:flex-row">
            <!-- Div izquierdo con texto -->
            <div id="div_iz" class="flex-1">
                
                <div class="item p-2">
                    <x-card title="{{$category->name}}" class="pb-4" id="feature">
                        <div class="flex flex-col items-center sm:flex-row sm:items-start">
                            <div class="flex-shrink-0 mr-4 sm:mr-0 flex-items-center">
                                <img src="{{asset('image/categories/svg/'.$category->icon_svg)}}" class="block w-12" alt="Wild Landscape" />
                            </div>
                            <div class="text-lg  ml-4 font-bold text-gray-700">
                                @include('livewire.home.featured.index')
                            </div>
                            <div class="ml-4 flex justify-center items-center h-full">
                                {{-- <img src="{{asset('image/categories/svg/'.$category->icon_svg)}}" class="block w-auto sm:w-auto" alt="Wild Landscape" /> --}}
                                <img src="{{asset($item->image_url)}}" class="block w-auto sm:w-auto rounded-lg shadow min-w-64" alt="Wild Landscape" />
                                
                            </div>
                        </div>
                    </x-card>
                </div>

            </div>

            <!-- Div derecho con imagen -->
            {{-- <div id="div_der" class="sm:w-auto sm:flex-shrink-0 flex justify-center items-center"> --}}
                {{-- <div class=""> --}}
                    {{-- <img src="{{asset('image/categories/svg/'.$category->icon_svg)}}" class="w-auto sm:w-auto" alt="Wild Landscape" /> --}}
                {{-- </div> --}}
            {{-- </div> --}}

        </div>



{{-- 
    <div class="container border rounded shadow-sm mb-2 p-2">
        <div class="grid grid-cols-1 md:grid-cols-2 sm:gap-2">
            <div class="item p-2">
                <x-card title="{{$category->name}}" class="pb-4" id="feature">

                    <div class="flex flex-col items-center sm:flex-row sm:items-start">
                        <div class="flex-shrink-0 mr-4 sm:mr-0 flex-items-center">
                            <img src="{{asset('image/categories/svg/'.$category->icon_svg)}}" class="block w-12"
                                alt="Wild Landscape" />
                        </div>
                        <div class="text-lg  ml-4 font-bold text-gray-700">
                            @include('livewire.home.featured.index')
                        </div>
                    </div>

                </x-card>
            </div>
            <div class="item flex justify-center items-center border rounded-sm shadow-sm m-2 p-2">
                <img src="{{asset('image/categories/svg/'.$category->icon_svg)}}" class="block w-12" alt="Wild Landscape" />
            </div>
        </div>
    </div>
 --}}




    @endforeach

    {{ $posts->links(data: ['scrollTo' => '#feature-category']) }}

    @includeWhen($modalShow,'livewire.home.featured.modal.post')

</div>