@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.pevaluacions.menus.index')
                </div>
                <h5>Asignación de la Carga Académica a los Profesores/Facilitadores por Grado/Sección/Asignatura/Lapso</h5>
            </div>

            <div class="card-body bd-callout bd-callout-{{$grado->color ?? 'default'}} p-2 m-2">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.pevaluacions.form.search.carga',[
                    'route'=>'administracion.pevaluacions.carga',
                    'required_grado'=>'true',
                    'required_seccion'=>'true'
                    ])

                @include('administracion.pevaluacions.table.carga')

            </div>
        </div>
    </main>

@endsection
