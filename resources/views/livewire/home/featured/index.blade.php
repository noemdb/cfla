<h5 class="text-lg md:text-xl lg:text-2xl xl:text-3xl flex items-center text-{{$category->color_class ?? null}}-600">
    <x-icon name="{{$category->iconClass ?? null}}" class="w-6 h-6 mr-1" /> {{$item->title ?? null}}
</h5>

<div class="text-sm md:text-md lg:text-lg xl:text-xl">{{$item->description ?? null}}</div>

<div class="sm:block md:py-2 lg:py-4 text-xs border-t-2 mt-2 max-w-full overflow-hidden text-wrap word-break">
    {{ Str::limit($item->body,500,'...') ?? null }}
</div>

<div class="border-t-2 mt-1 text-xs md:text-sm lg:text-md xl:text-lg text-gray-400 ">
    <div class="text-xs">Creado: {{$item->created_at ?? null}} || Actualizado: {{$item->updated_at ?? null}}</div>
</div>

<div class="flex justify-start">
    <x-button sm info label="Más..." wire:click="showItem({{$item->id ?? null}})"/>
</div>