<div style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">

    <p style="text-align: justify;">
        Detalles de la incidencia:
    </p>

    <ul style="text-transform: uppercase;">
        <li><span style="font-weight: bold">Código</span>: BE-I{{ $incident->id ?? null}}</li>
        <li><span style="font-weight: bold">Docente</span>: {{$profesor->fullname ?? null}}</li>
        <li><span style="font-weight: bold">Deber</span>: {{$incident->duty ?? null}}</li>
        <li><span style="font-weight: bold">Falta</span>: {{$incident->fault ?? null}}</li>
        <li><span style="font-weight: bold">Descripción</span>: <span style="white-space: normal !important;">{{$incident->description ?? null}}</span> </li>
        @if ($incident->observations) <li><span style="font-weight: bold">Observaciones</span>: {{$incident->observations ?? null}}</li> @endif
        <li><span style="font-weight: bold">Acciones tomadas</span>: {{$incident->taken_actions ?? null}}</li>
        <li><span style="font-weight: bold">Fecha</span>: {{ $incident->created_at->format('d-m-Y') }}</li>        
        <li><span style="font-weight: bold">Notificada al representante</span>: {{($incident->status_notify) ? 'SI' : 'NO'}} / {{ f_date($incident->date_notify_email) }}</li>
        <li><span style="font-weight: bold">Notificación de acuerdo</span>: {{($incident->status_notify_agreement) ? 'SI' : 'NO'}} / {{ f_date($incident->date_notify_agreement_email) }}</li>
        <li><span style="font-weight: bold">Incidencia cerrada</span>: {{($incident->status_close) ? 'SI' : 'NO'}} / {{ f_date($incident->date_close) }}</li>
        <li><span style="font-weight: bold">Notificación de cierre</span>: {{($incident->status_notify_close) ? 'SI' : 'NO'}}</li>
        @if ($incident->close_observations) <li><span style="font-weight: bold">Observación de cierre</span>: {{$incident->close_observations ?? null}}</li> @endif
        <li><span style="font-weight: bold">Se convoca al representante</span>: {{($incident->status_announcement) ? 'SI' : 'NO'}}</li>
        @if ($incident->time_announcement) <li><span style="font-weight: bold">Fecha de la convocatoria:</span> <span>{{ ($incident->time_announcement) ? $incident->time_announcement->format('d-m-Y h:i a') : null}}</span> </li> @endif

        <li><span style="font-weight: bold">Registrado por</span>: {{ ($incident->user) ? $incident->user->profile->full_name : null}}</li>
    </ul>


<hr>

<div style="border:#ccc 1px solid;padding:0.5rem;border-radius: 0.2rem">
    
    <div style="font-weight: bold;">Detalles de los acuerdos registrados:</div>

    <div style="margin-left:0.5rem">        

            @php $incident_agreements = $incident->incident_agreements; @endphp

            @forelse ($incident_agreements as $incident_agreement)
                N° {{$loop->iteration}}
                    <div>
                        <strong>Descripción:</strong> <span style="margin-left:0.5rem; font-weight:normal;">{{$incident_agreement->description ?? null}}</span>
                    </div>                        

                    @if ($incident_agreement->observations)
                        <div>
                            <strong>Observaciones:</strong> <span style="margin-left:0.5rem; font-weight:normal;">{{$incident_agreement->observations ?? null}}</span>
                        </div>                            
                    @endif

                    <div><strong>Fecha:</strong> <span style="font-weight:normal;">{{ ($incident_agreement->created_at) ? $incident_agreement->created_at->format('d-m-Y') : null }}</span></div>
                <hr>
            @empty
                <div>No hay acuerdos registrados</div>
            @endforelse
        
    </div>

</div>

<hr>

<div style="border:#ccc 1px solid;padding:0.5rem;border-radius: 0.2rem">
    {{-- <div style="font-weight: bold;">:</div> --}}
    @if ($incident->status_close)

        <ul style="font-weight: bold;">
            <li> Incidencia cerrada:{{$incident->date_close->format('d-m-Y h:i') ?? null}} </li>
            <li> Observación:{{$incident->close_observations ?? null}} </li>
            <li> Estado: {{ ($incident->status_notify_close) ? 'Cierre Notificado' : null }}</li>
        </ul>

    @else
        <div>
            Incidente abierto
        </div>
    @endif
</div>
