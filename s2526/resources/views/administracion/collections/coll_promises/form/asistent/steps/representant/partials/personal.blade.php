@php
    $class = ($meet_index < 25) ?  'danger': null ;
    $class = ($meet_index >= 25 && $meet_index < 50) ?  'warning':$class ;
    $class = ($meet_index >= 50 && $meet_index < 75) ?  'info': $class;
    $class = ($meet_index >= 75 && $meet_index < 100 ) ?  'primary':$class ;
    $class = ($meet_index == 100 ) ?  'success':$class ;
@endphp

<div class="alert alert-{{ $class }} p-4 shadow ">
    <h4 class="px-4">Información personal del representante seleccionado.</h4>
    <div class="px-4 flex-center" style="min-height: 300px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-3">
                    <img class="card-img-top"
                    src="{{ (isset($estudiant->logo)) ? asset($representant->logo) : asset('images/avatar/user_default.png') }}"
                    alt="Card image cap">
                </div>
                <div class="col-sm-6 flex-center">
                    <div class="alert text-uppercase w-100 p-0 m-0">
                        <div>Nombre: <span class="font-weight-bold">{{$representant->name ?? ''}} {{$representant->lastname ?? ''}}</span> </div>
                        <div>Cédula: <span class="font-weight-bold">{{$representant->ci_representant ?? ''}}</span></div>
                        {{-- <div>Cantidad de Estudiantes: <span class="font-weight-bold">{{ ($estudiants->isNotEmpty()) ? $estudiants->count() : null }}</span></div> --}}
                        <div>Deuda: <span class="font-weight-bold text-danger"><span>Bs. {{f_float($ammount_expire_bill_exchange)}}</span> <span class=" text-dark">[{{ ($exchange_rate_current) ? 'TDC: Bs. '.f_float($exchange_rate_current->ammount) : 'STDC' }}]</span></div>
                        <div>Deuda Cambiaria: <span class="font-weight-bold  text-dark">$ {{f_float($exchange_ammount_expire_bill)}}</span></div>
                        <div class=" text-danger">Índice de Morosidad: <span class="font-weight-bold">{{$late_index ?? '' }} %</span></div>
                        <div class=" text-primary">Índice de Cumplimiento de Pago: <span class="font-weight-bold">{{$meet_index ?? '' }} %</span></div>
                        <hr>
                        <div>
                            <div style="text-align: justify; padding-left: 1rem; padding-botton: 1rem;margin-bottom: 1rem">
                                <span style="margin-bottom: 0rem">Representante de:</span>
                                <div style="padding-left: 1rem; margin-top: 0rem">
                                    @foreach ($estudiants as $estudiant)
                                        <div style="font-weight: bold">{{$estudiant->fullname}}</div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 flex-center">
                    <div class="alert alert-light shadow-sm p-0 m-0 " role="alert">
                        <div class="">
                            <div class="text-center"><i class="fa fa-info-circle fa-2x p-2 text-info" aria-hidden="true"></i></div>
                            <div class="p-2">
                                Pulsa el botón:
                                <span class="badge badge-info d-block py-2 my-2">Siguinte &#10148</span>
                                para ver los datos telefónicos del representante.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
