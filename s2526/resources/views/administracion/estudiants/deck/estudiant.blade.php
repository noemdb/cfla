<div class="card card-primary mt-2">
    <div class="card-header">
        <h4 class=" pb-0 mb-0">
            Estudiantes
            {{-- <br> --}}

            {{-- INI Menu rapido --}}
            {{-- <div class="btn-group float-right pt-2"> --}}

            {{-- @include('administracion.configuraciones.menus.index') --}}

            {{-- </div> --}}
            {{-- FIN Menu rapido --}}

        </h4>

    </div>

    <div class="card-body pt-1">
        <small class="font-weight-bold pb-1">
            Encontrados: <span class="">{{$estudiants->count()}}</span> ||
            Criterio de Búsqueda: <span class="font-italic">{{$search}}</span>
        </small>

        {{-- Mensaje session-flash sobre operaciones con base de datos --}}
        {{-- @include('administracion.elements.messeges.oper_ok') --}}

        <div class="row">
            @foreach($estudiants as $estudiant)
                <div class="col-sm-6 col-md-6 col-lg-4 pl-1 pr-1 pb-2">
                    @include('administracion.estudiants.deck.card.estudiant')
                </div>
            @endforeach
        </div>

    </div>
</div>


@section('scripts')
@parent

{{-- INI script ajax json models --}}
{{-- <script src="{{ asset("js/models/users/delete.js") }}"></script> --}}
{{-- FIN script ajax json models --}}

@endsection

@section('style')
@parent
@endsection
