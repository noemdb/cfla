

@php
    //'user_id','ci_attendee','name','phone','cellphone','pmovilphone','email','gsemail','status_active','status_blacklist'
    $class['iteration']="d-none d-sm-table-cell";
    $class['name']="d-none d-sm-table-cell";
    $class['ci_attendee']="d-none d-sm-table-cell";
    $class['email']="d-none d-sm-table-cell";
    $class['status_notifiled']="d-none d-sm-table-cell text-right";
    $class['action']="d-none d-sm-table-cell";
    $table_id = 'table-data-default-show';
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['name'] ?? ''}}">Participantes</th>
            <th class="{{ $class['name'] ?? ''}}">Usuario</th>
            <th class="{{ $class['name'] ?? ''}}">Grado/Sección</th>
            <th class="{{ $class['name'] ?? ''}}">Email</th>
            <th class="{{ $class['name'] ?? ''}}">Token</th>
            <th class="{{ $class['ci_attendee'] ?? ''}}">CI</th>
            <th class="{{ $class['email'] ?? ''}}">Tipo</th>
            <th class="{{ $class['email'] ?? ''}}">Participación</th>
            @foreach($poll_questions as $poll_question)
                @php $name_question = 'P'.$poll_question->id; @endphp
                @php $poll_options = $poll_question->poll_options; @endphp
                @foreach($poll_options as $poll_option)
                    @php $name_option = $name_question. '|O'.$loop->iteration; @endphp
                        <th class="{{ $class['email'] ?? ''}}">{{$name_option ?? null}}</th>
                @endforeach
            @endforeach
        </tr>
    </thead>

    <tbody id="tdatos-{{$table_id}}">

        @forelse($poll_tokens as $poll_token)

            @php
                $attendee = $poll_token->user;
                $poll_answers = $attendee->getPollAnswers($poll_main->id);
                $staus_competitor = ($poll_answers->count()) ? true : false ;
                $poll_token = $poll_main->getTokenAttendeeUserId($attendee->id);
                $urlTokent = ($poll_token) ? env('APP_URL').'/general/polls/'.$poll_token->token : null;
            @endphp

            <tr data-id="{{$attendee->id}}" class="">
                <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
                <td class="{{ $class['name'] ?? ''}}">                    
                    @if($attendee->IsRepresentant())
                        <div>{{ $attendee->representant->name ?? null }}</div>
                    @else
                        <div>{{$attendee->fullname ?? ''}}</div>
                    @endif
                </td>

                <td class="{{ $class['name'] ?? ''}}">
                    <div>{{$attendee->username ?? ''}}</div>
                </td>

                <td class="{{ $class['name'] ?? ''}}">
                    @if($attendee->IsEstudiant())
                        @php $estudiant = $attendee->estudiant; @endphp
                        @if ($estudiant->getInscripcion())
                            <span
                                class="{{ $estudiant->getInscripcion()->seccion->grado->class_text_color ?? 'default' }}">
                                {{ $estudiant->getInscripcion()->seccion->grado->name ?? '' }}
                                {{ $estudiant->getInscripcion()->seccion->name ?? '' }}
                            </span>
                        @else
                            -SIN SECCION-
                        @endif
                    @endif
                </td>

                <td class="{{ $class['name'] ?? ''}}">
                    <div>{{$attendee->email ?? ''}}</div>
                </td>

                {{-- @admin --}}
                <td class="{{ $class['name'] ?? ''}}">
                    <div>{{$urlTokent ?? ''}}</div>
                </td>
                {{-- @endadmin --}}
                <td class="{{ $class['iteration'] ?? ''}}">{{$attendee->ci ?? ''}}</td>
                <td class="{{ $class['iteration'] ?? ''}}">{{$attendee->area ?? ''}} || {{$attendee->rol ?? ''}}</td>
                <td class="{{ $class['action'] ?? ''}}">
                    {{ ($staus_competitor) ? '-SI-':'-NO-'}}
                </td>
                @foreach($poll_questions as $poll_question)
                    @php
                        $poll_answer = $poll_question->getPollAnswerAttendee($attendee->id);
                        $poll_option_id = ($poll_answer) ? $poll_answer->poll_option_id : null ;
                        $poll_options = $poll_question->poll_options;
                    @endphp
                    @foreach($poll_options as $poll_option)
                        <td class="{{ $class['email'] ?? ''}}">{{ ($poll_option->id == $poll_option_id) ? 'X' : null}}</td>
                    @endforeach
                @endforeach
            </tr>

        @empty

            <tr>
                <td colspan="4" align="center">
                    <strong>No hay datos</strong>
                </td>
            </tr>

        @endforelse

    </tbody>

</table>

{{-- @include('academicos.datatables.default') --}}
@include('administracion.datatables.exportBootstrap')

