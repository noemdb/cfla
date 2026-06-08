@extends('administracion.layouts.dashboard.app')

@section('title') - Gestión de Lista Negra de Representantes @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-info">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">
                    @include('administracion.representants.menus.index')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>
                    <i class="fas fa-exclamation-triangle"></i> 
                    <u title="Gestión completa de lista negra">Gestión de Lista Negra</u> - Representantes con Morosidad y Restricciones
                </h4>
            </div>

            <div class="card-body border-0 m-0 p-0">
                
                {{-- Componente Livewire Principal --}}
                <livewire:administracion.representant.black-list-component />

            </div>
        </div>
    </main>

@endsection
