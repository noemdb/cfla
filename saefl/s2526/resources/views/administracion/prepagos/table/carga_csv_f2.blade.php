@php
    $class_N="d-none d-sm-table-cell";
    $class_representant="";
    $class_fecha="d-none d-md-table-cell";
    $class_banco="d-none d-md-table-cell";
    $class_monto="d-none d-lg-table-cell text-nowrap";
    $class_comment="text-wrap";
    $class_action="nosort text-center";
@endphp

{{-- @if ($errors->count() < $prepagosCSV->count())
    <button type="submit" class="btn btn-primary float-left">
        <i class="fa fa-save" aria-hidden="true"></i>
        Guardar
    </button>
@endif --}}

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default"> {{-- table-hover  --}}

    <thead>
        <tr>
            <th class="{{ $class_representant }} text-nowrap">N</th>
            <th class="{{ $class_representant }} text-nowrap">Fila</th>
            <th class="{{ $class_representant }} text-wrap">Representante</th>
            <th class="{{ $class_representant }} text-wrap">CI</th>
            <th class="{{ $class_representant }} text-wrap">Estudiante(s)</th>
            <th class="{{ $class_representant }} text-nowrap">Banco ORG</th>
            <th class="{{ $class_representant }} text-nowrap">Banco</th>
            <th class="{{ $class_representant }} text-nowrap">Referencia</th>
            <th class="{{ $class_representant }} text-nowrap" title="Fecha Operación">F. Operación</th>
            <th class="{{ $class_representant }} text-nowrap">Monto Bs.</th>
            <th class="{{ $class_representant }}">Telefono</th>
            <th class="{{ $class_representant }}">Comentario</th>
            <th class="{{ $class_representant }}">Incidencias</th>
            <th class="{{ $class_representant }}">Registrar</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @php $count_error = 0; @endphp

        @foreach($prepagosCSV as $prepagoCSV)

            @php
            $data = collect();
            $errors = $prepagoCSV['errors'];
            $datas = $prepagoCSV['datas'];

            $existNREX = $errors->where('code','NREX')->isNotEmpty();
            $existNRUA = $errors->where('code','NRUA')->isNotEmpty();
            $existNRR = $errors->where('code','NRR')->isNotEmpty();

            $class_error = (count($errors)) ? $errors[0]['class']:null;
            $class_error = ($existNREX || $existNRUA || $existNRR) ? 'danger':$class_error;
            @endphp

            <tr data-id="{{ $datas['representant_id'] ?? '' }}" class="table-{{ $class_error ?? '' }}">

                <td id="td-id" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>

                <td class=" font-weight-bold text-wrap {{ $class_representant  ?? ''}}">
                    {{ ($datas['index'] + 2) ?? '' }}
                </td>

                <td class=" font-weight-bold text-wrap {{ $class_representant  ?? ''}}">

                    <span class=" text-dark">{{ $datas['representant_name'] ?? '' }} </span>
                    {{-- <br>
                    <span class="small font-weight-bold text-muted">
                        {{ $datas['estudiant_name'] ?? '' }}
                        {{ $datas['ci_estudiant'] ?? '' }}
                    </span> --}}
                </td>

                <td class="text-nowrap {{ $class_representant ?? '' }}">
                    {{ $datas['representant_ci'] ?? '' }}
                </td>

                <td class="text-nowrap {{ $class_representant ?? '' }}">
                    {{-- {{ $datas['estudiant_name'] ?? '' }} {{ $datas['ci_estudiant'] ?? '' }} --}}
                    {{ $datas['data_estudiant_name_full'] ?? '' }}
                    {{-- {{ (empty($datas['estudiant_name'])) ? $datas['data_estudiant_name_full'] : $datas['estudiant_name']  }} --}}
                    {{-- {{ (empty($datas['ci_estudiant'])) ? $datas['data_ci_estudiant_full'] : $datas['ci_estudiant']  }} --}}
                </td>

                <td class="text-nowrap {{ $class_representant ?? '' }}">
                    {{ $datas['data_banco_ori_name'] ?? ''}}
                </td>

                <td class="text-nowrap {{ $class_representant ?? '' }}">
                    {{ $datas['data_banco_name'] ?? ''}}
                </td>

                <td class="{{ $class_representant ?? '' }}">
                    {{ $datas['number_i_pay'] }}
                </td>

                <td class="{{ $class_fecha ?? '' }}">
                    {{ f_date($datas['date_transaction']) }}
                </td>

                <td class="{{ $class_representant ?? '' }}">
                    {{ f_float($datas['ingreso_ammount']) ?? ''}}
                </td>

                <td class="{{ $class_representant }}" title="{{$datas['representant_phones'] ?? ''}}">
                    {{ Str::limit($datas['representant_phones'],12,'...') }}
                </td>

                <td class="{{ $class_comment }}" title="{{$datas['comments'] ?? ''}}">
                    {{-- {{ Str::limit($datas['comments'],40,'...') }} --}}
                    {{ $datas['comments'] ?? '' }}
                </td>


                <td class=" text-center {{ $class_representant ?? '' }} ">

                    @if (count($errors) > 0)
                        @include('administracion.prepagos.table.carga_csv.errors.error_f2')
                    @endif

                </td>
                <td class="text-center {{ $class_representant ?? '' }} ">
                    @if ( !($existNREX || $existNRUA || $existNRR) )
                        @include('administracion.prepagos.table.carga_csv.fields.hidden_f2')
                    @else
                        <span class=" btn badge badge-danger p-1 m-1">NO</span>
                    @endif
                </td>
            </tr>

            @endforeach

    </tbody>

</table>

<fieldset {{ $submitDisabled ?? '' }}>
    <div class="btn-group btn-block">
        <button type="submit" class="btn btn-primary btn-block {{ $submitDisabled ?? '' }}" title="{{ ($submitDisabled) ? 'No hay filas sin errores':null }}">
            <i class="fa fa-save" aria-hidden="true"></i>
            Guardar
        </button>
    </div>
</fieldset>



{{-- partials contentivo de los scripts datatables --}}
{{-- @include('administracion.datatables.simple_search') --}}
@include('administracion.datatables.exportBootstrap')

