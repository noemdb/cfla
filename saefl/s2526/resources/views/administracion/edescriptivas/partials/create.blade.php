<div class="container">
    <div class="row">

        <div class="col-8" data-id="{{ $estudiant->id }}">

            {!! Form::open([
                'route' => 'administracion.edescriptivas.store',
                'method' => 'POST',
                'id' => 'form-create-' . $estudiant->id,
                'class' => 'form-signin',
            ]) !!}

            @include('administracion.edescriptivas.form.fields')

            {!! Form::submit('Registrar', ['class' => 'btn-create btn btn-primary btn-block', 'id' => 'create']) !!}

            {!! Form::close() !!}

        </div>

        <div class="col-4">
            @php $edescriptivas = $estudiant->edescriptivas; @endphp
            @include('administracion.edescriptivas.partials.resume')
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        //ini del evento clic
        $('.btn-create').click(function(e) {
            e.preventDefault();
            var row = $(this).parents('div'); //console.log(row);  //fila contentiva de la data
            var id = row.data('id'); //console.log(id); //console.log(id);
            var modal = '#modal_create_edescriptivas_' + id; //console.log(container);
            var form = $('#form-create-' + id); //console.log(form); //console.log(form.attr('action'));
            var data = form.serialize(); //console.log(data);
            var url = "{{ route('administracion.edescriptivas.store') }}"; //console.log(url);
            $.post(url, data, function(result) {
                $('#span_count_' + id).html(result.count);
                Swal.fire({
                    title: result.messenge,
                    icon: 'success',
                    text: 'Cantidad de evaluaciones descriptivas registradas: ' + result
                        .count
                });
                $(modal).modal('toggle');
            }).fail(function(result) {
                var error_msg = '';
                $.each(result.responseJSON.errors, function(index, valor) {
                    error_msg += valor + ', ';
                });
                Swal.fire({
                    title: 'ERROR',
                    type: 'error',
                    text: error_msg
                });
            });
        });
        //fin del evento clic
    });
</script>
