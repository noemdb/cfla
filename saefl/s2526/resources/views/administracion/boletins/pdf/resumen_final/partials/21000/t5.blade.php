<table border="1" cellpadding="0" cellspacing="0" width="100%" class="table-list" class="text_small" style="width: 100% !important;">
    <thead>
        <tr>
            <th>N°</th>
            <th>Apellidos</th>
            <th colspan="2">Nombres</th>
        </tr>
    </thead>
    <tbody style="font-size: 0.55rem !important">
        @php $estudiants_count = $estudiants->count(); @endphp
        @forelse ($estudiants as $estudiant)
            <tr>
                <td style="width: 2rem !important" style="width: 1rem !important;">{{$loop->iteration ?? null}}</td>
                <td >{{$estudiant->lastname ?? null}}</td>
                <td style="width: 50% !important" colspan="2">{{$estudiant->name ?? null}}</td>
            </tr>
        @empty
            <th colspan="3">No hay estudiantes</th>
        @endforelse

        @if ($estudiants_count < $limit_page)
            @php
            $down = $estudiants_count + 1;
            $up = $limit_page;
            @endphp
            @for ($j = $down; $j <= $up; $j++)
                <tr>
                    <td style="width: 1rem !important;">{{$j}}</td>
                    @for ($i = 1; $i < 3; $i++) <td align="left" {{ ($i==2) ? ' colspan=2 ' : null }}> &nbsp; *</td>  @endfor
                </tr>
            @endfor
        @endif

        @if ($profesor_guia)
            @php
                $profesor = $profesor_guia->profesor;
            @endphp
            <tr  style="font-size: 0.7rem !important; vertical-align:top">
                <td colspan="2">Nombre y Apellido del(la) Docente:</td>
                <td>Número de CI:</td>
                <td style="width:7rem !important">Firma</td>
            </tr>
            <tr style="font-size: 0.7rem !important; vertical-align:top">
                <td colspan="2">{{$profesor->fullname ?? null}}</td>
                <td>V-{{$profesor->ci_profesor ?? null}}</td>
                <td>&nbsp;</td>
            </tr>
        @else
            <tr>
                <td colspan="4">No hay profesor guía asignado</td>
            </tr>
        @endif

    </tbody>
</table>
