@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-secondary">

                <h4>Resumen final del rendimiento estudiantíl</h4>

            </div>

            <div class="card-body p-2 m-2">

                    @include('administracion.boletin_revisions.form.search.resumen_final')

                    @include('administracion.boletin_revisions.table.resumen_final')

            </div>

        </div>

    </main>

@endsection
