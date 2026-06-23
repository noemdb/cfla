{{-- <table cellpadding="0" cellspacing="0" border="1" width="100%" style="font-size:0.7rem;margin-bottom:0.2rem;"
    class="table"> --}}
    <table cellpadding="2" cellspacing="2" border="1" class="table-seccion grid" style="width: 100%">
        <TR>
            <th align="left" class="tr_strong">VII. Plantel</th>
            <TD rowspan="8" align="center" style="vertical-align: middle !important;">SELLO DEL PLANTEL</TD>
            {{-- <TD rowspan="8">&nbsp;</TD> --}}
            <th align="left" class="tr_strong">VIII. Zona Educativa</th>
            <TD rowspan="8" align="center" style="vertical-align: middle !important;">SELLO DE LA ZONA EDUCATIVA</TD>
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
            <TD class="tr_strong">{{ $autoridad1->fullname ?? ''}}</TD>
            <TD>&nbsp;</TD>
        </TR>
        <TR>
            <TD>Cédula de Identidad:</TD>
            <TD>Cédula de Identidad:</TD>
        </TR>
        <TR>
            <TD class="tr_strong">{{ $autoridad1->ci ?? ''}}</TD>
            <TD>&nbsp;</TD>
        </TR>
        <TR>
            <TD><br>Firma:<br><br></TD>
            <TD><br>Firma:<br><br></TD>
        </TR>
        <TR>
            <TD>Para efectos de su validez nacional</TD>
            <TD>Para efectos de su validez internacional</TD>
        </TR>
    </TABLE>