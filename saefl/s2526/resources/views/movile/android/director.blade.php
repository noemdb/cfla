@extends('movile.android.layouts.app')
@section('content')
    <div class="content w-100 py-2 px-1">

        <div class=" d-flex d-flex justify-content-center px-2 py-2">
            <div>
                <i class="{{ $icon_menus['director'] ?? '' }} fa-3x img-thumbnail p-2"></i>
                <div class="small text-muted">Director</div>
            </div>
        </div>

        @auth

            @if (Auth::user()->IsDirector())
                <div>
                    <nav>
                        <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">
                            <button class="nav-link p-1 active" id="nav-details-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-details" type="button" role="tab" aria-controls="nav-details"
                                aria-selected="true">
                                <i class="bi bi-journal-text"></i>
                            </button>

                            <button class="nav-link p-1 " id="nav-prosecucion-tab" data-bs-toggle="tab" data-bs-target="#nav-prosecucion"
                                type="button" role="tab" aria-controls="nav-prosecucion" aria-selected="false">
                                <i class="fas fa-id-card-alt"></i>
                            </button>

                            <button class="nav-link p-1 " id="nav-papers-tab" data-bs-toggle="tab" data-bs-target="#nav-papers"
                                type="button" role="tab" aria-controls="nav-papers" aria-selected="false">
                                <i class="bi bi-list-check"></i>
                            </button>

                            <button class="nav-link p-1 " id="nav-competition-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-competition" type="button" role="tab" aria-controls="nav-competition"
                                aria-selected="false">
                                <i class="{{ $icon_menus['competitions'] ?? '' }}"></i>
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

                        <div class="tab-pane fade p-2" id="nav-prosecucion" role="tabpanel"
                            aria-labelledby="nav-prosecucion-tab" tabindex="0">
                            <div class="p-1">
                                {{-- inserta component livewire --}}
                                <livewire:movile.director.prosecucion.index-component />
                            </div>
                        </div>

                        <div class="tab-pane fade p-2" id="nav-papers" role="tabpanel" aria-labelledby="nav-papers-tab"
                            tabindex="0">
                            <div class="p-1 h-100">
                                <strong>Módulo de Planificación</strong>
                            </div>
                        </div>

                        <div class="tab-pane fade p-2" id="nav-competition" role="tabpanel"
                            aria-labelledby="nav-competition-tab" tabindex="0">
                            <div class="p-1 h-100">
                                <div class="content py-2 px-1">

                                    <div class="p-1 h-100">
                                        <strong>Competiciones</strong>
                                    </div>

                                    <nav>

                                        <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">

                                            <button class="nav-link p-1 active" id="nav-details-competition-tab"
                                                data-bs-toggle="tab" data-bs-target="#nav-details-competition" type="button"
                                                role="tab" aria-controls="nav-details-competition" aria-selected="true">

                                                Debates

                                            </button>

                                            <button class="nav-link p-1 " id="nav-papers-competition-tab" data-bs-toggle="tab"
                                                data-bs-target="#nav-papers-competition" type="button" role="tab"
                                                aria-controls="nav-papers-competition" aria-selected="false">

                                                Resultados

                                            </button>

                                        </div>

                                    </nav>

                                    <div class="tab-content border border-top-0 bg-white" id="nav-tabContent">

                                        <div class="tab-pane fade p-2 show active" id="nav-details-competition" role="tabpanel"
                                            aria-labelledby="nav-details-competition-tab" tabindex="0">
                                            <div class="p-1">

                                                <livewire:movile.competition.index-component />

                                            </div>
                                        </div>

                                        <div class="tab-pane fade p-2" id="nav-papers-competition" role="tabpanel"
                                            aria-labelledby="nav-papers-competition-tab" tabindex="0">

                                            <div class="p-1 h-100">

                                                <livewire:movile.competition.result-component />

                                            </div>

                                        </div>

                                    </div>


                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            @else
                <div class="content pt-4 mt-4">
                    <div>Contenido no disponible...</div>
                </div>
            @endif
        @else
            <a name="" id="" class="btn btn-dark btn-sm" href="{{ route('movile.android.welcome') }}"
                role="button">
                <div> Inicia tu sesión </div>
            </a>

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

        /* Contenedores y layouts para gráficos */
        .chart-content {
            padding: 1rem;
        }

        .chart-header {
            padding: 1rem 1rem 0.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        }

        /* Paleta de colores para gráficos */
        .chart-color-1 {
            background-color: #4361ee;
        }

        .chart-color-2 {
            background-color: #3a0ca3;
        }

        .chart-color-3 {
            background-color: #7209b7;
        }

        .chart-color-4 {
            background-color: #f72585;
        }

        .chart-color-5 {
            background-color: #4cc9f0;
        }

        .chart-color-6 {
            background-color: #4895ef;
        }

        .chart-color-7 {
            background-color: #560bad;
        }

        .chart-color-8 {
            background-color: #f7b801;
        }

        .chart-color-9 {
            background-color: #ff9e00;
        }

        .chart-color-10 {
            background-color: #38b000;
        }

        .chart-color-1 {
            background-color: #4361ee;
        }

        .chart-color-2 {
            background-color: #3a0ca3;
        }

        .chart-color-3 {
            background-color: #7209b7;
        }

        .chart-color-4 {
            background-color: #f72585;
        }

        .chart-color-5 {
            background-color: #4cc9f0;
        }

        .chart-color-6 {
            background-color: #4895ef;
        }

        .chart-color-7 {
            background-color: #560bad;
        }

        .chart-color-8 {
            background-color: #f7b801;
        }

        .chart-color-9 {
            background-color: #ff9e00;
        }

        .chart-color-10 {
            background-color: #38b000;
        }

        .chart-color-11 {
            background-color: #4361ee;
        }

        .chart-color-12 {
            background-color: #3a0ca3;
        }

        .chart-color-13 {
            background-color: #7209b7;
        }

        .chart-color-14 {
            background-color: #f72585;
        }

        .chart-color-15 {
            background-color: #4cc9f0;
        }

        .chart-color-16 {
            background-color: #4895ef;
        }

        .chart-color-17 {
            background-color: #560bad;
        }

        .chart-color-18 {
            background-color: #f7b801;
        }

        .chart-color-19 {
            background-color: #ff9e00;
        }

        .chart-color-20 {
            background-color: #38b000;
        }

        .chart-color-21 {
            background-color: #4361ee;
        }

        .chart-color-22 {
            background-color: #3a0ca3;
        }

        .chart-color-23 {
            background-color: #7209b7;
        }

        .chart-color-24 {
            background-color: #f72585;
        }

        .chart-color-25 {
            background-color: #4cc9f0;
        }

        .chart-color-26 {
            background-color: #4895ef;
        }

        .chart-color-27 {
            background-color: #560bad;
        }

        .chart-color-28 {
            background-color: #f7b801;
        }

        .chart-color-29 {
            background-color: #ff9e00;
        }

        .chart-color-30 {
            background-color: #38b000;
        }

        .chart-color-31 {
            background-color: #4361ee;
        }

        .chart-color-32 {
            background-color: #3a0ca3;
        }

        .chart-color-33 {
            background-color: #7209b7;
        }

        .chart-color-34 {
            background-color: #f72585;
        }

        .chart-color-35 {
            background-color: #4cc9f0;
        }

        .chart-color-36 {
            background-color: #4895ef;
        }

        .chart-color-37 {
            background-color: #560bad;
        }

        .chart-color-38 {
            background-color: #f7b801;
        }

        .chart-color-39 {
            background-color: #ff9e00;
        }

        .chart-color-40 {
            background-color: #38b000;
        }

        .chart-color-41 {
            background-color: #4361ee;
        }

        .chart-color-42 {
            background-color: #3a0ca3;
        }

        .chart-color-43 {
            background-color: #7209b7;
        }

        .chart-color-44 {
            background-color: #f72585;
        }

        .chart-color-45 {
            background-color: #4cc9f0;
        }

        .chart-color-46 {
            background-color: #4895ef;
        }

        .chart-color-47 {
            background-color: #560bad;
        }

        .chart-color-48 {
            background-color: #f7b801;
        }

        .chart-color-49 {
            background-color: #ff9e00;
        }

        .chart-color-50 {
            background-color: #38b000;
        }


        .chart-color-51 {
            background-color: #4361ee;
        }

        .chart-color-52 {
            background-color: #3a0ca3;
        }

        .chart-color-53 {
            background-color: #7209b7;
        }

        .chart-color-54 {
            background-color: #f72585;
        }

        .chart-color-55 {
            background-color: #4cc9f0;
        }

        .chart-color-56 {
            background-color: #4895ef;
        }

        .chart-color-57 {
            background-color: #560bad;
        }

        .chart-color-58 {
            background-color: #f7b801;
        }

        .chart-color-59 {
            background-color: #ff9e00;
        }

        .chart-color-60 {
            background-color: #38b000;
        }

        .chart-color-61 {
            background-color: #4361ee;
        }

        .chart-color-62 {
            background-color: #3a0ca3;
        }

        .chart-color-63 {
            background-color: #7209b7;
        }

        .chart-color-64 {
            background-color: #f72585;
        }

        .chart-color-65 {
            background-color: #4cc9f0;
        }

        .chart-color-66 {
            background-color: #4895ef;
        }

        .chart-color-67 {
            background-color: #560bad;
        }

        .chart-color-68 {
            background-color: #f7b801;
        }

        .chart-color-69 {
            background-color: #ff9e00;
        }

        .chart-color-70 {
            background-color: #38b000;
        }
    </style>
@endsection
