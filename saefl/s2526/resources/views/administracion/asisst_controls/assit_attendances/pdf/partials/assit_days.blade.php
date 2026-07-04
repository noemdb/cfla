{{-- <div>Días || Turnos || Horas</b></div> --}}
<table width="100%" class="table table-striped table-hover table-sm small p-1" cellpadding="0" cellspacing="0" style=" font-size:0.7rem;margin-bottom:0rem; padding-bottom:0rem;white-space: normal !important;">
    <tbody>
        <tr style="background-color: #fff;font-weight: bold">
            <td >Días</td>
            <td colspan="2">Turnos || Horas</td>
        </tr>
        @forelse ($assit_days as $assit_day)
        <tr style="background-color: #fff">
            <td style="padding-left: 0.4rem">{{$assit_day->name}}</td>
            <td>
                @php $assit_turns = $assit_day->assit_turns; @endphp
                @include('administracion.asisst_controls.assit_attendances.pdf.partials.assit_turns')
                {{-- {{$assit_turns ?? 'nada'}} --}}
            </td>
        </tr>
        @empty
            <tr><td>No hay datos</td></tr>
        @endforelse

    </tbody>
</table>
