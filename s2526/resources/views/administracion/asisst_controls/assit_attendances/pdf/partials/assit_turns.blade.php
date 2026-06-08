<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.7rem;margin-bottom:0rem; padding-bottom:0rem;">
    <tbody>
        {{-- <tr><td>Turnos</td></tr> --}}
        @forelse ($assit_turns as $assit_turn)
        <tr>
            <td style="padding-left: 0.4rem">{{$assit_turn->name}}</td>
            <td >
                @php $assit_hours = $assit_turn->assit_hours; @endphp
                @include('administracion.asisst_controls.assit_attendances.pdf.partials.assit_hours')
                {{-- {{$assit_hours ?? 'nada'}} --}}
            </td>
        </tr>

        @empty
            <tr><td>No hay datos</td></tr>
        @endforelse

    </tbody>
</table>
