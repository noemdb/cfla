<div class="card card-primary mt-2">
    <div class="card-header">
        <h4 class=" pb-0 mb-0">Estudiantes </h4>
    </div>

    <div class="card-body pt-1">
        <small class="font-weight-bold pb-1">
            Encontrados: <span class="">{{$estudiants->count() ?? ''}}</span> ||
            Criterio de Búsqueda: <span class="font-italic">{{$search ?? ''}}</span>
        </small>
        <div class="row">
            @foreach($estudiants as $estudiant)
                {{-- estudiantes sin inscripcion academica --}}
                {{-- @if (empty($estudiant->getInscripcion()->id))  --}}
                    <div class="col-sm-4 col-md-3 col-lg-2 pl-1 p-1 ">
                        @include('administracion.configuraciones.plan_beneficos.deck.card.estudiant')
                    </div>
                {{-- @endif --}}
            @endforeach
        </div>
    </div>
</div>
