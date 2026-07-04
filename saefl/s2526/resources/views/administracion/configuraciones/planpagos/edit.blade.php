@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary pb-0 mb-0 ">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.planpagos.menus.edit')
                </div>
                {{-- FIN Menu rapido --}}

                <h3>Actualizar <span class="font-weight-bolder">Plan de Pago</span> <span class="font-size-sm text-secondary">[{{ $planpago->name ?? '' }}]</span></h3>

            </div>

            <div class="card-body">

                    @include('administracion.elements.forms.errors')

                    @include('administracion.elements.messeges.oper_ok')

                    {!! Form::model($planpago,['route' => ['administracion.configuraciones.planpagos.update', $planpago->id], 'method' => 'PUT', 'id'=>'form-update-grupo_estable_'.$planpago->id, 'role'=>'form']) !!}

                        <div class="card bd-callout bd-callout-warning">

                            <h5 class="card-header pb-1 mb-1">
                                <i class="{{ $icon_menus['editar'] }} fa-1x text-warning float-right"></i>
                                Datos
                            </h5>

                            <div class="card-body p-2">

                                <div class="row">
                                    <div class="col-8">
                                        @include('administracion.configuraciones.planpagos.form.nav_tabs')
                                    </div>
                                    <div class="col-4">
                                         @include('administracion.configuraciones.planpagos.partials.resume.edit')
                                    </div>
                                </div>

                            </div>
                        </div>

                    {!! Form::close() !!}

            </div>
        </div>
    </main>

@endsection

@section('scripts')
    @parent <script type="text/javascript"> document.title = 'SAEFL - Planes de Pago, actualizar'; </script>
@endsection
