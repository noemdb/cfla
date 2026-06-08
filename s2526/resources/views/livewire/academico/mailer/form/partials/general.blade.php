<ul class="list-group list-group-flush">
    <li class="list-group-item">
        <div class="row">

            <div class="col">
                <div class="form-group">
                    <label for="name" class=" font-weight-bold m-0 small">{{ $list_comment['name'] ?? '' }}</label>
                    {!! Form::text('name', old('name'), [
                        'wire:model.defer' => 'name',
                        'class' => 'form-control',
                        'placeholder' => $list_comment['name'],
                    ]) !!}
                    @error('name')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="col">
                <div class="form-group">
                    <label for="code" class=" font-weight-bold m-0 small">{{ $list_comment['code'] ?? '' }}</label>
                    {!! Form::text('code', old('code'), [
                        'wire:model.defer' => 'code',
                        'class' => 'form-control',
                        'placeholder' => $list_comment['code'],
                    ]) !!}
                    @error('code')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="fecha" class=" font-weight-bold m-0 small">{{ $list_comment['fecha'] ?? '' }}</label>
                    {!! Form::date('fecha', old('fecha'), [
                        'wire:model.defer' => 'fecha',
                        'class' => 'form-control',
                        'placeholder' => 'Objetivo',
                        'id' => 'fecha',
                    ]) !!}
                    @error('fecha')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="time" class=" font-weight-bold m-0 small">{{ $list_comment['time'] ?? '' }}</label>
                    {!! Form::time('time', old('time'), [
                        'wire:model.defer' => 'time',
                        'class' => 'form-control',
                        'placeholder' => 'Objetivo',
                        'id' => 'time',
                    ]) !!}
                    @error('time')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="description"
                        class=" font-weight-bold m-0 small">{{ $list_comment['description'] ?? '' }}</label>
                    {!! Form::textarea('description', old('description'), [
                        'wire:model.defer' => 'description',
                        'class' => 'form-control',
                        'placeholder' => $list_comment['description'],
                        'rows' => '2',
                    ]) !!}
                    @error('description')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </li>

    <li class="list-group-item">
        @include('livewire.academico.mailer.form.partials.group')
    </li>

</ul>
