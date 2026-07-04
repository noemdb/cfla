<tr>
    <td align="center" style="font-weight: bold" colspan="4" rowspan="5">Total de Áreas de Formación</td>
    <td align="center" style="font-weight: bold" colspan="8">Inscritos</td>
    @foreach ($count_arr_in as $count)
        @php $result = str_pad($count, 2, "0", STR_PAD_LEFT);  @endphp
        <td align="center">{{ $result ?? '' }}</td>
    @endforeach
    {{-- <td align="center" >*</td> --}}
</tr>
<tr>
<td align="center" style="font-weight: bold" colspan="8">Inasistentes</td>
    @foreach ($count_arr_ia as $count)
        @php $result = str_pad($count, 2, "0", STR_PAD_LEFT);  @endphp
        <td align="center">{{ $result ?? '' }}</td>
    @endforeach
    {{-- <td align="center" >*</td> --}}
</tr>
<tr>
<td align="center" style="font-weight: bold" colspan="8">Aprobados</td>
    @foreach ($count_arr_ap as $count)
        @php $result = str_pad($count, 2, "0", STR_PAD_LEFT);  @endphp
        <td align="center">{{ $result ?? '' }}</td>
    @endforeach
    {{-- <td align="center" >*</td> --}}
</tr>
<tr>
<td align="center" style="font-weight: bold" colspan="8">No Aprobados</td>
    @foreach ($count_arr_ar as $count)
        @php $result = str_pad($count, 2, "0", STR_PAD_LEFT);  @endphp
        <td align="center">{{ $result ?? '' }}</td>
    @endforeach
    {{-- <td align="center" >*</td> --}}
</tr>
<tr>
<td align="center" style="font-weight: bold" colspan="8">No Cursantes</td>
    @foreach ($count_arr_nc as $count)
        @php $result = str_pad($count, 2, "0", STR_PAD_LEFT);  @endphp
        <td align="center">{{ $result ?? '' }}</td>
    @endforeach
    {{-- <td align="center" >*</td> --}}
</tr>
