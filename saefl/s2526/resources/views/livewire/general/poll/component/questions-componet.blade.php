<div>
    <div class="d-flex">
        <div class="flex-grow-1">
            {!! Form::select('question_id', $list_question, old('question_id'), [
                'wire:model' => 'question_id',
                'class' => 'form-select',
                'placeholder' => 'Seleccione la pregunta',
                'required' => 'required',
            ]) !!}
        </div>
        <div class="p-2">
            <small wire:loading.delay.shortest class="text-muted small px-2">
                Procesando...
            </small>
        </div>
    </div>

    @error('question_id') <span class="text-danger small">{{ $message }}</span> @enderror

    <div id="question-selected"></div>

</div>
