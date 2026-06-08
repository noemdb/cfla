@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_fecha="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" style="font-size:0.7rem" id="table-data-default">
        <thead>
            <tr style="font-size: 0.8rem">
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Representante</th>
                <th class="{{ $class_fecha }}">CI</th>
                <th class="{{ $class_fecha }}">Fecha</th>
                <th class="{{ $class_planpago }}" align="right">Monto Bs.</th>
                {{-- <th class="{{ $class_planpago }}">Estado</th> --}}
                {{-- <th class="{{ $class_grado }}">Grado/Sección</th> --}}
                {{-- <th class="{{ $class_fecha }}" title="Fecha de Inscripción Administrativa">Fecha</th> --}}
                {{-- <th class="{{ $class_action }}">Acción</th> --}}
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($creditoafavors as $creditoafavor)

                @php
                    $estudiant = $creditoafavor->estudiant;
                    $representant = $creditoafavor->representant
                @endphp

                <tr data-estudiant_id="{{$estudiant->id ?? ''}}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td class="{{ $class_estudiant  ?? ''}}">
                        {{$representant->name}}
                    </td>

                    <td class="{{ $class_fecha ?? '' }}">
                        {{ $representant->ci_representant ?? ''}}
                    </td>

                    <td id="td-creditoafavor-created_at-{{ $estudiant->id }}" class="{{ $class_fecha ?? '' }}">
                        {{ f_date($creditoafavor->created_at) }}
                    </td>

                    <td id="td-planpago-estudiant-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}" align="right">
                        {{ f_float($creditoafavor->credito_ammount) ?? ''}}
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

{{-- </div> --}}
