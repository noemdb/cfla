{{--
    Listado en tarjetas. El estado se calcula una vez por tarjeta desde el
    servicio (sin consultas extra: los datos ya vienen en el paginador).
--}}
@php
    $servicio = app(\App\Services\Inicial\SeguimientoInicial::class);
@endphp

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @foreach ($documentos as $documento)
        @php
            $estado = $servicio->estado($entidad, $documento);
        @endphp

        @if ($entidad === 'eiplanningwks')
            @include('livewire.inicial.seguimiento.partials.card-eiplanningwk', [
                'documento' => $documento,
                'estado' => $estado,
            ])
        @else
            @include('livewire.inicial.seguimiento.partials.card-documento', [
                'documento' => $documento,
                'estado' => $estado,
            ])
        @endif
    @endforeach
</div>
