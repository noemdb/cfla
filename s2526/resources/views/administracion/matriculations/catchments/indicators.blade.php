@extends('administracion.layouts.dashboard.app')

@section('title')Proceso de Matriculación Escolar - Indicadores - {{ Auth::user()->rol ?? '' }} @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.matriculations.catchments.menus.index')
                </div>
                {{-- FIN Menu rapido --}}
                <h4><u title="Listado especial con botones de acción">Indicadores</u> para las <span class="font-weight-bolder">manifestaciones de interés</span> registradas</h4>
            </div>

            <div class="card-body">

                @include('administracion.matriculations.catchments.indicators.index')

            </div>
        </div>
    </main>
    

@endsection

@section('sweetalert') @parent <script src="{{ asset('js/listeners/sweetalert/default.js') }}"></script> @endsection


@section('stylesheets')
    @parent
    <style>
        /* Estilos generales para gráficos y dashboards */
        .stat-chart {
            border-radius: 12px;
            overflow: hidden;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
        }
        
        .stat-chart .chart-title {
            font-weight: 600;
            font-size: 0.95rem;
            color: #333;
        }
        
        .stat-chart .total-badge {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 700;
            padding: 0.4rem 0.8rem;
            border-radius: 10px;
            font-size: 0.85rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }
        
        /* Estilos para elementos de gráficos de barras */
        .chart-item {
            margin-bottom: 0.65rem;
        }
        
        .chart-item:last-child {
            margin-bottom: 0;
        }
        
        .bar-container {
            position: relative;
        }
        
        .bar-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.2rem;
            font-size: 0.8rem;
        }
        
        .bar-label span {
            color: #495057;
            font-weight: 500;
        }
        
        .bar-label strong {
            color: #212529;
        }
        
        .progress-container {
            height: 6px;
            background-color: #f1f3f5;
            border-radius: 10px;
            position: relative;
            overflow: visible;
        }
        
        .progress-bar {
            height: 100%;
            border-radius: 10px;
            transition: width 0.6s ease-in-out;
        }
        
        .value-label {
            position: absolute;
            top: -22px;
            transform: translateX(-50%);
            font-size: 0.75rem;
            font-weight: 600;
            color: #495057;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .bar-container:hover .value-label {
            opacity: 1;
        }
        
        /* Estilos para pies de gráficos */
        .chart-footer {
            background-color: #fcfcfc;
            border-top: 1px solid rgba(0,0,0,0.03);
        }
        
        .chart-toggle {
            background-color: #f8f9fa;
            border: none;
            color: #4361ee;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.3rem 0.8rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .chart-toggle:hover {
            background-color: #eef1ff;
        }
        
        /* Contenedores y layouts para gráficos */
        .chart-content {
            padding: 1rem;
        }
        
        .chart-header {
            padding: 1rem 1rem 0.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.03);
        }
        
        /* Paleta de colores para gráficos */
        .chart-color-1 { background-color: #4361ee; }
        .chart-color-2 { background-color: #3a0ca3; }
        .chart-color-3 { background-color: #7209b7; }
        .chart-color-4 { background-color: #f72585; }
        .chart-color-5 { background-color: #4cc9f0; }
        .chart-color-6 { background-color: #4895ef; }
        .chart-color-7 { background-color: #560bad; }
        .chart-color-8 { background-color: #f7b801; }
        .chart-color-9 { background-color: #ff9e00; }
        .chart-color-10 { background-color: #38b000; }

 .chart-color-1 { background-color: #4361ee; }
        .chart-color-2 { background-color: #3a0ca3; }
        .chart-color-3 { background-color: #7209b7; }
        .chart-color-4 { background-color: #f72585; }
        .chart-color-5 { background-color: #4cc9f0; }
        .chart-color-6 { background-color: #4895ef; }
        .chart-color-7 { background-color: #560bad; }
        .chart-color-8 { background-color: #f7b801; }
        .chart-color-9 { background-color: #ff9e00; }
        .chart-color-10 { background-color: #38b000; }

 .chart-color-11 { background-color: #4361ee; }
        .chart-color-12 { background-color: #3a0ca3; }
        .chart-color-13 { background-color: #7209b7; }
        .chart-color-14 { background-color: #f72585; }
        .chart-color-15 { background-color: #4cc9f0; }
        .chart-color-16 { background-color: #4895ef; }
        .chart-color-17 { background-color: #560bad; }
        .chart-color-18 { background-color: #f7b801; }
        .chart-color-19 { background-color: #ff9e00; }
        .chart-color-20 { background-color: #38b000; }

 .chart-color-21 { background-color: #4361ee; }
        .chart-color-22 { background-color: #3a0ca3; }
        .chart-color-23 { background-color: #7209b7; }
        .chart-color-24 { background-color: #f72585; }
        .chart-color-25 { background-color: #4cc9f0; }
        .chart-color-26 { background-color: #4895ef; }
        .chart-color-27 { background-color: #560bad; }
        .chart-color-28 { background-color: #f7b801; }
        .chart-color-29 { background-color: #ff9e00; }
        .chart-color-30 { background-color: #38b000; }

 .chart-color-31 { background-color: #4361ee; }
        .chart-color-32 { background-color: #3a0ca3; }
        .chart-color-33 { background-color: #7209b7; }
        .chart-color-34 { background-color: #f72585; }
        .chart-color-35 { background-color: #4cc9f0; }
        .chart-color-36 { background-color: #4895ef; }
        .chart-color-37 { background-color: #560bad; }
        .chart-color-38 { background-color: #f7b801; }
        .chart-color-39 { background-color: #ff9e00; }
        .chart-color-40 { background-color: #38b000; }

 .chart-color-41 { background-color: #4361ee; }
        .chart-color-42 { background-color: #3a0ca3; }
        .chart-color-43 { background-color: #7209b7; }
        .chart-color-44 { background-color: #f72585; }
        .chart-color-45 { background-color: #4cc9f0; }
        .chart-color-46 { background-color: #4895ef; }
        .chart-color-47 { background-color: #560bad; }
        .chart-color-48 { background-color: #f7b801; }
        .chart-color-49 { background-color: #ff9e00; }
        .chart-color-50 { background-color: #38b000; }


 .chart-color-51 { background-color: #4361ee; }
        .chart-color-52 { background-color: #3a0ca3; }
        .chart-color-53 { background-color: #7209b7; }
        .chart-color-54 { background-color: #f72585; }
        .chart-color-55 { background-color: #4cc9f0; }
        .chart-color-56 { background-color: #4895ef; }
        .chart-color-57 { background-color: #560bad; }
        .chart-color-58 { background-color: #f7b801; }
        .chart-color-59 { background-color: #ff9e00; }
        .chart-color-60 { background-color: #38b000; }

 .chart-color-61 { background-color: #4361ee; }
        .chart-color-62 { background-color: #3a0ca3; }
        .chart-color-63 { background-color: #7209b7; }
        .chart-color-64 { background-color: #f72585; }
        .chart-color-65 { background-color: #4cc9f0; }
        .chart-color-66 { background-color: #4895ef; }
        .chart-color-67 { background-color: #560bad; }
        .chart-color-68 { background-color: #f7b801; }
        .chart-color-69 { background-color: #ff9e00; }
        .chart-color-70 { background-color: #38b000; }
    </style>
@endsection