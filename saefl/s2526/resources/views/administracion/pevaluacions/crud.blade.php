@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <h4>Planes de Evaluación registrados</h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.pevaluacions.form.search.crud',['route'=>'administracion.pevaluacions.crud'])
                @include('administracion.pevaluacions.table.crud')
            </div>
        </div>
    </main>

@endsection
