{{-- ,$banco_emisor_1,$phone_1,$banco_id_1,$method_pay_id_1,$number_i_pay_1,$date_transaction_1,$ammount_1,$observation_1,$image_1 --}}
<div id="test-l-1" role="tabpanel" class="bs-stepper-pane active" aria-labelledby="stepper1trigger1">

    {!! Form::open(['wire:submit.prevent' => 'save']) !!}

    <div class="continer-fluid small">

        <div class="row">
            <div class="col">
                <label for="ci_representant" class="fw-bold">Cédula del representante</label>
                <div class="d-flex">
                    <div class="py-2 w-100">
                        <input type="number" wire:model.defer="ci_representant" id="ci_representant"
                            class="form-control w-100" placeholder="Ingrese CI">
                    </div>
                </div>
                @error('ci_representant')
                    <span class="text-danger small d-block text-right">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="row">

            <div class="col-sm-6 mb-2">
                @php $name = 'banco_id_1' @endphp
                @php $class = ($errors->has($name)) ? 'border border-danger rounded':null ; @endphp
                {!! Form::label($name, $list_comment[$name], ['class' => 'fw-bold text-muted pb-0 mb-0']) !!}
                {!! Form::select($name, $banco_list, old($name), [
                    'wire:model.defer' => $name,
                    'class' => 'form-select ',
                    'placeholder' => 'Seleccione',
                ]) !!}
                @error($name)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-sm-6 mb-2">
                @php $name = 'banco_emisor_1' @endphp
                @php $class = ($errors->has($name)) ? 'border border-danger rounded':null ; @endphp
                {!! Form::label($name, $list_comment[$name], ['class' => 'fw-bold text-muted pb-0 mb-0']) !!}
                {!! Form::select($name, $banco_emisor_list, old($name), [
                    'wire:model.defer' => $name,
                    'class' => 'form-select ',
                    'placeholder' => 'Seleccione',
                ]) !!}
                @error($name)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="row">

            <div class="col-sm-6 mb-2">
                @php $name = 'ammount_1' @endphp
                @php $class = ($errors->has($name)) ? 'border border-danger rounded':null ; @endphp
                {!! Form::label($name, $list_comment[$name], ['class' => 'fw-bold text-muted pb-0 mb-0']) !!}
                {!! Form::text($name, old($name), [
                    'wire:model.defer' => $name,
                    'class' => 'form-control ' . $class,
                    'placeholder' => $list_comment[$name],
                ]) !!}
                @error($name)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-sm-6 mb-2">
                @php $name = 'number_i_pay_1' @endphp
                @php $class = ($errors->has($name)) ? 'border border-danger rounded':null ; @endphp
                {!! Form::label($name, $list_comment[$name], ['class' => 'fw-bold text-muted pb-0 mb-0']) !!}
                {!! Form::text($name, old($name), [
                    'wire:model.defer' => $name,
                    'class' => 'form-control ' . $class,
                    'placeholder' => $list_comment[$name],
                ]) !!}
                @error($name)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="row">

            <div class="col-sm-6 mb-2">
                @php $name = 'method_pay_id_1' @endphp
                @php $class = ($errors->has($name)) ? 'border border-danger rounded':null ; @endphp
                {!! Form::label($name, $list_comment[$name], ['class' => 'fw-bold text-muted pb-0 mb-0']) !!}
                {!! Form::select($name, $method_pay_list, old($name), [
                    'wire:model' => $name,
                    'class' => 'form-select ',
                    'placeholder' => 'Seleccione',
                ]) !!}
                @error($name)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-sm-6 mb-2">
                @php $name = 'date_transaction_1' @endphp
                @php $class = ($errors->has($name)) ? 'border border-danger rounded':null ; @endphp
                {!! Form::label($name, $list_comment[$name], ['class' => 'fw-bold text-muted pb-0 mb-0']) !!}
                {!! Form::date($name, old($name), ['wire:model.defer' => $name, 'class' => 'form-control ']) !!}
                @error($name)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="row">

            @if ($method_pay_id_1 == 5 || $method_pay_id_1 == 7)
                <div class="col mb-2">
                    @php $name = 'phone_1' @endphp
                    @php $class = ($errors->has($name)) ? 'border border-danger rounded':null ; @endphp
                    {!! Form::label($name, $list_comment[$name], ['class' => 'fw-bold text-muted pb-0 mb-0']) !!}
                    {!! Form::text($name, old($name), [
                        'wire:model.defer' => $name,
                        'class' => 'form-control ' . $class,
                        'placeholder' => $list_comment[$name],
                    ]) !!}
                    @error($name)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            @endif

        </div>

        <hr class="my-1 py-1">

        <div class="row pb-1">
            <div class="col-12">
                @php
                    $name = 'image';
                    $model = $name;
                @endphp
                <label for="{{ $name ?? null }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                <div class="custom-file">
                    <div class="input-group mb-3">
                        <input class="form-control" type="file" wire:model="{{ $name }}"
                            id="{{ $model }}" class="custom-file-input" id="customFile">
                        <label class="input-group-text" for="customFile">{{ $list_comment[$name] ?? '' }}</label>
                    </div>
                    @if ($image && method_exists($image, 'isPreviewable') && $image->isPreviewable())
                        <center>
                            <div class="">
                                <div class="text-muted">Vista previa:</div>
                                <div class="card" style="width: 18rem;">
                                    <img src="{{ $image->temporaryUrl() ?? null }}" class="card-img-top"
                                        alt="...">
                                </div>
                            </div>
                        </center>
                    @endif
                </div>
                @error($name)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="row pb-1">
            <div class="col">
                @php $name = 'comment' @endphp
                @php $class = ($errors->has($name)) ? 'border border-danger rounded':null ; @endphp
                {!! Form::label($name, $list_comment[$name], ['class' => 'fw-bold text-muted pb-0 mb-0']) !!}
                {!! Form::textarea($name, old($name), [
                    'wire:model.defer' => $name,
                    'class' => 'form-control ' . $class,
                    'placeholder' => $list_comment[$name],
                    'rows' => '4',
                ]) !!}
                @error($name)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>

    </div>

    {!! Form::close() !!}

    <div class="d-flex justify-content-evenly mt-3">
        <button wire:click="goStep(0)" class="btn btn-secondary mx-1">Anterior</button>
        <button wire:click="goStep(2)" class="btn btn-primary mx-1">Siguiente</button>
    </div>

</div>
