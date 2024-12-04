<x-modal.card title="{{$post->title ?? null}}" blur wire:model="modalShow" max-width="6xl">

    <div class="bg-blue-100 p-1 rounded-sm flex justify-between items-center">
        <div class="text-blue-600">
            <span class="text-lg font-light">
                {{$post->description ?? null}}.
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

</x-modal.card>