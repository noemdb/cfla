<div class="container-fluid border shadow py-2">

    <div class="row">
        <div class="col">
            <div class="h5 text-center font-weight-bold">Datos Generales del proceso de consulta</div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="d-flex justify-content-between">
                <div class=" pl-3 text-muted pb-0"><b>Comienzo:</b><br> {{$poll_main->start->format('d-m-Y H:i:s') ?? null}}</div>
                <div class=" pl-3 text-muted pb-0"><b>Finalización:</b><br> {{$poll_main->end->format('d-m-Y H:i:s') ?? null}}</div>
                <div class=" pl-3 text-muted pb-0"><b>Observaciones:</b><br> {{ $poll_main->observations ?? null }}</div>
                <div class=" pl-3 text-muted pb-0"><b>Total de Invitaciones:</b><br> {{ $count_tokens ?? null}}</div>
                <div class=" pl-3 text-muted pb-0"><b>Total de participantes:</b><br> {{ $count_poll_main_answer ?? null}}</div>
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        @foreach($poll_questions as $poll_question)
            @php $name = 'Pregunta '.$poll_question->id; @endphp
            <div class="col-md-4">
                <span>{{$name ?? null}}</span>
                <div class="font-weight-bold pb-0 px-2"> {{$poll_question->text ?? ''}} </div>

                <ul class="list-group list-group-flush">
                    @php $poll_options = $poll_question->poll_options; @endphp
                    @foreach($poll_options as $poll_option)
                        @php $name = 'Opción '.$loop->iteration; @endphp
                        <li class="list-group-item">
                            <div class="font-weight-bold">{{$name ?? null}}</div>
                            <div class=" font-weight-bold text-muted pl-2">
                                {{ $poll_option->text ?? null}}
                                <div class="pl-2"><i class=" font-weight-normal"><span class=" text-muted">{{ $poll_option->description ?? null}}</span></i></div>
                            </div>
                        </li>
                    @endforeach
                </ul>

            </div>
        @endforeach
    </div>

</div>


{{--

<ul class="list-group px-2">
    @foreach($poll_questions as $poll_question)
        @php $name = 'Pregunta '.$poll_question->id; @endphp
        <li class="list-group-item">
            <span>{{$name ?? null}}</span>
            <div class="font-weight-bold pb-0 px-2"> {{$poll_question->text ?? ''}} </div>
            <ul class="px-2">
                @php $poll_options = $poll_question->poll_options; @endphp
                @foreach($poll_options as $poll_option)
                    @php $name = 'Opción '.$loop->iteration; @endphp
                    <li class="">
                        <span>{{$name ?? null}}</span>
                        <span class=" font-weight-bold text-muted pl-2">
                            {{ $poll_option->text ?? null}}
                            <i class=" font-weight-normal"> - <span class=" text-muted">{{ $poll_option->description ?? null}}</span></i>
                        </span>
                    </li>
                @endforeach
            </ul>
        </li>
    @endforeach
</ul>

--}}
