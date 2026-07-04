<div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999; background-color: rgba(0,0,0,0.5); overflow-y: auto;">
    <div class="d-flex align-items-start justify-content-center min-vh-100 p-4">
        <div class="card" style="min-width: 400px;">
            <div class="card-header bg-info text-dark">
                <h5 class="mb-0">Métricas estadísticas de
                    {{ $peducativos->flatMap->grados->firstWhere('id', $statsGradoId)->name ?? 'Grado' }}
                </h5>
            </div>
            <div class="card-body">
                @php
                    $totalRealizadas = $competition->getAnsweredQuestionsCountByGradoId($statsGradoId);
                    $totalCorrectas = $competition->getCorrectAnsweredQuestionsByGrado($statsGradoId)->count();
                    $totalIncorrectas = $competition->getWrongAnsweredQuestionsByGrado($statsGradoId)->count();
                    $totalPuntajeMax = $competition->getTotalWeightingByGradoId($statsGradoId);
                    $accuracy = $competition->getAccuracyForGrado($statsGradoId)->accuracy;
                    $error = $competition->getWrongAnswerForGrado($statsGradoId)->wrongPercentage;
                @endphp
                <div class="text-center font-weight-bold">General</div>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fa fa-list-ol text-primary"></i>
                            Total preguntas realizadas
                        </span>
                        <span class="badge badge-primary rounded badge-lg">{{ $totalRealizadas }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fa fa-check text-success"></i>
                            Total preguntas correctas
                        </span>
                        <span class="badge badge-success rounded badge-lg">{{ $totalCorrectas }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fa fa-times text-danger"></i>
                            Total preguntas incorrectas
                        </span>
                        <span class="badge badge-danger rounded badge-lg">{{ $totalIncorrectas }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fa fa-trophy text-warning"></i>
                            Total puntaje máximo
                        </span>
                        <span class="badge badge-dark rounded badge-lg">{{ $totalPuntajeMax }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fa fa-bullseye text-info"></i>
                            Precisión
                        </span>
                        <span class="badge badge-info rounded badge-lg">{{ $accuracy }} %</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fa fa-exclamation-triangle text-warning"></i>
                            Error
                        </span>
                        <span class="badge badge-warning rounded badge-lg">{{ $error }} %</span>
                    </li>
                </ul>
                <hr>
                @php $seccions = $statsGrado->getSeccionsActive(); @endphp
                <div class="text-center font-weight-bold">Por secciones</div>
                <div class="d-flex justify-content-center">
                    @foreach($seccions as $seccion)
                        <ul class="list-group px-2" id="seccion-{{ $seccion->id }}">
                            <li class="list-group-item">
                                <strong>SECCIÓN {{ $seccion->name }}</strong>
                                <ul class="list-group mt-2">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="px-2">
                                            <i class="fa fa-list-ol text-primary"></i>
                                            Total preguntas realizadas
                                        </span>
                                        <span class="badge badge-primary rounded badge-lg">{{ $competition->getAnsweredQuestionsCountBySeccionId($seccion->id) }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="px-2">
                                            <i class="fa fa-trophy text-warning"></i>
                                            Total puntaje máximo
                                        </span>
                                        <span class="badge badge-dark rounded badge-lg">{{ $competition->getTotalWeightingBySeccionId($seccion->id) }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="px-2">
                                            <i class="fa fa-info-circle text-info"></i>
                                            Puntaje obtenido
                                        </span>
                                        <span class="badge badge-info rounded badge-lg">{{ $competition->getTotalScoreForSection($seccion->id) }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="px-2">
                                            <i class="fa fa-bullseye text-success"></i>
                                            Precisión considerando la puntuación alcanzada
                                        </span>
                                        <span class="badge badge-success rounded badge-lg">{{ $competition->getAccuracyForSeccion($seccion->id) }} %</span>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    @endforeach
                </div>
                <div class="mt-3 text-right">
                    <button class="btn btn-secondary" wire:click="closeStats">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
