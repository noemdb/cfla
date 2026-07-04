<div class="card card-primary mt-2">
    <div class="card-header">
        <h4 class=" pb-0 mb-0">
            Representantes

            <small class="text-default">
                {{-- <strong><span id="user_counter">{{$users->count()}}</span> Usuarios</strong> --}}
            </small>

            {{-- INI Menu rapido --}}
            {{-- <div class="btn-group float-right pt-2"> --}}

                {{-- @include('administracion.configuraciones.menus.index') --}}

            {{-- </div> --}}
            {{-- FIN Menu rapido --}}
        </h4>

    </div>

    <div class="card-body pt-1">
        <small class="font-weight-bold pb-1">
            Encontrados: <span class="">{{$representants->count() ?? ''}}</span> ||
            Criterio de Búsqueda: <span class="font-italic">{{$search ?? ''}}</span>
        </small>
        <div class="row">
            @foreach($representants as $representant) 
                <div class="col-sm-6 col-md-6 col-lg-4 pl-1 pr-1 pb-2">
                    @include('administracion.representants.deck.card.representant') 
                </div>                                 
            @endforeach
        </div>
    </div>
</div>