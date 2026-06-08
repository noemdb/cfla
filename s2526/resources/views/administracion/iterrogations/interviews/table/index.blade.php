@php
    $class_N="d-none d-sm-table-cell";
    $class_default="";
    $class_code_sm="d-none d-lg-table-cell";
    $class_ht="d-none d-md-table-cell";
    $class_hp="d-none d-lg-table-cell";
    $class_action="";
    $class_action="nosort";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_default }}">Usuario.</th>
            <th class="{{ $class_default }}">Entrevista</th>
            <th class="{{ $class_default }}">Pregunta</th>
            <th class="{{ $class_default }}">Respuesta</th>
            <th class="{{ $class_default }}">Imagen</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($interview_answers as $interview_answer)

        @php
            $user = $interview_answer->user;
            $interview = $interview_answer->interview;
            $interview_question = $interview_answer->interview_question;
            $interviewAttendee = $interview_answer->interviewAttendee;
        @endphp

        <tr data-id="{{$interview_answer->id}}">
            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>
            <td class="{{ $class_default  ?? ''}}">
                {{($user) ? $user->fullname : null}}
            </td>
            <td class="{{ $class_default  ?? ''}}">
                {{($interview) ? $interview->name : null}}
            </td>

            <td class="{{ $class_default  ?? ''}}">
                {{($interview_question) ? $interview_question->text : null}}
            </td>
            </td>

            <td class="{{ $class_default  ?? ''}}">
                {{($interview_answer) ? $interview_answer->text : null}}
            </td>

            <td class="{{ $class_default  ?? ''}}">
                <img src="{{($interviewAttendee) ? $interviewAttendee->PhotoUrl : null}}" class="img-fluid" alt="">
                
            </td>

        </tr>
    @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.exportBootstrap')

