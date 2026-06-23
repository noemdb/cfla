@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.peducativos.menus.edit')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>Actualizar <span class="font-weight-bolder">Programa Educativo</span></h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                <div class="card bd-callout bd-callout-warning">

                    <h5 class="card-header">Datos</h5>

                    <div class="card-body">

                        <div class="container">
                            
                            <div class="row">

                                <div class="col-8">
                                    @include('administracion.configuraciones.grados.partials.edit')
                                </div>

                                <div class="col-4">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Programas Educativos, Editar'; </script> @endsection





