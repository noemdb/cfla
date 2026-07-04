@php $profesor = $incident->profesor; @endphp
@php $profesor_guia = $incident->profesor_guia; @endphp
<table  cellpadding="2" cellspacing="2"  style="font-size:0.6rem !important; border-collapse: collapse; width: 100%; border: 1px solid #000">
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

                {{-- <p style="text-align: justify;">
                    Se le notifica que se ha registrado una incidencia realcionada al estudiante <span style="font-weight: bold;">{{$estudiant->fullname ?? null}}</span>, la cuál contiene los siguiente detalles:
                </p> --}}
                <p style="text-align: justify;">
                    Se le notifica que la <span style="font-weight: bold;"> Coordinación de Bienestar Estudiantil</span> y el docente guía [{{ ($profesor_guia) ? $profesor_guia->fullname : null}}],
                    han cerrado la incidencia identificada con el código BE-I{{ $incident->id ?? null}}, realcionada al estudiante <span style="font-weight: bold;">{{$estudiant->fullname ?? null}}:
                </p>

                <ul style="text-transform: uppercase;">
                    <li><span style="font-weight: bold">Código</span>: BE-I{{ $incident->id ?? null}}</li>
                    <li><span style="font-weight: bold">Docente</span>: {{$profesor->fullname ?? null}}</li>
                    <li><span style="font-weight: bold">Deber</span>: {{$incident->duty ?? null}}</li>
                    <li><span style="font-weight: bold">Falta</span>: {{$incident->fault ?? null}}</li>
                    <li><span style="font-weight: bold">Descripción</span>: {{$incident->description ?? null}}</li>
                    @if ($incident->observations) <li><span style="font-weight: bold">Observaciones</span>: {{$incident->observations ?? null}}</li> @endif
                    <li><span style="font-weight: bold">Acciones tomadas</span>: {{$incident->taken_actions ?? null}}</li>
                    <li><span style="font-weight: bold"></span>: {{ ($incident->status_aggression) ? 'SI':'NO'}}</li>
                    <li><span style="font-weight: bold">Se convoca al representante</span>: {{ ($incident->status_announcement) ? 'SI':'NO'}}</li>
                    <li><span style="font-weight: bold">Fecha</span>: {{ $incident->created_at->format('d-m-Y') }}</li>
                </ul>

                @if ($incident->status_announcement) 
                    <hr>               
                    <p>
                        Se solicita una entrevista con el representante.
                    </p>
                @endif
            
            </td>
        </tr>
    </tbody>
</table>