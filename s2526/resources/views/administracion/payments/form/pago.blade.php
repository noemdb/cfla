{!!Form::open(['route'=>'administracion.prepagos.pago.store','method'=>'POST','id'=>'form-prepagos-create','class'=>'form-signin','id'=>'form-pago-store-'.$prepago->id])!!}

    @php $representant = $prepago->representant; @endphp

    {{Form::hidden('representant_id',$representant->id)}}
    {{Form::hidden('estudiant_id',$representant->estudiants->first()->id)}}

    <div class="card">
        {{-- <div class="card-header pb-0 mb-0">
            <h5 class="card-title">
                <i class="{{ $icon_menus['abonos'] }} fa-1x text-success "></i>
                Datos para el registro de pago
            </h5>
        </div> --}}
        <div class="card-body p-1 m-1">

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
                        @include('administracion.prepagos.form.fields.pago')
                    </div>
                </div>

                <div class="col-4">
                    <div class=" border h-100 rounded p-2">
                        @includeIf('administracion.prepagos.form.pago.abn_caf_desc')
                    </div>
                </div>

                <div class="col-4">
                    <div class=" border h-100 rounded p-2">
                        @include('administracion.prepagos.form.pago.cuentasxpagars')
                    </div>
                </div>

            </div>

            <fieldset {{ ($representant_saldo) ? null : 'disabled=disabled' }} >

                <button type="submit" class="btn btn-primary btn-block btn-pago-store" data-id="{{$prepago->id}}">
                    <i class="far fa-save"></i>
                    Registrar
                </button>
        
            </fieldset>

        </div>
    </div>

    

{!! Form::close() !!}

<script type="text/javascript">
    //ini del evento clic
        $('.btn-pago-store').click(function (e) {
            e.preventDefault();
            var id = $(this).data('id'); console.log(id); console.log(id)
            var modal = '#modal_pago_'+id;  console.log(modal);
            var row = '#row_prepago_'+id;  console.log(row);
            var form = $('#form-pago-store-'+id); //console.log(form.attr('action'));
            var data = form.serialize(); //console.log(data);
            var url = "{{ route('administracion.prepagos.pago.store') }}"; //console.log(url);
            $.post(url, data, function (result){
                if (id) {
                    $(row).fadeOut(500);
                    $(modal).modal('toggle');
                    Swal.fire({
                        titleText: 'Resultado',
                        html: '<h5>'+result.messenge+'</h5>',
                        showConfirmButton: false,
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true
                    });

                } else {
                    Swal.fire({
                        titleText: 'Resultado',
                        html: '<h5>'+result.messenge+'</h5>',
                        icon: 'success'
                    });
                    }

            }).fail(function (result) {
                Swal.fire({
                    title: 'Error inesperado - Consulte al administrador del sistema',
                    icon: 'error'
                });
            });
    });
     //fin del evento clic
</script>
