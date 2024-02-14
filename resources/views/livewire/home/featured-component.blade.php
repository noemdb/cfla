<div>
    @foreach ($posts->take(4) as $item)

        @php $category = $item->category @endphp

        <x-card title="{{$category->name}}" class="pb-4">

            {{-- <img src="{{asset('image/categories/'.$category->icon.'.png')}}" class="block w-full" alt="Wild Landscape" /> --}}

            {{-- <x-icon name="{{$category->iconClass ?? null}}" class="w-12 h-12" /> --}}
                
            @include('livewire.home.featured.index')            
            
        </x-card>    

    @endforeach

    @includeWhen($modalShow,'livewire.home.featured.modal.post')

</div>
