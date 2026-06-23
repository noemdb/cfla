@extends('plannings.layouts.dashboard.app')

@section('main')
    <main role="main">

        <div class="card card-primary mt-2 bd-callout bd-callout-gray">

            <div class="card-header alert-secondary">
                <h3 class="mb-0 pb-0 border-bottom">
                    <div class="d-flex justify-content-between">
                        <div>
                            <i class="fas fa-chart-line text-info pr-1" aria-hidden="true"></i>
                            <span class="font-weight-bold">Jefatura - Diagnóstico Educativo</span>
                            <small class="text-muted ml-2">Panel de seguimiento y análisis</small>
                        </div>
                    </div>
                </h3>
            </div>

            <div class="card-body p-1 m-1">

                <livewire:evaluacion.diagnostic.index-component />

            </div>

        </div>

    </main>
@endsection


@section('stylesheet')
    @parent
    <style>
        .icon-circle {
            height: 2rem;
            width: 2rem;
            border-radius: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .border-left-danger {
            border-left: 0.25rem solid #dc3545 !important;
        }


        .progress-sm {
            height: 0.5rem;
        }

        .chart-area {
            position: relative;
            height: 20rem;
            width: 100%;
        }

        .chart-bar {
            position: relative;
            height: 20rem;
            width: 100%;
        }

        .chart-pie {
            position: relative;
            height: 15rem;
            width: 100%;
        }
    </style>
@endsection


@section('scripts')
    @parent
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
