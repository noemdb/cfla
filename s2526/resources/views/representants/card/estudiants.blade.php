{{-- <div class="card-deck"> --}}
{{-- @php $estudiants = ($representant) ? $representant->estudiants_formaly : collect(); @endphp
@foreach ($estudiants as $estudiant)

        <div class="card" >
            <div class="card-body">
                <p class="card-text small">
                    {{ $estudiant->short_name ?? '' }}
                    <hr class=" p-0 m-0">
                    <div class="small text-right font-weight-bold text-muted">
                        @php $inscripcion_name = $estudiant->getInscripcion()_name; @endphp
                        @if ($inscripcion_name) {{ $inscripcion_name ?? '' }} @else <span class=""> SIN INCRIPCIÓN ACADEMICA FORMALIZADA </span>@endif
                    </div>
                </p>
            </div>
        </div>
@endforeach --}}


<div class="alert-secondary rounded">
    <span class="small font-weight-bold text-muted text-uppercase">
        ESTUDIANTE(S)
    </span>
</div>


@php $estudiants = ($representant) ? $representant->estudiants_formaly : collect(); @endphp
@if ($estudiants->isNotEmpty())
    <div class=" pl-1 ml-1 text-dark">
        <ol class="ml-1 pl-1">
            @foreach ($estudiants as $estudiant)
            <dt class=" font-weight-bold">-. {{ $estudiant->short_name ?? '' }}</dt>
            @endforeach
        </ol>
    </div>
@endif
