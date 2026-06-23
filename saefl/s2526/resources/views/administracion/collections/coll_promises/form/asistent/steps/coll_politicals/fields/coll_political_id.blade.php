@php
    $stepShow = [
        'up'=>'step-representant-personal',
        'dowm'=>'step-start',
    ] ;
    $stepId = "step-coll_promises-coll_political_id";
@endphp

<div class="first-of-type" id="{{$stepId}}">
    <div class="alert alert-secondary rounded flex-center px-4 py-2"  style="min-height: 25rem;">
        <div>
            <h4>Seleccione la política de cobro a la cual será asociada la promesa de pago</h4>
            <div class="form-group">
                <label for="coll_political_id" class="font-weight-bold text-secondary m-0">{{$list_comment['coll_political_id'] ?? ''}}</label>
                {!! Form::select('coll_political_id',$list_coll_politicals,old('coll_political_id'),['class'=>'form-control','placeholder'=>'Seleccione']);!!}
                @error('coll_political_id')<span class="text-danger small">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <div class="step_form py-2 px-1 font-weight-bold">
        <input type="button" class="clic-step btn btn-light w-25 p-2" value="Anterior" data-step-show="{{$stepShow['dowm'] ?? ''}}" data-step-hide="{{$stepId ?? ''}}" data-direction="down" />
        <input type="button" class="clic-step btn btn-info w-75 p-2 float-right btn-load-representant" value="Siguinte &#10148" data-step-show="{{$stepShow['up'] ?? ''}}" data-step-hide="{{$stepId ?? ''}}" data-direction="up" />
    </div>
</div>


@section('scripts')
    @parent
    <script>

        $(document).ready(function() {
        //ini del evento clic
        $('.btn-load-representant').click(function (e) {
            e.preventDefault();
            var container = '#continer_representant';  //console.log(container);
            var form = $('#form-collPromise-create'); //console.log(form); //console.log(form.attr('action'));
            var data = form.serialize(); //console.log(data);
            var url = "{{ route('administracion.collections.coll_promises.load.representant') }}"; //console.log(url);
            $.post(url, data, function (result){
                $(container).html(result);
            }).fail(function (result) {
                var error_msg = '';
                $.each(result.responseJSON.errors,function(index,valor){
                    error_msg += valor+', ';
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
@endsection
