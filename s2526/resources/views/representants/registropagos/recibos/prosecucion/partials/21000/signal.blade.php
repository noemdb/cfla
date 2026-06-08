<p>&nbsp;</p><p>&nbsp;</p>

{{-- <p style=" font-size:0.8rem; white-space: wrap; text-align:center">
    {{ $autoridad1->name ?? ''}} {{ $autoridad1->lastname ?? '' }}<br>
    CI Nº {{$autoridad1->ci ?? ''}}<br>
    <span class="text-muted">{{$autoridad1->position ?? ''}}</span><br>
</p> --}}

<p>&nbsp;</p>

<div style="text-align:center">

    <table align="center" style="font-size: 0.8rem; width:90% !important" class="table-list" border="1">
        <tr>
            <th width="50%" align="center">PLANTEL<br>PARA VALIDEZ A NIVEL NACIONAL</th>
            <th width="50%" align="center">ZONA EDUCATIVA<br>PARA VALIDEZ A NIVEL INTERNACIONAL</th>
        </tr>
        <tr>
            <td><small>Director(a)</small><br> Nombre y Apellido: <strong> {{ $autoridad1->name ?? ''}} {{ $autoridad1->lastname ?? '' }}</strong></td>
            <td><small>Director(a)</small><br> Nombre y Apellido: </td>
        </tr>
        <tr>
            <td><small>Número de CI</small><br><strong>{{$autoridad1->ci ?? ''}}</strong></td>
            <td><small>Número de CI</small><br>&nbsp;</td>
        </tr>
        <tr>
            <td>FIRMA Y SELLO <p>&nbsp;</p>  <p>&nbsp;</p> </td>
            <td>FIRMA Y SELLO <p>&nbsp;</p>  <p>&nbsp;</p> </td>
        </tr>
    </table>

</div>
