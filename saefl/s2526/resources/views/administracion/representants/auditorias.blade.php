@extends('administracion.layouts.dashboard.app')

@section('title')
    @parent
    - Auditorías de Saldos de Representantes {{ Carbon\Carbon::now()->format('Y-m-d h:m') }}
@endsection

@section('main')
    
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-info">

                <div class="btn-group float-right">
                    @include('administracion.representants.menus.index')
                </div>

                <h4>
                    <span title="Reporte de Auditoría"><u>Auditorías</u></span> Estado de Cuenta por Representantes [USD]
                </h4>
                <small class="text-muted">Fecha: {{now()}} || No incluye Deudas de período anterior (Deuda Individual)</small>
            </div>

            <div class="card-body p-1 m-1">

                @include('administracion.representants.form.auditorias', [
                    'route' => 'administracion.representants.auditorias',
                ])

                @include('administracion.representants.table.auditorias')

            </div>

        </div>

    </main>
@endsection
