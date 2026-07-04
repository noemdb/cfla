    @php
            $ammount_expire_bill = round($estudiant->ammount_expire_bill,2);
            $exchange_ammount_expire_bill = round($estudiant->exchange_ammount_expire_bill,2);
            $ammount_expire_bill_exchange = ($exchange_rate_current) ? $exchange_rate_current->ammount * $exchange_ammount_expire_bill : null;
            $ammount_expire_bill_exchange = round($ammount_expire_bill_exchange,2);

            //unexpired paid
            $ammount_unexpired_bill_paid = round($estudiant->ammount_unexpired_bill_paid,2);
            $exchange_ammount_unexpired_bill_paid = round($estudiant->exchange_ammount_unexpired_bill_paid,2);
            //unexpired bill
            $ammount_unexpired_bill = round($estudiant->ammount_unexpired_bill,2);
            $exchange_ammount_unexpired_bill = round($estudiant->exchange_ammount_unexpired_bill,2);

            $descuentos = $estudiant->descuentos;
            $border_class = ($exchange_ammount_expire_bill>0) ? 'danger' : 'success' ;
            $border_class = "border border-".$border_class." rounded-bottom rounded-sm border-top-0 border-right-0 border-left-0";
            // $color = (!empty($estudiant->getInscripcion()->id)) ? $estudiant->getInscripcion()->seccion->grado->color : null ;
            $grado = $estudiant->grado;
            $pestudio = ($estudiant) ? $estudiant->pestudio : 'dark';
            $color = ($grado) ? $grado->color : 'dark';
            $class_callout =  "bd-callout bd-callout bd-callout-".$color;
            $representant = $estudiant->representant;
            $status_blacklist = ($representant->status_blacklist=="true" || $estudiant->status_blacklist=="true") ? true : false ;

            $messege_black_list = null;
            if ($status_blacklist=="true") {
                if (round($exchange_ammount_expire_bill,2) > 0) {
                    $messege_black_list = "Este estudainte o su representante tiene compromisos administrativos pendientes con la Institución";
                } else {
                    $messege_black_list = "Este estudainte o su representante tiene compromisos administrativos pendientes con la Institución";
                }
            }
    @endphp

    <div class="{{$class_callout ?? 'default'}} h-100 " id="card_estudiant_id_{{$estudiant->id ?? ''}}" data-id="{{$estudiant->id ?? ''}}">
        <div class="card h-100 {{$border_class ?? ''}} deck-card" style="max-width: 540px;">
        <div class="row no-gutters">

            <div class="col-md-10">
            <div class="card-body p-1">

                <small class="align-text-bottom text-mute">
                <p class="align-text-top {{ ($status_blacklist=="true") ? 'alert alert-dark text-danger':null}}" title="{{ $messege_black_list ?? null }}">
                    @if ($status_blacklist=="true") <span class="badge badge-dark font-weight-bold mr-2 p-2 float-right" title="{{ $messege_black_list ?? null }}"> BL </span>  @endif
                    <span class="d-block">ID: {{$estudiant->id ?? ''}}</span>
                    <span class="font-weight-bold"> {{$estudiant->fullname}}</span><br>
                    <span>CI: {{$estudiant->ci_estudiant}}</span><br>
                    <span class="text-dark font-weight-bold">
                        {{$estudiant->getInscripcion()->seccion->grado->name ?? ''}} {{$estudiant->getInscripcion()->seccion->name ?? ''}}
                        <small class="font-weight-bold">{{ ($pestudio) ? '['.$pestudio->code.']': null ;}}</small>
                    </span>
                    <br>

                    @if ($status_blacklist=="true") <span class=" font-weight-bold">{{ $messege_black_list ?? null }}</span> @endif

                    @admon @if ($descuentos) <span class="badge badge-success font-weight-bold mr-2 p-2 float-right" title="Actualmente con un plan benéfico"> PB </span>  @endif  @endadmon
                    @if (!empty($estudiant->retiro->id)) <span class=" d-block">Retiro (Administrativo/Académico) {{$estudiant->created_ap ?? ''}}</span> @endif

                    <hr class="py-1 my-1">

                </p>
                </small>
                @admon

                    @php
                        $representant = $estudiant->representant;

                        $total_credito = round($representant->total_credito ,2);
                        $total_credito_exchange = round($representant->total_credito_exchange ,2);

                        $total_abono = round($representant->total_abono ,2);
                        $total_abono_exchange = round($representant->total_abono_exchange ,2);

                        $saldo_a_favor = round($total_credito + $total_abono,2);
                        $saldo_a_favor_exchange = round($total_credito_exchange + $total_abono_exchange,2);
                    @endphp

                    <div class="dropdown-divider mb-0 pb-0 mb-0"></div>

                    <div class="d-block">

                        @if ($total_credito_exchange > 0)
                            <span title="Monto total de los créditos a favor disponibles" class="badge badge-info my-1 py-1 shadow-sm">Bs. {{f_float($total_credito) }} | $ {{ f_float($total_credito_exchange)}}</span>
                        @endif
                        @if ($total_abono_exchange > 0)
                            <span title="Monto total de los abonos disponibles" class="badge badge-secondary my-1 py-1 shadow-sm">Bs. {{f_float($total_abono) }} | $ {{ f_float($total_abono_exchange)}}</span>
                        @endif
                        <div class="dropdown-divider mb-0 pb-0 mb-0"></div>
                        @if ($saldo_a_favor_exchange>0)
                            <span title="Saldo a favor disponibles" class="badge badge-light my-1 py-1 shadow-sm">Bs. {{f_float($saldo_a_favor) }} | $ {{ f_float($saldo_a_favor_exchange)}}</span>
                        @endif

                        @admin
                            <div class="dropdown-divider mb-0 pb-0 mb-0"></div>
                            @if ($exchange_ammount_unexpired_bill_paid > 0)
                                <span class="border border-warning rounded badge table-warning" title="Monto total cancelado por Pagos Adelantados">
                                    Bs. {{f_float($ammount_unexpired_bill_paid) ?? ''}} | $ {{ f_float($exchange_ammount_unexpired_bill_paid)}}
                                </span>
                            @endif
                        @endadmin

                    </div>
                @endadmon

            </div>
            </div>

            <div class="col-md-2 pr-1 pt-1 pb-1">
                @include('administracion.estudiants.deck.button.estudiant')
                <button type="button" class="btn btn-sm btn-dark mt-1" data-toggle="modal" data-target="#qrModal{{$estudiant->id}}" title="Ver QR Boletín">
                    <i class="fas fa-qrcode"></i>
                </button>
            </div>

        </div>

        @include('administracion.estudiants.deck.card.partial.footer')

        </div>
    </div>

    <!-- Modal QR Boletín -->
    <div class="modal fade" id="qrModal{{$estudiant->id}}" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel{{$estudiant->id}}" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrModalLabel{{$estudiant->id}}">QR Informe de Notas - {{$estudiant->fullname}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if ($estudiant->boletinPdfUrl())
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            @php $pescolar =  App\Models\app\Pescolar::first(); $ffinal = ($pescolar) ? $pescolar->ffinal : null;@endphp
                            <p class="text-center mb-3">
                                Descargue el Informe de Notas. <strong>Válido hasta el {{ f_date($ffinal) }}</strong>
                            </p>
                            <div class="d-flex justify-content-center align-items-center">
                                {!! DNS2D::getBarcodeHTML($estudiant->boletinPdfUrl(), 'QRCODE', 8, 8) !!}
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
