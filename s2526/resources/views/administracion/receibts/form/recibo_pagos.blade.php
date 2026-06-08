{{-- 'recibo_id', 'quota', 'exchange_ammount'  --}}

<div class="container-fluid">

    <div class='input-form' id="div-clone-pagos">
        @for ($i = 0; $i < $num_pagos; $i++)
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="quota"
                            class="font-weight-bold text-secondary m-0">{{ $list_comment['quota'] ?? '' }}</label>
                        @php $name = 'quota['.$i.']' @endphp
                        {{-- {!! Form::text($name, old($name), ['class' => 'form-control','placeholder'=>$list_comment['quota'],'id'=>$name]); !!} --}}
                        {!! Form::select($name, $list_quota, $name, [
                            'class' => 'form-control',
                            'id' => 'representant_id',
                            'placeholder' => 'Seleccione',
                        ]) !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="exchange_ammount"
                            class="font-weight-bold text-secondary m-0">{{ $list_comment['exchange_ammount'] ?? '' }}</label>
                        @php $name = 'quota_exchange_ammount['.$i.']' @endphp
                        {!! Form::text($name, old($name), [
                            'class' => 'form-control',
                            'placeholder' => $list_comment['exchange_ammount'],
                            'id' => $name,
                            'required',
                        ]) !!}
                        {{-- {!! Form::select($name,$list_divisas,old($name),['class'=>'form-control','placeholder' => 'Monto Cambiario','id'=>$name,'required']) !!} --}}
                    </div>
                </div>
            </div>
        @endfor
    </div>

</div>

{{-- <input class="form-control" type='button' id='addPagos' value='Añadir otro couta'>

@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready(function(){
            $('#addPagos').click(function(){
                var newel = $('#div-clone-pagos:last').clone();
                $(newel).insertAfter("#div-clone-pagos:last");
            });
            $('.txt').focus(function(){
                $(this).css('border-color','red');
            });
            $('.txt').focusout(function(){
                $(this).css('border-color','initial');
            });
        });
    </script>
@endsection --}}
