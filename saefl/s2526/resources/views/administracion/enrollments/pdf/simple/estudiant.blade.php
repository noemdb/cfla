<table cellspacing="3" cellpadding="3" class="table-sm" style="padding-top:0.4rem;font-size:0.6rem !important" border="1">
    <thead>
      <tr>
        <th colspan="6" align="left" style="font-weight: bold !important;font-size: 0.8rem !important">DATOS DE ALUMNO(A):</th>
        <th colspan="5" style="font-weight: bold !important;font-size: 0.8rem !important">TELÉFONOS</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        {{-- <td colspan="4" style="border: 0px"></td> --}}
        <td colspan="6" rowspan="2" align="left">TELÉFONOS</td>
        <td colspan="2">TEL.</td>
        <td colspan="3">{{$estudiant->cellphone ?? ''}}</td>
      </tr>
      <tr>
        {{-- <td colspan="4" style="border: 0px"></td> --}}
        <td colspan="2">CORREO</td>
        <td colspan="3">{{$estudiant->email ?? ''}}</td>
      </tr>
      <tr>
        <td colspan="6">APELLIDOS: {{$estudiant->lastname ?? ''}}</td>
        <td colspan="5">NOMBRES: {{$estudiant->name ?? ''}}</td>
        {{-- <td colspan="3"></td> --}}
      </tr>
      <tr>
        <td>SEXO</td>
        <td colspan="3">CEDULA DE IDENTIDAD</td>
        <td>EDAD</td>
        <td colspan="3" align="center">FECHA DE NACIMIENTO</td>
        <td style="width: 3.5rem">CIU. DE N.</td>
        <td colspan="2">{{$estudiant->town_hall_birth ?? ''}}</td>
      </tr>
      <tr>
        <td>{{$estudiant->gender ?? ''}}</td>
        <td colspan="3">{{$estudiant->ci_estudiant ?? ''}}</td>
        <td>{{($estudiant->age) ? $estudiant->age : null}}</td>
        <td>DÍA</td>
        <td>MES</td>
        <td>AÑO</td>
        <td style="width: 3.5rem">EDO. DE N.</td>
        <td colspan="2">{{$estudiant->state_birth ?? ''}}</td>
      </tr>
      <tr>
        <td colspan="5"></td>
        <td>{{$estudiant->day ?? ''}}</td>
        <td>{{$estudiant->month ?? ''}}</td>
        <td>{{$estudiant->year ?? ''}}</td>
        <td style="width: 3.5rem">PAÍS. DE N.</td>
        <td colspan="2">{{$estudiant->country_birth ?? ''}}</td>
      </tr>
      <tr>
        <td colspan="11" style="font-weight: bold !important;font-size: 0.8rem !important">CURSOS (MARQUE CON UNA X LA CASILLA CORRESPONDIENTE)</td>
      </tr>
      <tr>
        <td colspan="6">PRIMARIA</td>
        <td colspan="5">MEDIA GENERAL</td>
      </tr>
      <tr align="center">
        <td>1</td>
        <td>2</td>
        <td>3</td>
        <td>4</td>
        <td>5</td>
        <td>6</td>
        <td>1</td>
        <td>2</td>
        <td>3</td>
        <td>4</td>
        <td>5</td>
      </tr>
      <tr align="center">
        <td>{!!($grado_next->id=='1') ? 'X' : '&nbsp;'!!}</td>
        <td>{!!($grado_next->id=='2') ? 'X' : '&nbsp;'!!}</td>
        <td>{!!($grado_next->id=='3') ? 'X' : '&nbsp;'!!}</td>
        <td>{!!($grado_next->id=='4') ? 'X' : '&nbsp;'!!}</td>
        <td>{!!($grado_next->id=='5') ? 'X' : '&nbsp;'!!}</td>
        <td>{!!($grado_next->id=='6') ? 'X' : '&nbsp;'!!}</td>
        <td>{!!($grado_next->id=='7') ? 'X' : '&nbsp;'!!}</td>
        <td>{!!($grado_next->id=='8') ? 'X' : '&nbsp;'!!}</td>
        <td>{!!($grado_next->id=='9') ? 'X' : '&nbsp;'!!}</td>
        <td>{!!($grado_next->id=='10') ? 'X' : '&nbsp;'!!}</td>
        <td>{!!($grado_next->id=='11') ? 'X' : '&nbsp;'!!}</td>
      </tr>
      <tr><td colspan="11">PARTICIPACIÓN EN GRUPOS: </td></tr>
      <tr><td colspan="11">MATERIA PENDIENTE: {{$estudiant->pending_matter ?? ''}}</td> </tr>
    </tbody>
    </table>
