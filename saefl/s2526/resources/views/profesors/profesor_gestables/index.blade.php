@extends('profesors.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <h4>
                    <i class="{{$icon_menus['grupo_estables'] ?? ''}} text-primary" aria-hidden="true"></i>
                    Cargar de Notas - Planes de Evaluación - Grupos Estables

                </h4>
            </div>

            <div class="card-body">

                @include('profesors.elements.forms.errors')
                @include('profesors.elements.messeges.oper_ok')

                {{-- @include('profesors.profesor_gestables.form.search.index',['route'=>'profesors.profesor_gestables.index']) --}}
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            @include('profesors.profesor_gestables.table.index')
                        </div>
                    </div>

                    @if ($modeSetUp)
                        <hr>
                        <div class="row">
                            <div class="col-sm-12">
                                @include('profesors.profesor_gestables.boletins.main')
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

@endsection
