<div class="p-2" style="background-color: #004400">

    <div class="text-xs md:text-sm lg:text-md xl:text-lg text-green-500 ">
        <div>{{$category->name ?? '098'}}</div>
        <div class="text-xs">Creado: {{$item->created_at ?? null}} || Actualizado: {{$item->updated_at ?? null}}</div>
    </div>

    <div class="flex justify-start">
        {{-- <a href="#">Más...</a> --}}
        <x-button sm info label="Más..." wire:click="showItem({{$item->id}})"/>
    </div>

</div>