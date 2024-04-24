<h5 class="text-lg md:text-xl lg:text-2xl xl:text-3xl"> <span class="text-sm text-gray-300">{{$loop->iteration ?? null}}. </span> {{$item->title ?? null}}</h5>

<div class="text-sm md:text-md lg:text-lg xl:text-xl">{{$item->description ?? null}}</div>

<div class="hidden sm:block md:py-2 lg:py-4 h-9 sm:h-24 md:h-44 lg:h-full xl:h-full text-xs border-t-2 mt-2 max-w-full overflow-hidden text-wrap word-break">
    {{ Str::limit($item->body,100,'...') ?? null }}
</div>

<div class="hidden sm:block md:py-2 lg:py-4 h-9 sm:h-24 md:h-44 lg:h-full xl:h-full text-xs border-t-2 mt-2 max-w-full overflow-hidden text-wrap word-break">
    {!! $item->insert ?? null !!}
</div>

<div class="border-t-2 mt-1 text-xs md:text-sm lg:text-md xl:text-lg text-green-500 ">
    <div class="text-xs">Creado: {{$item->created_at ?? null}} || Actualizado: {{$item->updated_at ?? null}}</div>
</div>

<div class="flex justify-start"><x-button sm info label="Más..." /></div>