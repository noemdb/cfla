<div class="card">
    {{-- <div class="card-header pb-0 mb-0">
        <h4 class="card-title">
            <i class="{{ $icon_menus['abonos'] }} fa-1x text-success "></i>
            Datos para el nuevo abono
        </h4>
    </div> --}}
    <div class="card-body p-1 m-1">
        <div class="row">

            <div class="col-7">
                {!!Form::open(['route'=>'administracion.prepagos.abono.store','method'=>'POST','id'=>'form-abono-store-'.$prepago->id,'class'=>'form-signin'])!!}
                    @include('administracion.prepagos.form.fields.abono')
                    <button type="submit" class="btn-abono-store btn btn-primary btn-block" data-id="{{$prepago->id}}">
                        <i class="far fa-save"></i>
                        Registrar
                    </button>
                {!! Form::close() !!}
            </div>

            <div class="col-5">
                @include('administracion.prepagos.show.representant.resume')
            </div>

        </div>

    </div>
</div>

<script type="text/javascript">
    //ini del evento clic
     $('.btn-abono-store').click(function (e) {
         e.preventDefault();
         var id = $(this).data('id'); console.log(id); console.log(id)
         var modal = '#modal_abono_'+id;  console.log(modal);
         var row = '#row_prepago_'+id;  console.log(row);
         var form = $('#form-abono-store-'+id); //console.log(form.attr('action'));
         var data = form.serialize(); //console.log(data);
         var url = "{{ route('administracion.prepagos.abono.store') }}"; //console.log(url);
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
