{{-- 'mother_name','mother_lastname','mother_profession','mother_phones','mother_address','father_name','father_lastname','father_profession','father_phones','father_address',     --}}

<ul class="list-group list-group-flush">
    <li class="list-group-item">

        <div class="text-dark"> Datos de la madre: </div>
        <div class="row">
            <div class="col">
                <div class="form-group pb-2">
                    @php
                        $name = 'mother_name';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::text($name, old($name), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group pb-2">
                    @php
                        $name = 'mother_lastname';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::text($name, old($name), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group pb-2">
                    @php
                        $name = 'mother_ci';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::number($name, old($name), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group pb-2">
                    @php
                        $name = 'mother_profession';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::text($name, old($name), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group pb-2">
                    @php
                        $name = 'mother_phones';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::number($name, old($name), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col">
                <div class="form-group pb-2">
                    @php
                        $name = 'mother_address';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::textarea($name, old($name), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                        'rows' => '2',
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>

        </div>

    </li>

    <li class="list-group-item">

        <div class="text-dark"> Datos del padre: </div>
        <div class="row">
            <div class="col">
                <div class="form-group pb-2">
                    @php
                        $name = 'father_name';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::text($name, old($name), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group pb-2">
                    @php
                        $name = 'father_lastname';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::text($name, old($name), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group pb-2">
                    @php
                        $name = 'father_ci';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::number($name, old($name), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group pb-2">
                    @php
                        $name = 'father_profession';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::text($name, old($name), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group pb-2">
                    @php
                        $name = 'father_phones';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::number($name, old($name), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group pb-2">
                    @php
                        $name = 'father_address';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::textarea($name, old($name), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                        'rows' => '2',
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

    </li>
</ul>
