<thead style="color: #fff; background-color: #292b2c;">
    <tr align="left">
        <th>N</th>
        <th>Asignatura</th>
        @foreach ($lapsos as $lapso)
            <th style="text-align:center">
                {{$lapso->code_sm ?? ''}}
                @admin <br> [ND][VSRPA][VSRSPA][PA] @endadmin
            </th>
        @endforeach
        <th style="text-align:left">Definitiva</th>
    </tr>
</thead>
