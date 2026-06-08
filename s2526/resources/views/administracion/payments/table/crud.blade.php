@php
    $class_N="d-none d-sm-table-cell";
    $class_ci_representant="";
    $class_name_representant="";
    $class_phone="d-none d-lg-table-cell";
    $class_type_pay="d-none d-lg-table-cell text-nowrap";
    $class_comment="d-none d-lg-table-cell";
    $class_nestudiants="";
    $class_ntransferencias="";

    $class_ci_estudiant_1="d-none d-xl-table-cell table-primary";
    $class_name_estudiant_1="d-none d-xl-table-cell table-primary";
    $class_grado_estudiant_1="d-none d-xl-table-cell table-primary text-nowrap";

    $class_ci_estudiant_2="d-none d-xl-table-cell table-success";
    $class_name_estudiant_2="d-none d-xl-table-cell table-success text-nowrap";
    $class_grado_estudiant_2="d-none d-xl-table-cell table-success";

    $class_ci_estudiant_3="d-none d-xl-table-cell table-info";
    $class_name_estudiant_3="d-none d-xl-table-cell table-info text-nowrap";
    $class_grado_estudiant_3="d-none d-xl-table-cell table-info";

    $class_banco_emisor_1="d-none d-xl-table-cell table-warning text-nowrap";
    $class_phone_1="d-none d-xl-table-cell table-warning text-nowrap";
    $class_banco_id_1="d-none d-xl-table-cell table-warning text-nowrap";
    $class_method_pay_id_1="d-none d-xl-table-cell table-warning text-nowrap";
    $class_number_i_pay_1="d-none d-xl-table-cell table-warning";
    $class_date_transaction_1="d-none d-xl-table-cell table-warning text-nowrap";
    $class_ammount_1="d-none d-xl-table-cell table-warning text-nowrap";
    $class_observatios_1="d-none d-xl-table-cell table-warning";
    $class_image_1="";

    $class_banco_emisor_2="d-none d-xl-table-cell table-secondary text-nowrap";
    $class_phone_2="d-none d-xl-table-cell table-secondary text-nowrap";
    $class_banco_id_2="d-none d-xl-table-cell table-secondary text-nowrap";
    $class_method_pay_id_2="d-none d-xl-table-cell table-secondary text-nowrap";
    $class_number_i_pay_2="d-none d-xl-table-cell table-secondary";
    $class_date_transaction_2="d-none d-xl-table-cell table-secondary text-nowrap";
    $class_ammount_2="d-none d-xl-table-cell table-secondary text-nowrap";
    $class_observatios_2="d-none d-xl-table-cell table-secondary";
    $class_image_2="";

    $class_banco_emisor_3="d-none d-xl-table-cell table-primary text-nowrap";
    $class_phone_3="d-none d-xl-table-cell table-primary text-nowrap";
    $class_banco_id_3="d-none d-xl-table-cell table-primary text-nowrap";
    $class_method_pay_id_3="d-none d-xl-table-cell table-primary text-nowrap";
    $class_number_i_pay_3="d-none d-xl-table-cell table-primary";
    $class_date_transaction_3="d-none d-xl-table-cell table-primary text-nowrap";
    $class_ammount_3="d-none d-xl-table-cell table-primary text-nowrap";
    $class_observatios_3="d-none d-xl-table-cell table-primary";
    $class_image_3="";

    $class_banco_emisor_4="d-none d-xl-table-cell table-danger text-nowrap";
    $class_phone_4="d-none d-xl-table-cell table-danger text-nowrap";
    $class_banco_id_4="d-none d-xl-table-cell table-danger text-nowrap";
    $class_method_pay_id_4="d-none d-xl-table-cell table-danger text-nowrap";
    $class_number_i_pay_4="d-none d-xl-table-cell table-danger";
    $class_date_transaction_4="d-none d-xl-table-cell table-danger text-nowrap";
    $class_ammount_4="d-none d-xl-table-cell table-danger text-nowrap";
    $class_observatios_4="d-none d-xl-table-cell table-danger";
    $class_image_4="d-none d-xl-table-cell table-danger";
    $class_created_at="d-none d-xl-table-cell";

    $class_action="";
@endphp

    {{--
        'ci_representant',
        'name_representant',
        'phone',
        'type_pay',
        'comment',

        'ci_estudiant_1',
        'name_estudiant_1',
        'grado_estudiant_1',

        'ci_estudiant_2',
        'name_estudiant_2',
        'grado_estudiant_2',

        'ci_estudiant_3',
        'name_estudiant_3',
        'grado_estudiant_3',

        'ci_estudiant_4',
        'name_estudiant_4',
        'grado_estudiant_4',

        'banco_emisor_1',
        'banco_id_1',
        'method_pay_id_id',
        'number_i_pay_1',
        'date_transaction_1',
        'ammount_1',
        'observatios_1',
        'image_1',

        'banco_emisor_2',
        'banco_id_2',
        'method_pay_id_2',
        'number_i_pay_2',
        'date_transaction_2',
        'ammount_2',
        'observatios_2',
        'image_2',

        'banco_emisor_3',
        'banco_id_3',
        'method_pay_id_i3',
        'number_i_pay_3',
        'date_transaction_3',
        'ammount_3',
        'observatios_3',
        'image_3',

        'banco_emisor_4',
        'banco_id_4',
        'method_pay_id_4',
        'number_i_pay_4',
        'date_transaction_4',
        'ammount_4',
        'observatios_4',
        'image_4',
    --}}

<table width="100%" class="table table-striped table-hover table-sm small py-1" id="table-data-default" >

    <thead>
        <tr>
            <th class="{{ $class_N ?? ''}}">N</th>
            <th class="{{ $class_N ?? ''}}">Estado</th>
            <th class="{{ $class_created_at ?? '' }}">Registrado</th>
            <th class="{{ $class_ci_representant ?? ''}}">CI</th>
            <th class="{{ $class_name_representant ?? ''}}">Representante</th>
            <th class="{{ $class_phone ?? ''}}">Telefono</th>
            <th class="{{ $class_type_pay ?? ''}}">Tipo Pago</th>
            <th class="{{ $class_comment ?? ''}}">Comentario</th>
            {{-- <th class="{{ $class_name_estudiant_1 ?? ''}}">Estudiante1</th>
            <th class="{{ $class_grado_estudiant_1 ?? ''}}">Grado/Sección</th>
            <th class="{{ $class_name_estudiant_2 ?? ''}}">Estudiante2</th>
            <th class="{{ $class_grado_estudiant_2 ?? ''}}">Grado/Sección</th>
            <th class="{{ $class_name_estudiant_3 ?? '' }}">Estudiante3</th>
            <th class="{{ $class_grado_estudiant_3 ?? '' }}">Grado/Sección</th> --}}

            <th class="{{ $class_banco_emisor_1 ?? '' }}">B.Emisor</th>
            <th class="{{ $class_banco_id_1 ?? '' }}">B.Receptor</th>
            <th class="{{ $class_method_pay_id_1 ?? '' }}">M.Pago</th>
            <th class="{{ $class_number_i_pay_1 ?? '' }}">Referencia</th>
            <th class="{{ $class_phone_1 ?? '' }}">Tlf.Pago Movíl</th>
            <th class="{{ $class_date_transaction_1 ?? '' }}">Fecha</th>
            <th class="{{ $class_ammount_1 ?? '' }}">Monto</th>
            <th class="{{ $class_ammount_1 ?? '' }}">M.Cambiario</th>
            <th class="{{ $class_observatios_1 ?? '' }}">Obs.</th>
            <th class="{{ $class_image_1 ?? '' }}">Imagen</th>

            <th class="{{ $class_banco_emisor_2 ?? '' }}">B.Emisor</th>
            <th class="{{ $class_banco_id_2 ?? '' }}">B.Receptor</th>
            <th class="{{ $class_method_pay_id_2 ?? '' }}">M.Pago</th>
            <th class="{{ $class_number_i_pay_2 ?? '' }}">Referencia</th>
            <th class="{{ $class_phone_2 ?? '' }}">Tlf.Pago Movíl</th>
            <th class="{{ $class_date_transaction_2 ?? '' }}">Fecha</th>
            <th class="{{ $class_ammount_2 ?? '' }}">Monto</th>
            <th class="{{ $class_ammount_2 ?? '' }}">M.Cambiario</th>
            <th class="{{ $class_observatios_2 ?? '' }}">Obs.</th>
            <th class="{{ $class_image_2 ?? '' }}">Imagen</th>

            {{-- <th class="{{ $class_banco_emisor_3 ?? '' }}">B.Emisor</th>
            <th class="{{ $class_banco_id_3 ?? '' }}">B.Receptor</th>
            <th class="{{ $class_method_pay_id_3 ?? '' }}">M.Pago</th>
            <th class="{{ $class_number_i_pay_3 ?? '' }}">Referencia</th>
            <th class="{{ $class_date_transaction_3 ?? '' }}">Fecha</th>
            <th class="{{ $class_ammount_3 ?? '' }}">Monto</th>
            <th class="{{ $class_observatios_3 ?? '' }}">Obs.</th>
            <th class="{{ $class_image_3 ?? '' }}">Imagen</th>

            <th class="{{ $class_banco_emisor_4 ?? '' }}">B.Emisor</th>
            <th class="{{ $class_banco_id_4 ?? '' }}">B.Receptor</th>
            <th class="{{ $class_method_pay_id_4 ?? '' }}">M.Pago</th>
            <th class="{{ $class_number_i_pay_4 ?? '' }}">Referencia</th>
            <th class="{{ $class_date_transaction_4 ?? '' }}">Fecha</th>
            <th class="{{ $class_ammount_4 ?? '' }}">Monto</th>
            <th class="{{ $class_observatios_4 ?? '' }}">Obs.</th>
            <th class="{{ $class_image_4 ?? '' }}">Imagen</th> --}}

        </tr>
    </thead>

    <tbody id="tdatos">
        @forelse($payments as $payment)

            @php $representant_id = ($payment->representant) ? $payment->representant->id : null; @endphp
            @php $exchange_ammount_1 = ($exchange_rate_current) ? $payment->ammount_1 / $exchange_rate_current->ammount : null; @endphp
            @php $exchange_ammount_2 = ($exchange_rate_current) ? $payment->ammount_2 / $exchange_rate_current->ammount : null; @endphp

            <tr data-id="{{$payment->id ?? ''}}" class="">

                <td class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_N }}">
                    @if ($payment->number_i_pay_1 && $payment->status_apply_t1)
                        <i class="fa fa-check-circle fa-1x text-success btn btn-sm p2" aria-hidden="true" title="Transferencia 1 asociada a un registro de pago"></i>
                    @else
                        @php
                            $request = [
                                'id'=>$representant_id,
                                'banco_id'=>$payment->banco_id_1,
                                'method_pay_id'=>$payment->method_pay_id_1,
                                'number_i_pay'=>$payment->number_i_pay_1,
                                'date_payment'=>$payment->date_transaction_1,
                                'date_transaction'=>$payment->date_transaction_1,
                                'ingreso_ammount'=>$payment->ammount_1,
                            ]
                        @endphp
                        <a class="btn btn-warning btn-sm shadow-sm" href="{{ route('administracion.registropagos.asistent.representant.create',$request) }}" role="button" title="Transferencia 1 NO asociada a un registro de pago">
                            <i class="fa fa-circle fa-1x text-dark" aria-hidden="true" title="Transferencia 1 NO asociada a un registro de pago"></i>
                        </a>
                    @endif

                    @if ($payment->number_i_pay_2)
                        @if ($payment->status_apply_t2)
                            <i class="fa fa-check-circle fa-1x text-success btn btn-sm p2" aria-hidden="true" title="Transferencia 2 asociada a un registro de pago"></i>
                        @else
                            @php
                                $request = [
                                    'id'=>$representant_id,
                                    'banco_id'=>$payment->banco_id_1,
                                    'method_pay_id'=>$payment->method_pay_id_2,
                                    'number_i_pay'=>$payment->number_i_pay_2,
                                    'date_payment'=>$payment->date_transaction_2,
                                    'date_transaction'=>$payment->date_transaction_2,
                                    'ingreso_ammount'=>$payment->ammount_2,
                                ]
                            @endphp
                            <a class="btn btn-warning btn-sm shadow-sm" href="{{ route('administracion.registropagos.asistent.representant.create',$request) }}" role="button" title="Transferencia 2 NO asociada a un registro de pago">
                                <i class="fa fa-circle fa-1x" aria-hidden="true" ></i>
                            </a>
                        @endif
                    @endif
                </td>

                <td class="{{ $class_created_at  ?? ''}}">
                    {{ $payment->created_at->format('d-m-Y') ?? null}}
                </td>

                <td class="{{ $class_ci_representant  ?? ''}}">
                    {{ $payment->ci_representant ?? null}}
                </td>
                <td class="{{ $class_name_representant  ?? ''}}">
                    {{ $payment->name_representant ?? null}}
                </td>
                <td class="{{ $class_phone  ?? ''}}">
                    {{ $payment->phone ?? null}}</span>
                </td>
                <td class="{{ $class_type_pay  ?? ''}}">
                    {{ $payment->type_pay ?? null}}
                </td>
                <td class="{{ $class_comment  ?? ''}}">
                    {{ $payment->comment ?? null}}
                </td>
                {{-- <td class="{{ $class_name_estudiant_1  ?? ''}}">
                    {{ $payment->name_estudiant_1 ?? null}}
                </td>
                <td class="{{ $class_name_estudiant_1  ?? ''}}">
                    {{ $payment->grado_estudiant_1 ?? null}}
                </td>
                <td class="{{ $class_name_estudiant_2  ?? ''}}">
                    {{ $payment->name_estudiant_2 ?? null}}
                </td>
                <td class="{{ $class_name_estudiant_2  ?? ''}}">
                    {{ $payment->grado_estudiant_2 ?? null}}
                </td>
                <td class="{{ $class_name_estudiant_3  ?? ''}}">
                    {{ $payment->name_estudiant_3 ?? null}}
                </td>
                <td class="{{ $class_name_estudiant_3  ?? ''}}">
                    {{ $payment->grado_estudiant_3 ?? null}}
                </td> --}}

                <td class="{{ $class_banco_emisor_1  ?? ''}}">
                    {{ $payment->banco_emisor_1 ?? null}}
                </td>
                <td class="{{ $class_banco_id_1  ?? ''}}">
                    @php $banco_id = $payment->banco_id_1; @endphp
                    {{ $payment->getBanco($banco_id)->name ?? null}}
                </td>
                <td class="{{ $class_method_pay_id_1  ?? ''}}">
                    @php $method_pay_id = $payment->method_pay_id_1; @endphp
                    {{ $payment->getMethod($method_pay_id)->name ?? null}}
                </td>
                <td class="{{ $class_number_i_pay_1  ?? ''}}">
                    {{ $payment->number_i_pay_1 ?? null}}
                </td>
                <td class="{{ $class_phone_1  ?? ''}}">
                    {{ $payment->phone_1 ?? null}}
                </td>
                <td class="{{ $class_date_transaction_1  ?? ''}}">
                    {{ f_date($payment->date_transaction_1) ?? ''}}
                </td>
                <td class="{{ $class_ammount_1  ?? ''}}">
                    {{-- {{ ($payment->ammount_1) ? f_float($payment->ammount_1) : null }} --}}
                    {{ ($payment->ammount_1) ? round($payment->ammount_1,2) : null }}
                </td>
                <td class="{{ $class_ammount_1  ?? ''}}">
                    {{-- {{ ($exchange_ammount_1) ? f_float($exchange_ammount_1,2) : null }} --}}
                    {{ ($exchange_ammount_1) ? round($exchange_ammount_1,2) : null }}
                </td>
                <td class="{{ $class_observatios_1  ?? ''}}">
                    {{ $payment->observatios_1 ?? null}}
                </td>
                <td class="{{ $class_image_1  ?? ''}}">
                    @if ($payment->image_1)
                        @php $fileUrl = $payment->image_1 @endphp
                        @php $ext = strtoupper(pathinfo($fileUrl, PATHINFO_EXTENSION)); @endphp
                        @php $modal_id = 'modal_image_'.$payment->id.'_1' ; @endphp
                        @includeWhen($fileUrl, 'elements.modals.show'.$ext,['fileUrl'=>$fileUrl,'modal_id'=>$modal_id,'preview'=>false])
                        {{-- <a class="btn btn-light" href="{{ asset($payment->image_1) }}" role="button" target="_blank">
                            <i class="fa fa-file-image" aria-hidden="true"></i>
                        </a> --}}
                    @endif
                </td>

                <td class="{{ $class_banco_emisor_2  ?? ''}}">
                    {{ $payment->banco_emisor_2 ?? null}}
                </td>
                <td class="{{ $class_banco_id_2  ?? ''}}">
                    @php $banco_id = $payment->banco_id_2; @endphp
                    {{ $payment->getBanco($banco_id)->name ?? null}}
                </td>
                <td class="{{ $class_method_pay_id_2  ?? ''}}">
                    @php $method_pay_id = $payment->method_pay_id_2; @endphp
                    {{ $payment->getMethod($method_pay_id)->name ?? null}}
                </td>
                <td class="{{ $class_number_i_pay_2  ?? ''}}">
                    {{ $payment->number_i_pay_2 ?? null}}
                </td>
                <td class="{{ $class_phone_2  ?? ''}}">
                    {{ $payment->phone_2 ?? null}}
                </td>
                <td class="{{ $class_date_transaction_2  ?? ''}}">
                    {{ f_date($payment->date_transaction_2) ?? ''}}
                </td>
                <td class="{{ $class_ammount_2  ?? ''}}">
                    {{-- {{ ($payment->ammount_2) ? f_float($payment->ammount_2) : null }} --}}
                    {{ ($payment->ammount_2) ? round($payment->ammount_2,2) : null }}
                </td>
                <td class="{{ $class_ammount_2  ?? ''}}">
                    {{-- {{ ($payment->ammount_2) ? f_float($payment->ammount_2) : null }} --}}
                    {{ ($exchange_ammount_2) ? round($exchange_ammount_2,2) : null }}
                </td>
                <td class="{{ $class_observatios_2  ?? ''}}">
                    {{ $payment->observatios_2 ?? null}}
                </td>
                <td class="{{ $class_image_2  ?? ''}}">
                    @if ($payment->image_2)
                        @php $fileUrl = $payment->image_2 @endphp
                        @php $ext = strtoupper(pathinfo($fileUrl, PATHINFO_EXTENSION)); @endphp
                        @php $modal_id = 'modal_image_'.$payment->id.'_1' ; @endphp
                        @includeWhen($fileUrl, 'elements.modals.show'.$ext,['fileUrl'=>$fileUrl,'modal_id'=>$modal_id,'preview'=>false])
                    @endif
                </td>

                {{-- <td class="{{ $class_banco_emisor_3  ?? ''}}">
                    {{ $payment->banco_emisor_3 ?? null}}
                </td>
                <td class="{{ $class_banco_id_3  ?? ''}}">
                    @php $banco_id = $payment->banco_id_3; @endphp
                    {{ $payment->getBanco($banco_id)->name ?? null}}
                </td>
                <td class="{{ $class_method_pay_id_3  ?? ''}}">
                    @php $method_pay_id = $payment->method_pay_id_3; @endphp
                    {{ $payment->getMethod($method_pay_id)->name ?? null}}
                </td>
                <td class="{{ $class_number_i_pay_3  ?? ''}}">
                    {{ $payment->number_i_pay_3 ?? null}}
                </td>
                <td class="{{ $class_date_transaction_3  ?? ''}}">
                    {{ $payment->date_transaction_3 ?? null}}
                </td>
                <td class="{{ $class_ammount_3  ?? ''}}">
                    {{ ($payment->ammount_3) ? f_float($payment->ammount_3) : null }}
                </td>
                <td class="{{ $class_observatios_3  ?? ''}}">
                    {{ $payment->observatios_3 ?? null}}
                </td>
                <td class="{{ $class_image_3  ?? ''}}">
                    @if ($payment->image_3)
                        <a name="" id="" class="btn btn-light" href="{{ asset($payment->image_3) }}" role="button" target="_blank">
                            <i class="fa fa-file-image" aria-hidden="true"></i>
                        </a>
                    @endif
                </td>

                <td class="{{ $class_banco_emisor_4  ?? ''}}">
                    {{ $payment->banco_emisor_4 ?? null}}
                </td>
                <td class="{{ $class_banco_id_4  ?? ''}}">
                    @php $banco_id = $payment->banco_id_4; @endphp
                    {{ $payment->getBanco($banco_id)->name ?? null}}
                </td>
                <td class="{{ $class_method_pay_id_4  ?? ''}}">
                    @php $method_pay_id = $payment->method_pay_id_4; @endphp
                    {{ $payment->getMethod($method_pay_id)->name ?? null}}
                </td>
                <td class="{{ $class_number_i_pay_4  ?? ''}}">
                    {{ $payment->number_i_pay_4 ?? null}}
                </td>
                <td class="{{ $class_date_transaction_4  ?? ''}}">
                    {{ $payment->date_transaction_4 ?? null}}
                </td>
                <td class="{{ $class_ammount_4  ?? ''}}">
                    {{ ($payment->ammount_4) ? f_float($payment->ammount_4) : null }}
                </td>
                <td class="{{ $class_observatios_4  ?? ''}}">
                    {{ $payment->observatios_4 ?? null}}
                </td>
                <td class="{{ $class_image_4  ?? ''}}">
                    @if ($payment->image_4)
                        <a name="" id="" class="btn btn-light" href="{{ asset($payment->image_4) }}" role="button" target="_blank">
                            <i class="fa fa-file-image" aria-hidden="true"></i>
                        </a>
                    @endif
                </td> --}}


            </tr>

        @empty

            <tr> <td colspan="30" class=" small text-left pl-4 font-weight-bold">NO HAY DATOS</td> </tr>

        @endforelse

    </tbody>
</table>


{{-- partials contentivo de los scripts datatables --}}
{{-- @include('datatables.expotCDN') --}}
@include('administracion.datatables.exportBootstrap')
