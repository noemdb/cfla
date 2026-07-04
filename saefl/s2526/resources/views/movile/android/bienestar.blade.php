@extends('movile.android.layouts.app')
@section('content')
    <div class="content w-100 py-2 px-1">

        <div class=" d-flex d-flex justify-content-center px-2 py-2">
            <div>
                <i class="{{ $icon_menus['bienestar'] ?? 'bi bi-folder2-open' }} fa-3x img-thumbnail p-2"></i>
                <div class="small text-muted">Bienestar</div>
            </div>
        </div>

        @auth

            @if (Auth::user()->IsBienestar())
                <div>
                    <nav>
                        <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">
                            <button class="nav-link p-1 active" id="nav-details-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-details" type="button" role="tab" aria-controls="nav-details"
                                aria-selected="true">
                                <i class="bi bi-journal-text"></i>
                            </button>
                        </div>
                    </nav>
                    <div class="tab-content border border-top-0 bg-white" id="nav-tabContent">

                        <div class="tab-pane fade p-2 show active" id="nav-details" role="tabpanel"
                            aria-labelledby="nav-details-tab" tabindex="0">
                            <div class="p-1">
                                <livewire:movile.director.catchment.index-component />
                            </div>
                        </div>

                    </div>
                </div>
            @else
                <div class="alert alert-warning mx-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    No tienes permisos para acceder a este módulo.
                </div>
            @endif
        @else
            <div class="alert alert-info mx-2">
                <i class="fas fa-sign-in-alt"></i>
                Debes iniciar sesión para acceder a este módulo.
            </div>
        @endauth

    </div>
@endsection

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

        .chart-footer {
            background-color: #fcfcfc;
            border-top: 1px solid rgba(0, 0, 0, 0.03);
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

        .chart-content {
            padding: 1rem;
        }

        .chart-header {
            padding: 1rem 1rem 0.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        }

        /* Paleta de colores para gráficos (ciclo de 10 colores repetido) */
        @php $colors =['#4361ee', '#3a0ca3', '#7209b7', '#f72585', '#4cc9f0', '#4895ef', '#560bad', '#f7b801', '#ff9e00', '#38b000'];
        @endphp
        @for ($i = 1; $i <= 70; $i++)
            .chart-color-{{ $i }} {
                background-color: {{ $colors[($i - 1) % 10] }};
            }
        @endfor
    </style>
@endsection
