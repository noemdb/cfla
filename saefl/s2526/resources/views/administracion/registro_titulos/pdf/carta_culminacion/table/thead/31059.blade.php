<thead style="color: #fff; background-color: #292b2c;">
    <tr>
        <th>N</th>
        <th>Asignatura</th>
        @foreach ($lapsos as $lapso)
            <th style="text-align:center">
                {{$lapso->code_sm ?? ''}}
            </th>
        @endforeach
        <th style="text-align:left">Definitiva</th>
    </tr>
</thead>
