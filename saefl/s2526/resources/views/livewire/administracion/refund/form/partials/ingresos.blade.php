<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="method_pay_id" class="m-0">{{ $list_comment['method_pay_id'] }}</label>
                {!! Form::select('method_pay_id', $method_pay_list, old('method_pay_id'), [
                    'wire:model.defer' => 'method_pay_id',
                    'class' => 'form-control',
                    'id' => 'method_pay_id',
                    'placeholder' => 'Seleccione',
                    'required' => 'required',
                ]) !!}
                @error('method_pay_id')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="banco_id" class="m-0">{{ $list_comment['banco_id'] }}</label>
                {!! Form::select('banco_id', $banco_list, old('banco_id'), [
                    'wire:model.defer' => 'banco_id',
                    'class' => 'form-control',
                    'id' => 'banco_id',
                    'placeholder' => 'Seleccione',
                    'required' => 'required',
                ]) !!}
                @error('banco_id')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>


<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="date_transaction" class="m-0">Fecha</label>
                {!! Form::date('date_transaction', old('date_transaction'), [
                    'wire:model.defer' => 'date_transaction',
                    'class' => 'form-control',
                    'required',
                ]) !!}
                @error('date_transaction')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="number_i_pay" class="m-0">{{ $list_comment['number_i_pay'] }}</label>
                {!! Form::text('number_i_pay', old('number_i_pay'), [
                    'wire:model.defer' => 'number_i_pay',
                    'class' => 'form-control',
                    'placeholder' => 'Número de la transacción',
                    'id' => 'number_i_pay',
                    'required' => 'required',
                ]) !!}
                @error('number_i_pay')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="ammount" class="m-0">{{ $list_comment['ammount'] }} <span
                        class="text-muted small d-block">[Decimales separados por punto]</span></label>
                {!! Form::text('ammount', old('ammount'), [
                    'wire:model.defer' => 'ammount',
                    'class' => 'form-control ',
                    'placeholder' => $list_comment['ammount'],
                    'id' => 'ammount',
                ]) !!}
                @error('ammount')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="ammount_exchange" class="m-0">{{ $list_comment['ammount_exchange'] }} <span
                        class="text-muted small d-block">[Decimales separados por punto]</span></label>
                {!! Form::text('ammount_exchange', old('ammount_exchange'), [
                    'wire:model.defer' => 'ammount_exchange',
                    'class' => 'form-control ',
                    'placeholder' => $list_comment['ammount_exchange'],
                    'id' => 'ammount_exchange',
                ]) !!}
                @error('ammount_exchange')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>
