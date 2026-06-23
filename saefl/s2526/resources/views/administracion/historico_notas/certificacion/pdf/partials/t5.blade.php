<TABLE border="1" cellpadding=0 cellspacing=0 width="100%" id="table_normal">
    <TR>
        <th colspan="5" align="left">VI. Observaciones</th>
    </TR>
      <TR>
        <TD colspan="5" class="td_font_sm_1">
            INDICE ACADÉMICO ({{ $estudiant->GetIA($pestudio->id) ?? '' }}). {{ $historico_nota->observations ?? '' }}
            {{-- APLICACIÓN DEL PROCESO DE CONVERSIÓN Y TRANSFERENCIA DE ESTUDIOS DE ACUERDO AL MEMORANDO DE FECHA 17 DE NOVIEMBRE DE 2017 --}}
        </TD>
      </TR>
      <TR>
        <th align="left" class="tr_strong">VII. Institución Educativa</th>
        <TD style="width: 20% !important; vertical-align: middle !important;" rowspan="8" align="center">SELLO DE LA INSTITUCIÓN EDUCATIVA</TD>
        {{-- <TD rowspan="8">&nbsp;</TD> --}}
        <th align="left" class="tr_strong">VIII. ZONA EDUCATIVA DEL ESTADO YARACUY</th>
        <TD style="width: 20% !important; vertical-align: middle !important;" rowspan="8" align="center">SELLO DE LA ZONA EDUCATIVA DEL ESTADO YARACUY</TD>
      </TR>
      <TR>
        <TD>Directora</TD>
        <TD>Director (a)</TD>
      </TR>
      <TR>
        <TD>Apellidos y Nombres:</TD>
        <TD>Apellidos y Nombres:</TD>
      </TR>
      <TR>
        {{-- <TD class="tr_strong">{{ $autoridad1->name.' '.$autoridad1->lastname }}</TD> --}}
        <TD class="tr_strong">{{ $autoridad1->lastname .' '. $autoridad1->name }}</TD>
        <TD>&nbsp;</TD>
      </TR>
      <TR>
        <TD>Cédula de Identidad:</TD>
        <TD>Cédula de Identidad:</TD>
      </TR>
      <TR>
        {{-- <TD class="tr_strong">{{ $autoridad1->ci}}</TD> --}}
        <TD class="tr_strong">{{ $autoridad1->getCiFullF2()}}</TD>
        <TD>&nbsp;</TD>
      </TR>
      <TR>
        <TD><br>Firma:<br></TD>
        <TD><br>Firma:<br></TD>
      </TR>
      <TR>
        <TD>Para efectos de su validez nacional</TD>
        <TD>Para efectos de su validez internacional</TD>
      </TR>
  </TABLE>
