<div id="feature-category">

    {{-- @foreach ($posts->take(4) as $item) --}}
    @foreach ($posts as $item)

        @php $category = $item->category; @endphp
        @php $category_image_url = $item->category_image_url; @endphp

        <div class="flex flex-col sm:flex-row">
            <div id="div_iz" class="flex-1">
                
                <div class="item p-2">
                    <x-card title="{{$category->name}}" class="pb-4" id="feature">
                        <div class="flex flex-col items-center sm:flex-row sm:items-start">
                            <div class="flex-none mr-4 sm:mr-0 flex-items-center">
                                <img src="{{asset('image/categories/svg/'.$category->icon_svg)}}" class="block w-12" alt="Wild Landscape" />
                            </div>
                            <div class="text-lg grow ml-4 font-bold text-gray-700">
                                @include('livewire.home.featured.index')
                            </div>
                            @if ($item->file_exist)                                
                            <div class="ml-4 flex-none justify-center items-center h-full">
                                <img src="{{asset($item->saefl_image_url)}}" class="block w-auto sm:w-auto rounded-lg shadow min-w-64" alt="Wild Landscape" />                                
                            </div>
                            @endif
                        </div>
                    </x-card>
                </div>

            </div>
        </div>

    @endforeach

    {{ $posts->links(data: ['scrollTo' => '#feature-category']) }}

    @includeWhen($modalShow,'livewire.home.featured.modal.post')

</div>