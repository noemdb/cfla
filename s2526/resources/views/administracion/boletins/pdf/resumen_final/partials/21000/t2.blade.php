<table cellpadding="1" cellspacing="1" class="table-seccion" width="100%" border="0">
    <thead>
        <tr>
            <th colspan="8" style="text-align: left">II. Datos de la Institucion Educativa:</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td  class="tr_strong" colspan="2" class="td-nowrap" style="width: 6rem;">Código del Institucion Educativa:</td>
            <td colspan="1" class="td_uline small-90" style="width: 48px !important;">{{ $institucion->code_oficial ?? '' }}</td>
            <td class="tr_strong" align="right" style="width: 60px !important;">Denominación y Epónimo::</td>
            <td colspan="2" class="td_uline small-90 td-nowrap">{{ $institucion->name ?? '' }}</td>
            <td class="tr_strong"> <b>Territorio</b>:</td>
            <td class="td_uline" align="center">73</td>
        </tr>
        <tr>
            <td class="tr_strong" style="width: 48px !important;">Dirección:</td>
            <td colspan="5" class="td_uline small-90">{{ $institucion->address ?? '' }}</td>
            <td  class="tr_strong" align="right" style="padding-right: 8px">Teléfono:</td>
            <td colspan="1" class="td_uline small-90 td-nowrap">{{ $institucion->phone ?? '' }} / {{ $institucion->phone2 ?? '' }}</td>
        </tr>
        <tr>
            <td  class="tr_strong" style="width: 48px !important;">Municipio:</td>
            <td colspan="2" class="td_uline small-90 td-nowrap">{{ $institucion->town_hall ?? '' }}</td>
            <td  colspan="2" class="tr_strong" align="right" class="td-nowrap">Ent. Federal:</td>
            <td colspan="1" class="td_uline small-90 td-nowrap">{{ $institucion->state ?? '' }}</td>
            <td  class="tr_strong" colspan="1" align="right" style="padding-right: 8px">CDCEE:</td>
            <td colspan="1" class="td_uline small-90">YARACUY</td>
        </tr>
        {{-- <tr>
            <td style="width: 48px !important;">Director(a):</td>
            <td colspan="4" class="td_uline small-90 td-nowrap">{{ $autoridad1->fullname ?? ''}}</td>
            <td colspan="1" align="right" style="padding-right: 8px" class="td-nowrap">Cédula de Identidad:</td>
            <td colspan="2" class="td_uline small-90">V-{{ $autoridad1->ci ?? ''}}</td>
        </tr> --}}

        {{-- <tr>
            <td colspan="8">
                @include('administracion.boletins.pdf.resumen_final.partials.21000.t3')
            </td>
        </tr> --}}
    </tbody>
</table>
