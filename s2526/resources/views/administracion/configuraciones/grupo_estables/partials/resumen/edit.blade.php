<div class="text-right font-weight-bold card bd-callout bd-callout-dark px-1">
    <p class="text-dark">Resumen</p>
    <small class="px-1">
        <dl class="mb-1 ">
            <dt>Plan de Estudio</dt>
            @foreach ($asignatura->pensums as $pensum)
                <dd class="text-secondary">{{ $pensum->pestudio->name?? '' }}</dd>
            @endforeach
        </dl>
        <dl class="mb-1 ">
            <dt>Grados relacionados</dt>
            @foreach ($asignatura->pensums as $pensum)
                <dd class="text-secondary">{{ $pensum->grado->name?? '' }}</dd>
            @endforeach
        </dl>
        <dl class="mb-1 ">
            <dt>Profesores relacionados</dt>
            @foreach ($asignatura->pensums as $pensum)
                @php $profesors = $pensum->profesors @endphp
                @foreach ($profesors as $profesor)
                    <dd class="text-secondary">{{ $profesor->fullname ?? '' }}</dd>
                @endforeach
            @endforeach
        </dl>
        {{-- <dl class="mb-1 ">
            <dt>Baremos</dt>
            @foreach ($asignatura->pensums as $pensum)
                @foreach ($pensum->baremos as $baremo)
                    <dd class="text-secondary">{{ $baremo->valoracion . ' - ' . $baremo->description ?? '' }}</dd>
                @endforeach
            @endforeach
        </dl> --}}
    </small>
</div>
