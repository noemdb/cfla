<div class="accordion" id="accordionPanelsStayOpenPollMain">

    @forelse ($poll_mains as $poll_main)

        @php
            $poll_token = $poll_main->getTokenUserId($user_id);
            $poll_answers = $poll_main->getPollAnswersUserId($user_id);
            $poll_questions = $poll_main->poll_questions;
        @endphp

        @if ($poll_token)
            <div class="accordion-item text-start">

                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true"
                        aria-controls="panelsStayOpen-collapseOne">
                        {{$loop->iteration ?? null}}. {{$poll_main->name ?? null}}
                    </button>
                </h2>

                <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <div class="d-flex justify-content-between border-bottom mb-2">
                            <div> <span class="fw-bold"> Inicio:</span> <span class="text-muted">{{$poll_main->start->format('d-m-Y H:i:s')}}</span></div>
                            <div> <span class="fw-bold"> Fin:</span> <span class="text-muted"> {{$poll_main->end->format('d-m-Y H:i:s')}}</span></div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <div><b>Preguntas:</b> {{ $poll_questions->count() ?? null }}</div>
                                <div><b>Respuestas:</b> {{ $poll_answers->count() ?? null }}</div>
                            </div>
                            <ul class="list-group list-group-flush">
                                @forelse ($poll_questions as $poll_question)
                                <li class="list-group-item">
                                    {{$loop->iteration ?? null}}. {{$poll_question->text ?? null}}
                                    <div class="ms-2">
                                        <b>Opciones:</b>
                                        <ul class="list-group list-group-flush">
                                            @forelse ($poll_question->poll_options as $poll_option)
                                            @php $status_vote = $poll_option->statusSelectedVote($poll_token->token) @endphp
                                            <li class="list-group-item py-1">
                                                <div class="d-flex justify-content-between">
                                                    <div>{{$loop->iteration ?? null}}. {{$poll_option->text ?? null}}</div>
                                                    <div class="fw-bold">{{ ($status_vote) ? 'X' : null}}</div>
                                                </div>
                                            </li>
                                            @empty
                                            <li class="list-group-item disabled">No hay opciones.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </li>
                                @empty
                                    <li class="list-group-item disabled">No hay preguntas.</li>
                                @endforelse
                            </ul>
                        </div>

                        <div class="text-center">
                            @if ($poll_main->StatusActiveAttendee($poll_token->token))
                                <a class="btn btn-primary w-50" href="#" wire:click="vote({{$poll_main->id}})" role="button">Participar</a>
                            @else
                                <div>Su participación ya fue registrada completamente, no tiene preguntas sin responder.</div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>

        @else

            <div>No hay ticket de participación</div>

        @endif

    @empty

        <div>No hay Procesos de Consultas activas para usted.</div>

    @endforelse

</div>
