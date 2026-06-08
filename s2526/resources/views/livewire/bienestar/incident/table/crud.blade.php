<table width="100%" class="table table-striped table-hover table-sm small p-1" style="padding-bottom:0.5rem;font-size:0.7rem !important;">
    <thead  class="thead-inverse"  align="left">
        <tr >
            <th>N.</th>
            <th>Código</th>
            <th>Profesor</th>
            <th>Deber</th>
            <th>Falta</th>
            <th>Descripción / Observaciones / Acciones tomadas</th>
			<th style="white-space:nowrap;">Fecha Registro</th>
        </tr>
    </thead>
    <tbody id="tdatos">

	    @foreach ($incidents as $incident)
			@php $profesor = $incident->profesor; @endphp
		    <tr>
		    	<th style="vertical-align: top" align="left">{{$loop->iteration}}</th>
		    	<th style="vertical-align: top" align="left">{{ $incident->code ?? null}}</th>
		    	<td style="vertical-align: top">{{ $profesor->fullname ?? null }}</td>
		    	<td style="vertical-align: top">{{ $incident->duty ?? null }}</td>
		    	<td style="vertical-align: top">{{ $incident->fault ?? null }}</td>
		    	<td style="vertical-align: top;width:60%" class="text_wrap">
					<div style="margin-left:0.2rem">
						<div>-. {{ $incident->description ?? null }}</div>
						@if (!empty($incident->observations)) <div>-. {{ $incident->observations ?? null }}</div> @endif
						@if (!empty($incident->taken_actions)) <div>-. {{ $incident->taken_actions ?? null }}</div> @endif
					</div>
		    	</td>
				<td style="vertical-align: top;white-space:nowrap;">{{ $incident->created_at->format('d-m-Y h:i') ?? null }}</td>
			</tr>
			@php $incident_actions = $incident->incident_actions; @endphp
			<tr>
				<td>&nbsp</td>
				<td colspan="6">
					Se convoca al representante: {{ ($incident->status_announcement) ? 'SI' : 'NO' }}  |
					Notificada: {{ ($incident->status_notify) ? 'SI' : 'NO' }} @if ($incident->status_notify) <i>[ {{ ($incident->date_notify_email) ?  $incident->date_notify_email->format('d-m-Y'): null }} ] </i> @endif  |
					Notificación de acuerdo: {{ ($incident->status_notify_agreement) ? 'SI' : 'NO' }} @if ($incident->status_notify_agreement) <i>[ {{ ($incident->date_notify_agreement_email) ? $incident->date_notify_agreement_email->format('d-m-Y')  : null }} ]</i> @endif
                    @if ($incident->status_close) || Incidencia cerrada @endif
                    || C.Pedagógico: {{ ($incident->incident_actions->isNotEmpty()) ? 'SI':'NO'}}
				</td>
			</tr>
			@php $agreements = $incident->incident_agreements; @endphp
			@if($agreements->isNotEmpty())

				<tr><td colspan="7">&nbsp;</td></tr>

				<tr><td></td><th colspan="7" align="left">Acuerdos</th> </tr>

				<tr>
					<th>&nbsp</th>
					<th>&nbsp</th>
					<th colspan="4" align="left">Descripción / Observaciones</th>
					<th colspan="1" align="left">Fec.Registro</th>
				</tr>
				@foreach ($agreements as $item)
					<tr>
						<td>&nbsp</td>
						<td  style="vertical-align: top;" align="center">{{$loop->iteration}}</td>
						<td  style="vertical-align: top;" colspan="4">
							@if (! empty($item->description))
								<div>-. {{ $item->description ?? null }}</div>
							@endif

							@if (! empty($item->observations))
								<div>-. {{ $item->observations ?? null }}</div>
							@endif	
						</td>
						<td  style="vertical-align: top;" colspan="1">{{ ($item->created_at) ? $item->created_at->format('d-m-Y h:i') : null }}</td>
					</tr>

                    @if(!$loop->last) <tr><td colspan="7"><hr></td></tr> @endif

				@endforeach

			@endif


			@php $incident_actions = $incident->incident_actions; @endphp
			@if($incident_actions->isNotEmpty())
				<tr><td colspan="7">&nbsp;</td></tr>
				<tr><td></td><th colspan="7" align="left">Correctivos:</th> </tr>
				<tr>
					<th>&nbsp</th>
					<th>&nbsp</th>
					<th colspan="4" align="left">Descripción / Observaciones</th>
					<th colspan="1" align="left">Fec.Registro</th>
				</tr>
				@foreach ($incident_actions as $item)
					@php $corrective = ($item) ? $item->incident_corrective : null ; @endphp
					<tr>
						<td>&nbsp</td>
						<td  style="vertical-align: top;" align="center">{{$loop->iteration}}</td>
						<td  style="vertical-align: top;" colspan="4">
							@if (! empty($corrective->description))
								<div>-. {{ $corrective->description ?? null }}</div>
							@endif
							@if (! empty($corrective->observations))
								<div>-. {{ $corrective->observations ?? null }}</div>
							@endif	
						</td>
						<td  style="vertical-align: top;" colspan="1">{{ ($item->created_at) ? $item->created_at->format('d-m-Y h:i') : null }}</td>
					</tr>
                    @if(!$loop->last) <tr><td colspan="7"><hr></td></tr> @endif
				@endforeach
			@endif

            @if ($incident->status_close)
                <tr><td colspan="7">&nbsp;</td></tr>
                <tr style="background-color: #ccc">
                    <th  style="vertical-align: top; padding-left:1rem; padding-right:1rem;" colspan="3" >Incidencia cerrada:<br>{{$incident->date_close->format('d-m-Y h:i') ?? null}}</th>
                    <td  style="vertical-align: top;" colspan="3">{{$incident->close_observations ?? null}}</td>
                    <td  style="vertical-align: top;" colspan="1">{{ ($incident->status_notify_close) ? 'Cierre Notificado' : null }}</td>
                </tr>
            @else
                <tr><td colspan="7" style="padding-top:1rem; padding-bottom:1rem; padding-left:1rem">Incidente abierto</td></tr>
            @endif

            @if(!$loop->last) <tr><td colspan="7"><hr></td></tr> @endif

		@endforeach
	</tbody>
</table>