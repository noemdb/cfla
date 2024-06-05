<div>
    Grado: {{$grado->name}}
    <div>
        @forelse ($seccions as $item)

        <x-badge.circle lg label="{{$item->name}}" positive/>
        
        @empty
            <div>No hay secciones</div>
        @endforelse
    </div>
</div>

{{-- primary,secondary,positive,negative,warning,info,dark,secondary,positive,negative,primary --}}