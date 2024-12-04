@extends('layouts.home')

@section('title', 'C.E. Colegio Fray Luis Amigó')

@section('header') <livewire:home.header-component  /> @endsection

{{-- @section('hero') <livewire:home.hero-component /> @endsection --}}

@section('highlighted')

@php $category = $post->category; $url = $post->category_image_url @endphp

<div class="relative bg-cover bg-center bg-no-repeat p-6" style="background-image: url('{{asset($url)}}');">
    <div class="absolute inset-0 bg-white/80"></div>
    <div class="relative z-10">
        <div class="text-2xl font-bold">
            <h5 class="text-lg md:text-xl lg:text-2xl xl:text-3xl flex items-center text-{{$category->color_class ?? null}}-600">
                <x-icon name="{{$category->iconClass ?? null}}" class="w-12 h-12 mr-1" /> {{$post->title ?? null}}
            </h5>
        </div>
        <p class="mt-2">
            <div class="p-1 rounded-sm flex justify-between items-center">
                <div class="text-blue-800">
                    <span class="text-lg font-light">
                        {{$post->description ?? null}}
                    </span>
                    <br>
                    <div class="text-xs text-gray-400 text-left">
                        Creado: {{$post->created_at ?? null}} || Actualizado: {{$post->updated_at ?? null}}
                    </div>         
                </div> 
            </div>
            
            <div class="text-xs border-t-2 mt-2 max-w-full overflow-hidden text-wrap word-break">
                {{ $post->body,100 ?? null }}
            </div>
            
            <div class="text-xs border-t-2 mt-2 max-w-full overflow-hidden text-wrap word-break">
                {!! $post->insert ?? null !!}
            </div>
            
            @if ($post->saefl_image_url)                            
                <div id="footer-out" class="flex justify-center">
                    <img src="{{asset($post->saefl_image_url)}}" class="border-b-2 block rounded-lg shadow-lg" alt="Wild Landscape" />
                </div> 
            @endif
        </p>
    </div>
</div>

</div>



@endsection

@section('footer')  @include('home.footer.main') @endsection