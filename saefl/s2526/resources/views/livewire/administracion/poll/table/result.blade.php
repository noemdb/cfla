@php
    $key = 'key-result'.$poll_main->id;
    $poll_answer_by_tokens = $poll_main->poll_answer_by_tokens;
    $count_poll_main_answer = $poll_answer_by_tokens->count();

    $count_tokens = ($poll_main->poll_tokens->isNotEmpty()) ? $poll_main->poll_tokens->count() : null;
    $participations = $poll_main->participations;
@endphp

<div class="card" wire:key="{{$key}}-card-poll-results">
    <div class="card-header alert-info">
        <button type="button" class="close" wire:click='showModeIndex()'>
            <span aria-hidden="true">×</span>
        </button>
        <strong>Resultados para la consulta: </strong> {{ $poll_main->name ?? null }}
        <div class=" font-weight-bold text-right border-top-1 pt-2">Total de participantes: <span class=" border rounded p-1 table-light"> {{ $count_poll_main_answer ?? null}}</span> </div>

        <div class="progress mt-2" style="height: 1rem;">
            <div class="progress-bar font-weight-bold" role="progressbar" style="width: {{$participations ?? null}}%" aria-valuenow="{{$participations ?? null}}" aria-valuemin="0" aria-valuemax="100">{{$participations ?? null}}%</div>
        </div>
    </div>
    <div class="card-body text-secondary">
        <nav>
            <div class="nav nav-tabs font-weight-bold" id="nav-tab" role="tablist">
                @foreach($poll_questions as $poll_question)
                    <a class="nav-item nav-link {{($loop->iteration==1) ? 'active':''}}" id="nav-header-tab-poll_main_question-{{$poll_question->id}}" data-toggle="tab" href="#nav-content-poll_main_question-{{$poll_question->id}}" role="tab" aria-controls="nav-home" aria-selected="true"><b>Pregunta {{$loop->iteration?? ''}}</b></a>
                @endforeach
            </div>
        </nav>


        <div class="tab-content border border-top-0" id="nav-tabContent">

            @foreach($poll_questions as $poll_question)

                <div class="tab-pane fade show {{($loop->iteration==1) ? 'active':''}}" id="nav-content-poll_main_question-{{$poll_question->id}}" role="tabpanel" aria-labelledby="nav-header-home-tab-{{$poll_question->id}}">
                    <div class="p-2">
                        <div class="font-weight-bold pb-2 px-2"> {{$poll_question->text ?? ''}} </div>
                        {{-- <hr class="p-0 m-0 pb-2"> --}}
                        <ul class="list-group px-2">
                        @php
                            $poll_options = $poll_question->poll_options;
                            $poll_tokens = $poll_question->poll_tokens;
                            $count_tokens = $poll_tokens->count();
                        @endphp
                        @foreach($poll_options as $poll_option)
                            @php
                                $poll_answers = $poll_option->poll_answers;
                                $count_answers = $poll_answers->count();

                                $participations = ($count_poll_main_answer) ? 100 * $count_answers / $count_poll_main_answer : null;
                                $participations = round($participations,2);
                            @endphp
                            <li class="list-group-item">
                                <div class=" d-flex justify-content-between align-items-center">
                                    <div class=" font-weight-bold text-muted">{{ $poll_option->text ?? null}}
                                        <i class=" font-weight-normal"> - <span class=" text-muted">{{ $poll_option->description ?? null}}</span></i>
                                    </div>
                                    <div>
                                        {{-- <span class="badge badge-dark border rounded p-2"><span class="h6">{{$participations}} %</span></span> --}}
                                        <span class="badge badge-light border rounded p-2"><span class="h6">{{$count_answers}}</span></span>
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height: 0.8rem;">
                                    <div class="progress-bar bg-info font-weight-bold" role="progressbar" style="width: {{$participations ?? null}}%" aria-valuenow="{{$participations ?? null}}" aria-valuemin="0" aria-valuemax="100">{{$participations ?? null}}%</div>
                                </div>
                            </li>
                        @endforeach
                        </ul>
                    </div>
                </div>

            @endforeach

        </div>
    </div>
</div>


<hr>

@include('livewire.administracion.poll.table.partials.pestudios')

{{-- <div wire:ignore> @include('livewire.administracion.poll.charts.movimientocambiario') </div> --}}
