<div>
    {!! Form::select('option_id', $list_options, old('option_id'), [
        'wire:model' => 'option_id',
        'class' => 'form-select',
        'placeholder' => 'Seleccione su respuesta',
        'required' => 'required',
    ]) !!}
    @error('option_id')
        <span class="text-danger small">{{ $message }}</span>
    @enderror

    @if ($poll_option)



        <div class="alert alert-info my-2">
            <h5>Opción seleccionada:</h5>
            <div>
                <div class="fw-bold text-muted mb-2">
                    <div>{{ $poll_option->text ?? null }}</div>
                    <div>
                        <i class=" font-weight-normal">
                            <span class=" text-muted">{{ $poll_option->observations ?? null }}</span>
                        </i>
                    </div>
                </div>

                @if ($poll_option->image)
                    <div class="d-flex justify-content-around">
                        <div class="card" style="width: 18rem;">
                            <img src="{!! asset($poll_option->image_url) !!}" class="card-img-top" alt="...">
                            <div class="card-body">
                                <p class="card-text">{{ $poll_option->description }}.</p>

                            </div>
                        </div>
                        @if ($poll_option->body)
                            <div class="text-start ms-2">
                                <div class="">{!! $poll_option->body !!}</div>
                            </div>
                        @endif
                    </div>
                @endif
                <div class="d-flex justify-content-center my-2">
                    <button type="button" class="btn btn-success btn-lg" wire:click="save({{ $poll_option->id }})"
                        wire:loading.attr="disabled">Registrar</button>
                </div>
            </div>
        </div>
    @endif
</div>
