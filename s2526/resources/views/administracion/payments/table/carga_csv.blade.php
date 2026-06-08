@php
    $class_N="d-none d-sm-table-cell";
    $class_representant="";
    $class_fecha="d-none d-md-table-cell";
    $class_banco="d-none d-md-table-cell";
    $class_monto="d-none d-lg-table-cell text-nowrap";
    $class_action="nosort text-center";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default"> {{-- table-hover  --}}

    <thead>
        <tr>
            <th class="{{ $class_representant }} text-nowrap">Fila</th>
            <th class="{{ $class_representant }} text-wrap">Representante</th>
            <th class="{{ $class_representant }} text-nowrap">M.Pago</th>
            <th class="{{ $class_representant }} text-nowrap">Banco</th>
            <th class="{{ $class_representant }} text-nowrap">Referencia</th>
            <th class="{{ $class_representant }} text-nowrap" title="Fecha Operación">F. Operación</th>
            <th class="{{ $class_representant }} text-nowrap">Monto Bs.</th>
            <th class="{{ $class_representant }}">Telefono</th>
            <th class="{{ $class_representant }}">Comentario</th>
            <th class="{{ $class_representant }}">Estado</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @php $count_error = 0; @endphp

        @foreach($prepagosCSV as $prepagoCSV)

            @php
            $class_error = null;
            $data = collect();
            $errors = $prepagoCSV['errors'];
            @endphp

            {{-- @include('administracion.prepagos.table.carga_csv.errors') --}}

            <tr data-id="{{ $prepagoCSV['representant_id'] ?? '' }}" class="table-{{ $errors[0]['class'] ?? 'default' }}">

                <td class=" font-weight-bold text-wrap {{ $class_representant  ?? ''}}">
                    {{ $prepagoCSV['fila'] ?? '' }}
                </td>

                <td class=" font-weight-bold text-wrap {{ $class_representant  ?? ''}}">
                    {{ $prepagoCSV['representant_name'] ?? '' }}
                    <span class="small font-weight-bold text-muted">
                        [{{ $prepagoCSV['ci_representant'] ?? '' }}]
                    </span>
                </td>

                <td class="text-nowrap {{ $class_representant }}">
                    {{$prepagoCSV['method_pay_name'] ?? ''}}
                </td>

                <td class="text-nowrap {{ $class_representant ?? '' }}">
                    {{ $prepagoCSV['banco_name'] ?? ''}}
                </td>

                <td class="{{ $class_representant ?? '' }}">
                    {{ $prepagoCSV['number_i_pay'] }}
                </td>

                <td class="{{ $class_fecha ?? '' }}">
                    {{-- {{ f_date($prepagoCSV['date_transaction']) ?? '' }} --}}
                    {{ $prepagoCSV['date_transaction'] ?? '' }}
                </td>

                <td class="{{ $class_representant ?? '' }}">
                    {{ f_float($prepagoCSV['ingreso_ammount']) ?? ''}}
                </td>

                <td class=" text-wrap {{ $class_representant ?? '' }}">
                    {{ $prepagoCSV['telefono'] ?? ''}}
                </td>

                <td class=" text-wrap {{ $class_representant ?? '' }}">
                    {{ $prepagoCSV['comment'] ?? ''}}
                </td>

                <td class=" text-wrap {{ $class_representant ?? '' }}">

                    @if ($errors->count()>0)
                        @php $modal_id = "mostrar_errors_".$loop->iteration; @endphp
                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#{{$modal_id ?? 'modal_id'}}" role="button">
                            <span class="badge badge-light">{{ ($errors->count()) ?? '' }}</span>
                        </button>
                        @include('administracion.prepagos.table.carga_csv.errors.modal')
                    @else
                        <button type="button" class="btn btn-success btn-sm" role="button">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </button>
                    @endif

                    @includeWhen($errors->count()==0,'administracion.prepagos.table.carga_csv.fields.hidden')

                </td>
            </tr>

            @endforeach

    </tbody>

</table>

@if ($prepagosCSV->count() > 0)

    <fieldset {{ ( $errors->count() == $prepagosCSV->count()) ? 'disabled':null  }} >
        <div class="btn-group btn-block">
            <button type="submit" class="btn-boletin btn btn-primary w-100">
                <i class="fa fa-save" aria-hidden="true"></i>
                Guardar
            </button>
        </div>
    </fieldset>

@endif

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.simple_search')


