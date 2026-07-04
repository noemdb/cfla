@php
    $class_N = 'd-none d-sm-table-cell';
    $class_representant = '';
    $class_fecha = 'd-none d-md-table-cell';
    $class_banco = 'd-none d-md-table-cell';
    $class_monto = 'd-none d-lg-table-cell';
    $class_action = 'nosort text-center';
@endphp

{{-- {!! Form::open(['route'=>'administracion.prepagos.update','method'=>'POST','id'=>'form-aprobe','class'=>'form-aprobe pb-2', 'role'=>'form-signin']) !!} --}}

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    {{-- table-hover  --}}

    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_representant }}">Representante</th>
            <th class="{{ $class_fecha }}" title="Fecha Operación">F. Oper.</th>
            <th class="{{ $class_banco }} text-nowrap">Banco</th>
            <th class="{{ $class_monto }} text-nowrap">Referencia</th>
            <th class="{{ $class_monto }} text-nowrap">Monto Bs.</th>
            <th class="{{ $class_monto }} text-wrap">Observaciones</th>
            <th class="{{ $class_monto }} text-wrap">Comentario</th>
            <th class="{{ $class_monto }} text-nowrap text-center">Insidencias</th>
            <th class="{{ $class_monto }} text-nowrap text-center">Aprobación</th>
            <th class="{{ $class_monto }} text-nowrap">Estado</th>
            <th class="{{ $class_action }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach ($prepagos as $prepago)

            @php
                $representant = $prepago->representant;
                $status_approved = $prepago->status_approved;
                $status_apply = $prepago->status_apply;
                $readonly = $status_apply == 'true' ? 'readonly' : null;
            @endphp

            <tr data-id="{{ $prepago->id ?? '' }}" id="tr_{{ $prepago->id ?? '' }}"
                class="table-{{ $status_apply == 'true' ? 'secondary text-muted' : null }}"
                id="row_prepago_{{ $prepago->id }}">

                {!! Form::model($prepago, [
                    'route' => ['administracion.prepagos.update', $prepago->id],
                    'method' => 'PUT',
                    'id' => 'form_aprobe_' . $prepago->id,
                    'role' => 'form',
                ]) !!}

                <td id="td-id" class="{{ $class_N }}">
                    {{ $loop->iteration }}
                    {{ Form::hidden('prepago_id', $prepago->id, ['id' => 'prepago_id']) }}
                </td>

                <td class="{{ $class_representant ?? '' }}">

                    @if ($representant)
                        <b>{{ $representant->name ?? '' }}</b>
                        <br>
                        <span class="text-muted">
                            ({{ $representant->ci_representant ?? '' }})
                        </span>
                    @else
                        {!! Form::text('ci_representant', old('ci_representant'), [
                            $readonly,
                            'size' => '10',
                            'placeholder' => 'CI Representante',
                        ]) !!}
                    @endif

                </td>

                <td class="{{ $class_fecha ?? '' }} text-nowrap">
                    {!! Form::date('date_transaction', $prepago->date_transaction, [
                        $readonly,
                        'size' => '10',
                        'id' => 'date_transaction',
                    ]) !!}
                </td>

                <td class="{{ $class_monto ?? '' }} text-nowrap">
                    {!! Form::select('banco_id', $list_banco, $prepago->banco_id, [
                        $readonly,
                        'id' => 'banco_id',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </td>

                <td class="font-weight-bold {{ $class_monto ?? '' }} text-nowrap"
                    title="{{ $prepago->FullMbancario }}">
                    {{ $prepago->number_i_pay ?? '' }}
                </td>

                <td class="{{ $class_monto ?? '' }} text-nowrap">
                    {!! Form::text('ingreso_ammount', $prepago->ingreso_ammount, [
                        $readonly,
                        'size' => '10',
                        'placeholder' => 'Monto del Pago',
                        'id' => 'ingreso_ammount',
                    ]) !!}
                </td>

                <td class="{{ $class_monto ?? '' }} text-wrap">
                    {!! Form::text('ingreso_observations', $prepago->ingreso_observations, [
                        'size' => '10',
                        'placeholder' => 'Observaciones',
                        'id' => 'ingreso_observations',
                    ]) !!}
                </td>
                <td class="{{ $class_monto ?? '' }} text-wrap">
                    {{ $prepago->comment ?? '' }}
                    {{ Form::hidden('comment', $prepago->comment, ['id' => 'comment']) }}
                </td>

                <td class="{{ $class_monto ?? '' }} text-nowrap text-center">
                    <div class="btn-group-vertical" role="group" aria-label="Basic example"
                        id="divErrors_{{ $prepago->id ?? '' }}">


                        @php $status_error = false @endphp
                        @if ($prepago->error_exist)
                            <span class="btn btn-sm badge badge-danger" title="Número de referencia no encontrado">REF.
                                N.E</span>
                            @php $status_error=true; @endphp
                        @else
                            @if ($prepago->error_apply)
                                <span class="btn btn-sm badge badge-info" title="Referencia ya usada">REF. US.</span>
                                @php $status_error=true; @endphp
                            @else
                                @if ($prepago->error_date)
                                    <span class="btn btn-sm badge badge-warning"
                                        title="No coincide la fecha ingresada">FECHA N.C</span>
                                    @php $status_error=true; @endphp
                                @endif
                                @if ($prepago->error_ammount)
                                    <span class="btn btn-sm badge badge-secondary"
                                        title="No coincide el monto ingresado">MONTO N.C</span>
                                    @php $status_error=true; @endphp
                                @endif
                                @if ($prepago->error_bank)
                                    <span class="btn btn-sm badge badge-dark"
                                        title="No coincide el banco ingresado">BANCO N.C</span>
                                    @php $status_error=true; @endphp
                                @endif
                            @endif
                        @endif
                        @empty($prepago->representant)
                            <span class="btn btn-sm badge badge-danger" title="Representante no encotrado">RNE</span>
                            @php $status_error=true; @endphp
                        @endempty

                    </div>

                </td>

                <td class="{{ $class_monto ?? '' }} text-nowrap text-center">
                    {!! Form::select('status_approved', ['true' => 'APROBADA', 'false' => 'RECHAZADA'], $prepago->status_approved, [
                        $readonly,
                        'id' => 'status_approved',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </td>

                <td class="{{ $class_monto ?? '' }} text-nowrap text-center">
                    <span class="badge badge-{{ $status_apply == 'true' ? 'success' : 'warning' }}">
                        {{ $status_apply == 'true' ? 'APLICADA' : 'NO APLICADA' }}
                    </span>
                </td>

                <td class="{{ $class_action ?? '' }}">

                    <div class="btn-group btn-group-sm">

                        @php $disabled = ( $status_apply=="true" ) ? 'disabled=disabled': null ; @endphp

                        <fieldset id="fieldset_btn_{{ $prepago->id ?? '' }}" {{ $disabled ?? '' }}>

                            <button title="Actualizar notificación" type="button"
                                class="btn-aprobe btn btn-success btn-sm {{ $status_apply == 'true' ? 'disabled' : null }}">
                                <i class="fa fa-check-circle" aria-hidden="true"></i>
                            </button>

                        </fieldset>

                    </div>

                </td>

                {!! Form::close() !!}

            </tr>

        @endforeach

    </tbody>
</table>

{{-- partials contentivo de los scripts datatables (default,simple,simple_search) --}}
@include('administracion.datatables.default')

@section('scripts')
    @parent

    <script type="text/javascript">
        //ini del evento clic
        $('.btn-aprobe').click(function(e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id'); //console.log(id);
            var form = $('#form_aprobe_' + id); //console.log(form);
            var data = form.serialize(); //console.log(data);
            var url = form.attr('action'); //console.log(url);
            $.post(url, data, function(result) {
                if (id) {
                    $('#fieldset_btn_' + id).attr('disabled', 'disabled');
                    $('#tr_' + id).addClass('table-secondary');
                    $('#badge_aprobe_' + id).removeClass('badge-info').addClass('badge-success').html(
                        'APROBADA');
                    $('#divErrors_' + id).html('');
                    Swal.fire({
                        titleText: 'Resultado',
                        html: '<h5>' + result.messenge + '</h5>',
                        showConfirmButton: false,
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true
                    });
                } else {
                    Swal.fire({
                        titleText: 'Resultado',
                        html: '<h5>' + result.messenge + '</h5>',
                        icon: 'success'
                    });
                }

            }).fail(function(result) {
                Swal.fire({
                    title: 'ERROR',
                    icon: 'error'
                });
            });
        });
        //fin del evento clic
    </script>
@endsection
