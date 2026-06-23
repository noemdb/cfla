<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.7rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
    <tbody>
        {{-- <tr><td>Hora de Entrada/Salída</td></tr> --}}
        <tr>
            <td>
                @foreach ($assit_hours as $assit_hour)
                <span> {{str_pad($assit_hour->h, 2,'0', STR_PAD_LEFT)}}:{{str_pad($assit_hour->m, 2,'0', STR_PAD_LEFT)}} {{($assit_hour->type) ? 'Ent.':'Sal.'}}</span>
                @if (! $loop->last ) || @endif
                @endforeach
            </td>
        </tr>
    </tbody>
</table>
