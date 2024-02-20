<div>
    @foreach ($posts->take(4) as $item)

        @php $category = $item->category; @endphp

        <x-card title="{{$category->name}}" class="pb-4">

            {{-- <img src="{{asset('image/categories/svg/'.$category->icon_svg)}}" class="block w-12" alt="Wild Landscape" /> --}}

            {{-- <x-icon name="{{$category->iconClass ?? null}}" class="w-12 h-12" /> --}}

            <div class="flex flex-col items-center sm:flex-row sm:items-start">
                <div class="flex-shrink-0 mr-4 sm:mr-0 flex-items-center">
                    <img src="{{asset('image/categories/svg/'.$category->icon_svg)}}" class="block w-12" alt="Wild Landscape" />
                </div>
                <div class="text-lg font-bold text-gray-700">
                    @include('livewire.home.featured.index')
                </div>
            </div>

        </x-card>    

    @endforeach

    @includeWhen($modalShow,'livewire.home.featured.modal.post')

</div>
