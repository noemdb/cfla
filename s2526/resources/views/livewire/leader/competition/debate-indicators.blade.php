<div>

    <div class="card">
        <div class="card-header bg-light text-dark">
            <h5 class="card-title mb-0">
                <i class="fas fa-chart-bar"></i> Indicadores de la Competición <strong>{{ $competition->name }}</strong>
            </h5>
        </div>
        <div class="card-body" id="indicators-debate">

            @if($showDetails)
                <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999; background-color: rgba(0,0,0,0.5); overflow-y: auto;">
                    <div class="d-flex align-items-start justify-content-center min-vh-100 p-4">
                        @include('livewire.leader.competition.modal.details')
                    </div>
                </div>
            @endif

            @if($showStats)
                @include('livewire.leader.competition.modal.stats')
            @endif

            <div class="row">
                @foreach($peducativos as $peducativo)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0 font-weight-bold">{{ $peducativo->name }}</h6>
                            </div>
                            <div class="card-body">
                                <!-- Grades Table -->
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                        <tr class="bg-light">
                                            <th class="border-top-0 font-weight-bold text-muted">Grados/Años</th>
                                            <th class="border-top-0 text-center text-success">Correctas</th>
                                            <th class="border-top-0 text-center text-danger">Erradas</th>
                                            <th class="border-top-0 text-center text-dark">Puntaje</th>
                                            <th class="border-top-0 text-center text-info">Detalles</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($peducativo->authorized_grados ?? [] as $grado)
                                            @php
                                            $totalScore = $competition->getTotalScoreForGrado($grado->id);
                                            $accuracy = $competition->getAccuracyForGrado($grado->id);
                                            $wrong = $competition->getWrongAnswerForGrado($grado->id);
                                            @endphp
                                            <tr>
                                            <td class="align-middle">{{ $grado->name }}</td>
                                            <td class="text-center">
                                                <div>
                                                    {{ $accuracy->accuracy }}%
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="text-danger font-weight-bold">
                                                {{ $wrong->wrongPercentage }}%
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="text-dark font-weight-bold">
                                                {{ $totalScore }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle" id="actions-grado-{{ $grado->id }}">
                                                <div class="btn-group" role="group" aria-label="Button group">
                                                    <button wire:click="showGradoDetails({{ $grado->id }})"
                                                        class="btn btn-sm btn-info" title="Ver detalles de {{ $grado->name }}">
                                                        <i class="fas fa-info"></i>
                                                    </button>
                                                    <button wire:click="showGradoStats({{ $grado->id }})"
                                                            class="btn btn-sm btn-warning" title="Ver métricas estadísticas de {{ $grado->name }}">
                                                        <i class="fas fa-chart-pie"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>


</div>
