@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <h4>Asignar Planes de Evaluación a Grupos Estables</h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.profesor_gestables.form.search.index',['route'=>'administracion.profesor_gestables.index'])
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-{{ ($modeSetUp) ? '7':'12' }}">
                            @include('administracion.profesor_gestables.table.index')
                        </div>
                        @if ($modeSetUp)
                            <div class="col-sm-5">
                                @include('administracion.profesor_gestables.setup.main')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
        </div>

    </main>

@endsection
