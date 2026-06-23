<div class="" id="ticketRegister" tabindex="-1">
    <div>
        <div class="">
            <div class="">
                <h4 class="">
                    Datos del ticket de participación:
                </h4>
            </div>
            <div style="margin-left: 1rem">

                <ul class="px-2">
                    <li> <span class="">Número de registro:</span>  {!! $poll_token->id ?? null !!}</li>
                    <li> <span class="">Fecha:</span> {!! $toDate !!}</li>
                    <li> <span class="">Usuario:</span> CI-{!! $ci !!} - {!! $fullname !!} </li>
                </ul>
                <p>Preguntas y respuestas del instrumento aplicado:</p>
                <ul class="">
                    @foreach ($poll_questions as $poll_question)
                        @php
                            $poll_answer = $poll_question->poll_answers->where('token',$poll_token->token)->first();
                            $poll_option = ($poll_answer) ? $poll_answer->poll_option : null;
                        @endphp
                        @if($poll_option)
                            <li>
                                <div style="font-size: 0.8rem">{{$poll_question->text}}: </div>
                                <div style="margin-left: 1rem;">
                                    <span><b>Opción seleccionada:<b></span>
                                    <span style="margin-left: 0.2rem;">
                                        {{ $poll_option->text}} - <i style="font-weight: normal;">{{ $poll_option->description}}<i>
                                    </span>
                                </div>
                            </li>
                        @else
                            <div>No tiene registrada su participación</div>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
