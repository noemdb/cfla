{{-- <p>&nbsp;</p> --}}
<table width="50%" border="1" align="center" cellpadding="0" cellspacing="0" style="margin-top: 0.5rem">
  <tr>
    <td class="th_2_sb">Datos de Elaboración, Verificación y Autorización del Documento</td>
  </tr>
  <tr>
    {{-- <td bgcolor="#000000" class="td_dark">UNIDAD EDUCATIVA COLEGIO FRAY LUIS AMIGÓ</td> --}}
    <td bgcolor="#000000" class="td_dark"><b>{{ $institucion->name ?? null }}</b></td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC">{{$autoridad2->position ?? null}}</td>
    {{-- <td bgcolor="#CCCCCC">Jefe de Control de Estudios que elabora y autoriza el documento</td> --}}
  </tr>
  <tr>
    <td class="tr_strong">{{ $autoridad2->full_name ?? null }}</td>
    {{-- <td class="tr_strong">LIDOSKA BEATRIZ VELIZ DUDAMELL</td> --}}
  </tr>
  <tr>
    <td class="tr_strong">C.I. {{$autoridad2->ci}}</td>
  </tr>
  <tr>
    <td class="tr_strong">Firma:<br>&nbsp;</td>
  </tr>
</table>