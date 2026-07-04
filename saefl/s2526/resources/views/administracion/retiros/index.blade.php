@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-info">
                <h4>
                    Estudiantes retirados
                </h4>
            </div>

            <div class="card-body">

                @include('admin.elements.messeges.oper_ok')

                @include('administracion.retiros.table.index')

            </div>
        </div>
    </main>
@endsection

@section('scripts')
    @parent
@endsection
