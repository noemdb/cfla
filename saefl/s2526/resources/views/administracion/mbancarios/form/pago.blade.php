{!!Form::open(['route'=>'administracion.mbancarios.pago.store','method'=>'POST','id'=>'form-mbancarios-create','class'=>'form-signin'])!!}

    @php $representant = $prepago->representant; @endphp

    {{Form::hidden('representant_id',$representant->id)}}
    {{Form::hidden('estudiant_id',$representant->estudiants->first()->id)}}

    <div class="card">
        <div class="card-header pb-0 mb-0">
            <h5 class="card-title">
                <i class="{{ $icon_menus['abonos'] }} fa-1x text-success "></i>
                Datos para el registro de pago
            </h5>
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-12">
                    <div class="alert alert-secondary font-weight-bolder">

                        @php $representant_saldo = $representant->ammount_expire_bill @endphp
                        @if ($representant_saldo > 0)
                            <span class="badge badge-danger float-right">
                                Bs. {{ f_float($representant_saldo) ?? ''}}
                            </span>
                        @else

                            <span class="badge badge-success float-right" title="SOLVENTE">
                                {{-- <i class="{{ $icon_menus['check'] }} fa-1x"></i> --}}
                                SOLVENTE
                            </span>
                        @endif

                        <i class="{{ $icon_menus['representante'] ?? '' }} fa-1x text-dark "></i>
                        {{ $representant->name ?? ''}}
                        <span class="small"> [{{ $representant->ci_representant ?? ''}}] </span>

                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-4">
                    <div class=" border h-100 rounded p-2">
                        @include('administracion.mbancarios.form.fields.pago')
                    </div>
                </div>

                <div class="col-4">
                    <div class=" border h-100 rounded p-2">
                        @includeIf('administracion.mbancarios.form.pago.abn_caf_desc')
                    </div>
                </div>

                <div class="col-4">
                    <div class=" border h-100 rounded p-2">
                        @include('administracion.mbancarios.form.pago.cuentasxpagars')
                    </div>
                </div>

            </div>

        </div>
    </div>

    <fieldset {{ ($representant_saldo) ? null : 'disabled=disabled' }} >

        <button type="submit" class="btn btn-primary btn-block">
            <i class="far fa-save"></i>
            Registrar
        </button>

    </fieldset>

{!! Form::close() !!}
