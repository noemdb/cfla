@extends('evaluacions.layouts.dashboard.app')

@section('main')
    <main role="main">

        <div class="card card-primary mt-2 bd-callout bd-callout-gray">

            <div class="card-header alert-secondary">
                <h3 class="mb-0 pb-0 border-bottom">
                    <div class="d-flex justify-content-between">
                        <div>
                            <i class="fas fa-chart-line text-info pr-1" aria-hidden="true"></i>
                            <span class="font-weight-bold">Coordinación - Diagnóstico Educativo</span>
                            <small class="text-muted ml-2">Panel gestión, seguimiento y análisis</small>
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
    <script src="{{ asset('vendor/jsdelivr/4.5.0/chart.js') }}"></script>
@endsection

@section('sweetalert')
    @parent
    <script>
        window.addEventListener('swal', function(e) {
            Swal.fire(e.detail);
        });

        window.addEventListener('swal:confirm', function(e) {
            Swal.fire({
                title: e.detail.message,
                text: e.detail.text,
                icon: e.detail.type,
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then((result) => {
                if (result.value) {
                    window.livewire.emit('remove', e.detail.id);
                }
            });
        });

        window.addEventListener('swal:question', function(e) {
            Swal.fire({
                title: e.detail.message,
                text: e.detail.text,
                icon: e.detail.type,
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.value) {
                    window.livewire.emit(e.detail.method, e.detail.id);
                }
            });
        });
    </script>
@endsection

@section('chartjs')
    @parent
    <script src="{{ asset('js/Chart.bundle.js') }}"></script>
    <script src="{{ asset('js/ChartFunction.js') }}"></script>
    <script src="{{ asset('js/ChartEvent.js') }}"></script>
@endsection
