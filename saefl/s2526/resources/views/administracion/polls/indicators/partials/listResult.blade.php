<div class="container-fluid border shadow py-2">
    <div class="row">
        <div class="col">
            <div class="h5 text-center font-weight-bold">Lista de resultados del proceso de consulta</div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
            <ul class="list-group px-2">
                @foreach($poll_questions as $poll_question)
                    @php $name = 'Pregunta '.$poll_question->id; @endphp
                    <li class="list-group-item">
                        <span>{{$name ?? null}}</span>
                        <div class="d-flex justify-content-between">
                            <div class="font-weight-bold pb-0 px-2"> {{$poll_question->text ?? ''}} </div>
                            <div>Votos</div>
                        </div>
                        
                        <ol class="list-group  list-group-numbered">
                            {{-- @php $poll_options = $poll_question->poll_options; @endphp --}}

                            @php 
                                $poll_main = $poll_question->poll_main;
                                $competitors = $poll_main->competitors;
                                $count_competitors = $competitors->count();
                                $poll_options = $poll_question->getPollOptionsAnswer();
                            @endphp
                            @foreach($poll_options as $poll_option)
                                @php 
                                    $poll_answers = $poll_option->poll_answers;
                                    $name = 'Opción '.$loop->iteration;
                                    $porcentage = ($count_competitors > 0) ? 100 * $poll_answers->count() / $count_competitors : null ;
                                    // $porcentage = round($porcentage,2);
                                    $porcentage = number_format($porcentage, 2, '.', '');
                                @endphp
                                <li class="list-group-item py-1 px-2 {{($loop->first) ? 'list-group-item-success' : null}}">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="d-flex">
                                                <div>{{$loop->iteration ?? null}}.</div>
                                                {{-- <span>{{$name ?? null}}</span> --}}
                                                <div class=" font-weight-bold text-muted pl-2">
                                                    {{ $poll_option->text ?? null}}
                                                    <div>
                                                        <i class=" font-weight-normal">
                                                            <span class=" text-muted">{{ $poll_option->description ?? null}}</span>
                                                        </i>
                                                    </div>
                                                </div>
                                            </div>                                            
                                            
                                        </div>

                                        <div class="my-2">
                                            <span class="p-2 rounded border font-weight-bold">
                                                {{$porcentage ?? null}} %
                                            </span>
                                            <span class="mx-2">
                                                {{ ($poll_answers->isNotEmpty()) ? $poll_answers->count() : null }}
                                            </span>
                                            
                                        </div>

                                    </div>

                                </li>
                            @endforeach
                            
                            <li class="list-group-item font-weight-bold list-group-item-secondary">
                                <div class="d-flex justify-content-between">
                                    @php $poll_answers = $poll_question->poll_answers; @endphp
                                    <div>Total</div>
                                    <div>{{ ($poll_answers->isNotEmpty()) ? $poll_answers->count() : 0}}</div>
                                </div>
                            </li>
                        </ol>
                    </li>
                @endforeach
            </ul>

        </div>

    </div>
</div>
