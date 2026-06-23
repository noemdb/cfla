<div class="card card-primary mt-2">
        <div class="card-header">
            <h4>
                Representante<br>
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
                Encontrados: <span class="">{{$representants->count()}}</span> ||
                Criterio de Búsqueda: <span class="font-italic">{{$search}}</span>
            </small>    
            <div class="row">
                @foreach($representants as $representant)
    
                    @include('administracion.representants.card') 
                    
                @endforeach
            </div>
    
        </div>
    </div>
        
    
    @section('scripts')
        @parent
    
    @endsection
    
    @section('style')
        @parent
    @endsection
    