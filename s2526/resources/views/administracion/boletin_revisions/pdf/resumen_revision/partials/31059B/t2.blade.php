<table cellpadding="1" cellspacing="1" class="table-seccion" width="100%" border="0">
    <thead>
        <tr>
            <th colspan="8" align="left">II. Datos de la Institución Educativa</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2" class="td-nowrap" style="width: 6rem;">Código de la Institución Educativa: <span class="td_uline small-90">{{ $institucion->code_oficial ?? '' }}</span></td>
            {{-- <td colspan="1" align="left" class="td_uline small-90" style="width: 48px !important;">{{ $institucion->code_oficial ?? '' }}</td> --}}
            <td colspan="3" align="right" style="width: 6rem !important;white-space: nowrap">Denominación y Epónimo:</td>
            <td colspan="3" class="td_uline small-90">{{ $institucion->name ?? '' }}</td>
        </tr>
        <tr>
            <td style="width: 48px !important;">Dirección:</td>
            <td colspan="5" class="td_uline small-90">{{ $institucion->address ?? '' }}</td>
            <td align="right" style="padding-right: 8px">Teléfono:</td>
            <td colspan="1" class="td_uline small-90 td-nowrap">{{ $institucion->phone ?? '' }} / {{ $institucion->phone2 ?? '' }}</td>
        </tr>
        <tr>
            <td style="width: 48px !important;">Municipio:</td>
            <td colspan="2" class="td_uline small-90 td-nowrap">{{ $institucion->town_hall ?? '' }}</td>
            <td align="right" class="td-nowrap">Entidad Federal:</td>
            <td colspan="1" class="td_uline small-90 td-nowrap">{{ $institucion->state ?? '' }}</td>
            <td colspan="1" align="right" style="padding-right: 8px">CDCEE:</td>
            <td colspan="2" class="td_uline small-90">YARACUY</td>
        </tr>
        <tr>
        <td style="width: 48px !important;">Director(a):</td>
        <td colspan="4" class="td_uline small-90 td-nowrap">{{ $autoridad1->fullname ?? ''}}</td>
        <td colspan="1" align="right" style="padding-right: 8px" class="td-nowrap">Cédula de Identidad:</td>
        <td colspan="2" class="td_uline small-90">{{ $autoridad1->getCiFullF2() ?? ''}}</td>
        </tr>
    </tbody>
</table>
