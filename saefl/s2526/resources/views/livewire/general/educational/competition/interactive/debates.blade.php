<div class="row mt-2">
    <div class="col-md-8 col-12">
        <div class="card rounded-4 shadow-sm border-0 h-100 overlay">
            <div class="card-body bg-body p-4">

                @if ($debates->count())

                    <h5 class="card-title text-center text-muted mb-4 {{ $status_running_timer ? 'opacity-50' : null }}"
                        id="debate-title">
                        <span class="fw-light">Debate: </span>
                        <span class="fw-bold text-body">{{ $debate->name }}</span>
                    </h5>

                    <div class="card-text text-center pt-2" id="pregunta-text">

                        <div class="text-success fw-bold small text-uppercase mb-2">Pregunta Activa</div>
                        @if ($questions->count())
                            <div class="px-3">
                                <h1 class="display-5 fw-black text-body mb-4" style="line-height: 1.2;">
                                    {{ $question->text ?? null }}
                                </h1>
                            </div>
                        @else
                            <div class="alert alert-info rounded-4">No hay preguntas disponibles</div>
                        @endif

                    </div>

                    <!-- Opciones -->
                    <div class="my-4">
                        @include('livewire.general.educational.competition.interactive.partials.options')
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <div class="d-flex flex-column">
                            <span class="text-muted small text-uppercase fw-bold">Puntuación</span>
                            <span class="fs-4 fw-black text-success">{{ $question->weighting ?? 0 }} pts</span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm"
                                wire:click="nextQuestion()">
                                Siguiente
                                <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                @else
                    <div>
                        No hay debates
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- Aside -->
    <div class="col-md-4 col-12 text-center">
        <div class="card mb-4">
            <div class="card-body">
                <!-- Cronómetro -->
                @include('livewire.general.educational.competition.interactive.partials.timer')

                <hr>
                <!-- Adjudicar Puntaje -->
                @include('livewire.general.educational.competition.interactive.partials.awardScore')

            </div>
        </div>

        <!-- Resultados Preliminares -->
        @include('livewire.general.educational.competition.interactive.partials.results')
    </div>

</div>

@section('livewires')
    @parent
    <script>
        Livewire.on('newQuestion', () => {
            alert('A post was added with the id of: ');
        })
    </script>
@endsection
