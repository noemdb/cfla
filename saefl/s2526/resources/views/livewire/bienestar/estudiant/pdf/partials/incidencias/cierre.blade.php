<table  cellpadding="2" cellspacing="2"  style="background-color:#fff; font-size:0.6rem !important; border-collapse: collapse; width: 100%; border: 1px solid #000">
    <tbody>
        <tr>
            <td style="width: 100%;">

                <p style="text-align: right;">En San Felipe - Edo. Yaracuy a {{ ($incident->date_notify_email) ? $incident->date_notify_email->format('d F Y') : null}}</p>

                <p style="text-align: justify;">
                    Estimado(a) Sr(a).&nbsp;<em><strong>{{$representant->name}}</strong>, CI; {{$representant->ci_representant}},</em>
                </p>

                @php $address = ($representant) ? $representant->address : null;  @endphp
                @if ($address)
                    <p style="text-align: justify;">
                        Domiciliado en: <em><strong>{!!$address!!}</strong></em>
                    </p>
                @endif

                <div style="text-align: justify; padding-left: 1rem; padding-botton: 1rem;margin-bottom: 1rem">
                    <span style="margin-bottom: 0rem">Representante de:</span>
                    <div style="padding-left: 1rem; margin-top: 0rem">
                        @php $estudiants = $representant->estudiants_formaly @endphp
                        @foreach ($estudiants as $estudiant)
                            <div style="font-weight: bold">{{$estudiant->fullname}}</div>
                        @endforeach
                    </div>
                </div>

                <hr>

                <div style="">
                    <div style="">
                        <p style="text-align: justify;">
                            @php $profesor_guia = $incident->profesor_guia; @endphp
                            Se le notifica que la <span style="font-weight: bold;"> Coordinación de Bienestar Estudiantil</span> y el docente guía [{{ ($profesor_guia) ? $profesor_guia->fullname : null}}],
                            han cerrado la incidencia identificada con el código BE-I{{ $incident->id ?? null}}:
                        </p>
                        <div>
                            Observación de cierre: <span>{{ $incident->close_observations ?? null}}</span>
                        </div>
                    </div>

                    <hr>

                    <div>Detalles de la incidencia:</div>

                    <div style="margin-left:1rem">
                        <div>
                            <ul style="text-transform: uppercase; font-size:0.6rem">
                                <li><span style="font-weight: bold">Estudiante</span>: {{$estudiant->fullname ?? null}} <span style="color: #ccc">{{$estudiant->ci_estudiant ?? null}}</span></li>
                                <li><span style="font-weight: bold">Docente</span>: {{$profesor->fullname ?? null}}</li>
                                <li><span style="font-weight: bold">Deber</span>: {{$incident->duty ?? null}}</li>
                                <li><span style="font-weight: bold">Falta</span>: {{$incident->fault ?? null}}</li>
                                <li><span style="font-weight: bold">Descripción</span>: {{$incident->description ?? null}}</li>
                                @if ($incident->observations) <li><span style="font-weight: bold">Observaciones</span>: {{$incident->observations ?? null}}</li> @endif
                                <li><span style="font-weight: bold">Acciones tomadas</span>: {{$incident->taken_actions ?? null}}</li>
                                <li><span style="font-weight: bold">Fecha</span>: {{ $incident->created_at->format('d-m-Y') }}</li>
                                <li><span style="font-weight: bold">Registrado por</span>: {{ ($incident->user) ? $incident->user->profile->full_name : null}}</li>
                                @if ($incident->status_announcement) <li><span style="font-weight: bold">Fecha de la convocatoria al representante:</span> <span>{{ ($incident->time_announcement) ? $incident->time_announcement->format('d-m-Y h:i a') : null}}</span> </li> @endif
                            </ul>
                        </div>
                    </div>
                </div>
                @php $incident_agreements = $incident->incident_agreements @endphp
                @if ($incident_agreements->isNotEmpty())
                    <hr>
                    <div style="border:#ccc 1px solid;padding:0.5rem;border-radius: 0.2rem">
                        <div style="font-weight: bold;">Acuerdos alcazados:</div>

                        <div style="margin-left:0.0rem">

                            @foreach ($incident_agreements as $incident_agreement)
                                <div style="display: flex;">
                                    <div style="width: 2%; display:flex;align-items: center;justify-content: center;">{{ $loop->iteration}}</div>
                                    <div style="width: 98%;">
                                        <ul style="font-weight: bold;margin-bottom: 0.1rem;">
                                            <li>
                                                <span>Descripción:</span>
                                                <span style="margin-left:0.0rem; font-weight:normal;">{{$incident_agreement->description ?? null}}</span>
                                            </li>

                                            @if ($incident_agreement->observations)
                                                <li>
                                                    <span>Observaciones:</span>
                                                    <span style="margin-left:0.0rem; font-weight:normal;">{{$incident_agreement->observations ?? null}}</span>
                                                </li>
                                            @endif

                                            <li>
                                                <div>Fecha: <span style="font-weight:normal;">{{ ($incident_agreement->created_at) ? $incident_agreement->created_at->format('d-m-Y') : null }}</span></div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <hr style="margin: 0rem">
                            @endforeach

                        </div>

                    <br>

                @endif

            </td>
        </tr>
    </tbody>
</table>
