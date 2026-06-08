<table width="100%" cellpadding="0" cellspacing="0"
    style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
    <thead>
        <tr>
            <th scope="row" width="70px">
                <img width="70px" height="70px" class="card-img-top" src="{{asset('images/avatar/uecfla.jpg')}}">
            </th>
            <th align="center" style="text-align: center;">
                <div class="title"><b>{{ $institucion->name }}</b></div>
                <div class="title"><b>DIRECCIÓN DE ADMINISTRACIÓN</b></div>
            </th>
            <th scope="row" width="70px">
                <img width="100px" height="70px" class="card-img-top" src="{{asset('images/avatar/amigoniano.png')}}">
            </th>
        </tr>
    </thead>
</table>

<hr>

<table cellpadding="2" cellspacing="2" style="border-collapse: collapse; width: 100%;" border="0" cell>
    <tbody>
        <tr>
            <td style="width: 100%;">

                <p style="text-align: right;">En San Felipe - Edo. Yaracuy a {{$toDate ?? null}}</p>

                @if ($interview)
                    <h1>Ticket de registro de participación</h1>
                    <h3>Entrevista interactiva</h3>

                    <hr>

                    <p style="text-align: justify;">
                        Estimado(a) Sr(a).&nbsp;<em><strong>{{$user->fullname}}</strong></em>
                    </p>

                    <p>Gracias por participar en nuestra encuesta interactiva. Su opinión es muy importante para nosotros y
                        nos ayudará a mejorar la experiencia de los estudiantes y las familias en nuestra institución
                        educativa.</p>

                    <p>Su participación ha sido registrada con éxito. En breve recibirá un correo electrónico con un resumen
                        de sus respuestas.</p>

                    <p>Agradecemos su tiempo y compromiso con nuestra institución educativa.</p>


                    <p>Sus repuestas a continuación:</p>

                    <ul>
                        @foreach ($interview_questions as $question)
                            @php $answer = $question->getInterviewAnswerUserId($user->id); @endphp
                            <li>
                                <div>{{$question->text}}: </div>                            
                                <div>{{$answer->text ?? null}}</div>
                            </li>
                        @endforeach
                    </ul>

                    <p>Esperamos que usted y su familia encuentren en nuestra institución el lugar ideal para que su hijo o hija reciba una educación de calidad.</p>

                @endif

                <hr>

                <strong>Agradecemos su interés en nuestra institución.</strong>

                <hr>

                <p style="text-align: center;">Atte.</p>
                <p>
                    <div>{{$director->profile_professional}} {{$director->fullname}}</div>
                    <div>{{$director->position}}</div>
                </p>
            </td>
        </tr>
    </tbody>
</table>

<hr>

<footer class="text-muted" style="font-size:0.8rem">
    <span>
        AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE ESTADO YARACUY. VENEZUELA<br>
        Teléfonos: 0424-5891682 / 0414-5442298. Correo electrónico: frayluisamigoyara@hotmail.com
    </span>
</footer>