<div class="container-fluid">

    <div class='input-form' id="div-clone-cashs">
        @for ($i = 0; $i < $num_caashs; $i++)
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="serial"
                            class="font-weight-bold text-secondary m-0">{{ $list_comment['serial'] ?? '' }}</label>
                        @php $name = 'cashs_serial['.$i.']' @endphp
                        {!! Form::text($name, old($name), [
                            'class' => 'form-control',
                            'placeholder' => $list_comment['serial'],
                            'id' => $name,
                            'required',
                        ]) !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="exchange_ammount"
                            class="font-weight-bold text-secondary m-0">{{ $list_comment['exchange_ammount'] ?? '' }}</label>
                        @php $name = 'cashs_exchange_ammount['.$i.']' @endphp
                        {{-- {!! Form::text($name, old($name), ['class' => 'form-control','placeholder'=>$list_comment['exchange_ammount'],'id'=>$name,'required']) !!} --}}
                        {!! Form::select($name, $list_divisas, old($name), [
                            'class' => 'form-control',
                            'placeholder' => 'Monto Cambiario',
                            'id' => $name,
                            'required',
                        ]) !!}
                    </div>
                </div>
            </div>
        @endfor
    </div>

</div>


{{-- <input class="form-control" type='button' id='addCashs' value='Añadir otro billete'>
@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready(function(){
            $('#addCashs').click(function(){
                var newel = $('#div-clone-cashs:last').clone();
                $(newel).insertAfter("#div-clone-cashs:last");
                $(newel).find('input[type=text]:nth-child(1)').val("name_"+index);
            });
        });
    </script>
@endsection --}}
