<table class="table-sm" style=" font-size:0.7rem;">
    <tr>
        <th colspan="3">
            En San Felipe {{ $fecha_remision->format('d-m-Y') ?? '' }}.

        </th>
    </tr>
    <tr>
        <th colspan="3" style="text-align:center;background-color: #fff;">
            <p> &nbsp; </p>
            <p> &nbsp; </p>
            <p> Sello de la Institución </p>
        </th>
    </tr>

    <tr>
        <td width="33%">

            @php $profesor_guia = ($estudiant->profesor_guia) ? $estudiant->profesor_guia:null; @endphp

            {{ $profesor_guia->fullname ?? '' }}<br>

            CI: {{ $profesor_guia->ci_profesor ?? '' }}<br>

            <span class="text-muted">Profesor Guía</span>

        </td>
        <td width="33%">

        </td>
        <td width="33%">
            {{ $autoridad1->name.' '.$autoridad1->lastname }}<br>
            CI: {{ $autoridad1->ci ?? '' }}<br>
            <span class="text-muted">{{$autoridad1->position ?? ''}}</span>
        </td>
    </tr>
</table>
