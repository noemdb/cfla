<div id="feature-category">
    {{-- @foreach ($posts->take(4) as $item) --}}
    @foreach ($posts as $item)

        @php $category = $item->category; @endphp

        <x-card title="{{$category->name}}" class="pb-4" id="feature">

            <div class="flex flex-col items-center sm:flex-row sm:items-start">
                <div class="flex-shrink-0 mr-4 sm:mr-0 flex-items-center">
                    <img src="{{asset('image/categories/svg/'.$category->icon_svg)}}" class="block w-12" alt="Wild Landscape" />
                </div>
                <div class="text-lg  ml-4 font-bold text-gray-700">
                    @include('livewire.home.featured.index')
                </div>
            </div>

        </x-card>    

    @endforeach

    {{ $posts->links(data: ['scrollTo' => '#feature-category']) }}

    @includeWhen($modalShow,'livewire.home.featured.modal.post')

</div>
