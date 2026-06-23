@extends('admin.layouts.dashboard.app')
{{-- @section('page_heading','Listado de Usuarios') --}}
@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10 px-4">

        <div class="card bd-callout bd-callout-warning mt-2">

            <div class="card-header">

                <h2>

                    Actualizar Rol

                        {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">

                        @include('admin.rols.menus.edit')

                    </div>
                    {{-- FIN Menu rapido --}}

                </h2>

            </div>

            <div class="card-body p-1">

                @include('admin.rols.forms.update')

            </div>

        </div>

    </main>

@endsection