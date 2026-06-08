<div id="test-l-1" role="tabpanel" class="bs-stepper-pane active" aria-labelledby="stepper1trigger1">

    <div class="text-start">
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
                    @php $name = 'type_pay' @endphp
                    @php $class = ($errors->has($name)) ? 'border border-danger rounded':null ; @endphp
                    {!! Form::label($name, $list_comment[$name], ['class' => 'fw-bold text-muted pb-0 mb-0']) !!}
                    {!! Form::select($name, $type_pay_list, old($name), [
                        'wire:model.defer' => $name,
                        'class' => 'form-select ',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                    @error($name)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-sm-6 mb-2">
                    @php $name = 'phone' @endphp
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

            {{-- <div class="row">
                <div class="col">
                    @php $name = 'comment' @endphp
                    @php $class = ($errors->has($name)) ? 'border border-danger rounded':null ; @endphp
                    {!! Form::label($name, $list_comment[$name], ['class' => 'fw-bold text-muted pb-0 mb-0']) !!}
                    {!! Form::textarea($name, old($name), ['wire:model.defer'=>$name,'class'=>'form-control '.$class,'placeholder'=>$list_comment[$name],'rows'=>"4"]) !!}
                    @error($name)<span class="text-danger small">{{ $message }}</span> @enderror
                </div>
            </div> --}}

        </div>
    </div>

    {{-- <div class="d-flex justify-content-evenly mt-3"> --}}
    {{-- <button wire:click="previousStep(2)" class="btn btn-secondary mx-1">Anterior</button> --}}
    {{-- <button wire:click="goStep(2)" class="btn btn-primary mx-1">Siguiente</button> --}}
    {{-- </div> --}}

</div>
