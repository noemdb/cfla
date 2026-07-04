@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-secondary">
                <div class="btn-group float-right pt-0 pb-2">
                    @component('elements.buttons.default')
                        @slot('title', 'Crear nuevo boletin')
                        @slot('class_bt', 'primary')
                        @slot('route', route('administracion.boletins.create'))
                        @slot('icon', $icon_menus['nuevo'])
                    @endcomponent
                </div>

                <h4>Carga de Notas <b class="text-dark">POR ESTUDIANTE</b></h4>

            </div>

            <div class="card-body bd-callout bd-callout-{{$grado->color ?? 'default'}} p-2 m-2">

                    @include('administracion.elements.forms.errors')

                    @include('administracion.elements.messeges.oper_ok')

                    @include('administracion.boletins.partials.search',['route'=>'administracion.boletins.index','required_seccion'=>true])

                    @include('administracion.boletins.table.index')

            </div>
        </div>
    </main>

@endsection
