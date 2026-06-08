<div class="card card-primary mt-2">
    <div class="card-header alert-secondary">
        <h4 class=" pb-0 mb-0">
            Representantes
        </h4>

    </div>

    <div class="card-body pt-1">

        <small class="font-weight-bold pb-1">
            Encontrados: <span class="">{{$representants->count() ?? ''}}</span> ||
            Criterio de Búsqueda: <span class="font-italic">{{$search ?? ''}}</span>
        </small>

        <div class="row">
            @foreach($representants as $representant)
                <div class="col-sm-6 col-md-6 col-lg-4 pl-1 p-1 ">
                    @include('administracion.registropagos.deck.card.representant')
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
