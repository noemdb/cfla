@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="";
    $class_grado="";
    $class_promedio="text-right";
    $class_position="text-right";
    $class_solvente="text-left";
    $class_action="nosort";
@endphp

<tbody id="tdatos" style="font-size: 0.7rem !important">

    @foreach($estudiants as $estudiant)

        <tr>

            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>

            <td class="{{ $class_estudiant  ?? ''}}">
                {{$estudiant['ci_estudiant']}}
            </td>

            <td class="{{ $class_estudiant  ?? ''}}">
                {{$estudiant['fullname']}}
            </td>

            <td class="{{ $class_promedio  ?? ''}}" style="text-align: right">
                {{$estudiant['nota']}}
            </td>

        </tr>

    @endforeach

</tbody>
