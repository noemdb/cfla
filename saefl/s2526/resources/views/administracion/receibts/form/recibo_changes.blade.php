<div class="container-fluid">

    <div class='input-form' id="div-clone-cashs">
        @for ($i = 0; $i < $num_changes; $i++)
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="serial"
                            class="font-weight-bold text-secondary m-0">{{ $list_comment['serial'] ?? '' }}</label>
                        @php $name = 'changes_serial['.$i.']' @endphp
                        {!! Form::text($name, old($name), [
                            'class' => 'form-control',
                            'placeholder' => $list_comment['serial'],
                            'id' => $name,
                        ]) !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="exchange_ammount"
                            class="font-weight-bold text-secondary m-0">{{ $list_comment['exchange_ammount'] ?? '' }}</label>
                        @php $name = 'changes_exchange_ammount['.$i.']' @endphp
                        {{-- {!! Form::text($name, old($name), ['class' => 'form-control','placeholder'=>$list_comment['exchange_ammount'],'id'=>$name]); !!} --}}
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
