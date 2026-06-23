@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card mt-2">
            <div class="card-header alert-secondary">

                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.registro_titulos.menus.edit')
                </div>

                <h4 class="pb-0 mb-0">
                    <i class="fas fa-graduation-cap fa-1x"></i>
                    Actualizar <span class=" font-weight-bolder">Promoción</span>
                </h4>

            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                <div class="row">

                    <div class="col-9 px-1">
                        @includeif('administracion.registro_titulos.form.edit')
                    </div>

                    <div class="col-3 px-1">
                    </div>

                </div>

            </div>

        </div>
    </main>

@endsection
