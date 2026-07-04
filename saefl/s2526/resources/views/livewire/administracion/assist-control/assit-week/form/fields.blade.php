<div class="container-fluid">

    <div class="row">

        {{-- 'name','assit_schedule_id','number_week' --}}

        <div class="col">
            <div class="form-group">
                {!! Form::label('name', $list_comment_assit_week['name'], ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!}
                {!! Form::text('name', old('name'), [
                    'wire:model.defer' => 'name',
                    'class' => 'form-control',
                    'placeholder' => $list_comment_assit_week['name'],
                ]) !!}
                @error('name')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                {!! Form::label('number_week', $list_comment_assit_week['number_week'], [
                    'class' => 'font-weight-bold text-muted pb-0 mb-0',
                ]) !!}
                {!! Form::selectRange('number_week', 1, 5, old('number_week'), [
                    'wire:model.defer' => 'number_week',
                    'class' => 'form-control',
                    'id' => 'number_week',
                    'placeholder' => 'Seleccione',
                ]) !!}
                @error('number_week')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>

    </div>

</div>
