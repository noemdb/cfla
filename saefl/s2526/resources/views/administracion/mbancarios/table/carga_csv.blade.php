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
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_representant }} text-nowrap" title="Fecha de la operación">F. Operación</th>
            <th class="{{ $class_representant }} text-nowrap">Referencia</th>
            <th class="{{ $class_representant }} text-nowrap">Monto Bs.</th>
            <th class="{{ $class_representant }} text-nowrap">Errores</th>
            <th class="{{ $class_representant }} text-nowrap">Estado</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @php $count_error = 0; @endphp

        @foreach($mbancariosCSV as $mbancarioCSV)

            @php
            $class_error = null;
            $data = collect();
            $errors = $mbancarioCSV['errors'];
            @endphp

            <tr data-id="{{ $loop->iteration ?? '' }}" class="table-{{ $mbancarioCSV['class'] ?? 'default' }}">

                <td id="td-id" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>

                <td class="{{ $class_fecha ?? '' }}">
                    {{ f_date($mbancarioCSV['date_transaction']) ?? ''}}
                </td>

                <td class="{{ $class_representant ?? '' }}">
                    {{ $mbancarioCSV['number_i_pay'] }}
                </td>

                <td class="{{ $class_representant ?? '' }}">
                    {{ f_float($mbancarioCSV['ingreso_ammount']) ?? ''}}
                </td>

                <td class="{{ $class_representant ?? '' }}">

                    @foreach ($errors as $error)
                        @include('administracion.mbancarios.table.carga_csv.errors.error')
                    @endforeach

                </td>

                <td class=" text-wrap {{ $class_representant ?? '' }}">

                    @if ($errors->count()>0)
                        @php
                            $modal_id = "mostrar_errors_".$loop->iteration;
                            $count_error++;
                        @endphp
                        <button type="button" class="btn btn-danger btn-sm" role="button">
                            <span class="badge badge-light">{{ ($errors->count()) ?? '' }}</span>
                        </button>
                    @else
                        <button type="button" class="btn btn-success btn-sm" role="button">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </button>
                        @includeWhen($errors->isEmpty(),'administracion.mbancarios.table.carga_csv.fields.hidden')
                    @endif

                </td>

            </tr>

            @endforeach

    </tbody>

</table>

@if ($mbancariosCSV->count() > 0)

    <fieldset {{ ( $count_error >= $mbancariosCSV->count()) ? 'disabled':null  }} >
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


