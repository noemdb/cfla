{{-- 'mother_name','mother_lastname','mother_profession','mother_phones','mother_address','father_name','father_lastname','father_profession','father_phones','father_address',     --}}

<ul class="list-group list-group-flush">
    <li class="list-group-item">

        <div class="text-dark"> Datos de la madre: </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    @php
                        $name = 'mother_name';
                        $model = 'student_record.' . $name;
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
                <div class="form-group">
                    @php
                        $name = 'mother_lastname';
                        $model = 'student_record.' . $name;
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
                <div class="form-group">
                    @php
                        $name = 'mother_ci';
                        $model = 'student_record.' . $name;
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
                <div class="form-group">
                    @php
                        $name = 'mother_profession';
                        $model = 'student_record.' . $name;
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
                <div class="form-group">
                    @php
                        $name = 'mother_phones';
                        $model = 'student_record.' . $name;
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

    </li>

    <li class="list-group-item">

        <div class="text-dark"> Datos del padre: </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    @php
                        $name = 'father_name';
                        $model = 'student_record.' . $name;
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
                <div class="form-group">
                    @php
                        $name = 'father_lastname';
                        $model = 'student_record.' . $name;
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
                <div class="form-group">
                    @php
                        $name = 'father_ci';
                        $model = 'student_record.' . $name;
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
                <div class="form-group">
                    @php
                        $name = 'father_profession';
                        $model = 'student_record.' . $name;
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
                <div class="form-group">
                    @php
                        $name = 'father_phones';
                        $model = 'student_record.' . $name;
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

    </li>
</ul>
