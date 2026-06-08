<table cellpadding="1" cellspacing="1" class="table-seccion" width="100%" border="0">
    <tbody>
        <tr>
            <th colspan="10" style="text-align: left">III. Identificación del Curso</th>
        </tr>
        <tr>
            <td class="tr_strong" style="width: 2rem !important;">Grado: </td>
            <td align="center" class="td_uline">{{ $grado->id ?? ''}}°</td>

            <td class="tr_strong" style="width: 2rem !important;">Sección:</td>
            <td align="center" class="td_uline">{{ $seccion->name ?? ''}}</td>

            <th class="tr_strong td-nowrap" colspan="2" style="text-align: center">N° de Estudiantes de la Sección:</th>
            <td align="center" class="td_uline" style="width: 2rem !important;">{{ $estudiants_full->count() ?? ''}}</td>


            <th class="tr_strong td-nowrap" colspan="2" style="text-align: center">N° de Estudiantes en esta Página:</th>
            @php $count = str_pad($estudiants->count(),2, "0", STR_PAD_LEFT); @endphp
            <td align="center" class="td_uline" style="width: 2rem !important;">{{ $count ?? null}} </td>

        </tr>
    </tbody>
</table>
