<p style="font-size:1rem; background:#f2f2f2">
    <b>Horario de Trabajo.</b>
</p>
<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.9rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
    <tr>
        <td width="50%" style="margin-right: 0.2rem; vertical-align:text-top !important">


            <table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.8rem !important;margin-bottom:0.2rem; padding-bottom:0.2rem;">
                <tbody>
                    <tr style="background-color: #fff">
                        <td scope="row"><b>Nombre:</b></td>
                        <td scope="row">{{ $assit_schedule->name ?? null}}</td>
                    </tr>
                    <tr style="background-color: #fff">
                        <td><b>Cantidad de Turnos:</b></td>
                        <td>{{ $assit_schedule->number_turn ?? null}}</td>
                    </tr>
                    <tr style="background-color: #fff">
                        <td><b>Descripción:</b></td>
                        <td style="white-space: normal !important;">{{ $assit_schedule->description ?? null}}</td>
                    </tr>
                    <tr style="background-color: #fff">
                        <td><b>Frecuencia:</b></td>
                        <td>{{ $assit_schedule->frecuency ?? null}}</td>
                    </tr>
                </tbody>
            </table>
        </td>
        <td width="50%" style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem; margin-left: 0.2rem">
            @if ($assit_schedule)
                @php $assit_days = $assit_schedule->assit_days; @endphp
                @include('administracion.asisst_controls.assit_attendances.pdf.partials.assit_days')
            @else
                <span>No hay datos de Horario</span>
            @endif

        </td>
    </tr>
</table>
