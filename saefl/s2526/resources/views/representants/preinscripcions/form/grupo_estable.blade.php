<div class="card">
    <div class="card-header pb-0 mb-0">
        <h4 class="card-title">
            <i class="{{ $icon_menus['grupo_estables'] }} fa-1x text-info "></i>
            Datos
        </h4>
    </div>
    <div class="card-body">

        {!! Form::model($inscripcion,['route' => ['representants.preinscripcions.update', $inscripcion->id], 'method' => 'PUT', 'id'=>'form-update_'.$inscripcion->id, 'role'=>'form']) !!}
            @include('representants.preinscripcions.form.fields.grupo_estable')
            <button type="submit" class="btn-create btn btn-primary btn-block" data-id="{{ $inscripcion->id ?? ''}}">
                <i class="far fa-save"></i>
                Asignar/Actilizar
            </button>
        {!! Form::close() !!}

    </div>
</div>

<script type="text/javascript">
    //ini del evento clic
    $('.btn-create').click(function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var grupo_estable = '#grupo_estable_'+id;  //console.log(modal);
        var modal = '#modal_update_'+id;  //console.log(modal);

        var form = $('#form-update_'+id); //console.log(form.attr('action'));
        var data = form.serialize(); //console.log(data); //console.log(data);
        // var url = "{{ route('representants.evaluacions.store') }}"; console.log(url);
        var url = form.attr('action'); //console.log(url);
        $.post(url, data, function (result){
            $(grupo_estable).html(result.grupo_estable); //console.log(result.grupo_estable)
            Swal.fire({
                title: result.messenge,
                icon: 'success',
                timer: 2000
            });
            $(modal).modal('toggle');
        }).fail(function (result) {
            Swal.fire({
                    title: 'ERROR',
                    icon: 'error'
                });
        });
    });
    //fin del evento clic


</script>
