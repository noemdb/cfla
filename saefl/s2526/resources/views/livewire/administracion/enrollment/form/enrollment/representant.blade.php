    <h5>Datos del representante</h5>
    <div class="container-fluid">
        <div class="row">
            <div class="col"> <strong>Información Académica.</strong> </div>
        </div>
        <div class="row py-2">
            <div class="col-sm-4">
                <div class="form-group pb-2">
                    @php
                        $name = 'ci_representant';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {{-- {!! Form::number($name, old($name), ['wire:model.defer'=>$model,'class' => 'form-control alert alert-secondary p-2 fw-bold','readonly','placeholder'=>$list_comment[$name]]) !!} --}}
                    {!! Form::number($name, old($name), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control p-2 fw-bold',
                        'placeholder' => $list_comment[$name],
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-8">
                <div class="form-group pb-2">
                    @php
                        $name = 'name_representant';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {{-- {!! Form::text($name, old($name), ['wire:model.defer'=>$model,'class' => 'form-control alert alert-secondary p-2 fw-bold','readonly','placeholder'=>$list_comment[$name]]) !!} --}}
                    {!! Form::text($name, old($name), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control p-2 fw-bold',
                        'placeholder' => $list_comment[$name],
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row py-2">
            <div class="col-sm-6">
                <div class="form-group pb-2">
                    @php
                        $name = 'relationship';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::select($model, $list_relationship, old($model), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'id' => $model,
                        'placeholder' => 'Selecciones',
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group pb-2">
                    @php
                        $name = 'phone_representant';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
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
        <div class="row py-2">
            <div class="col-sm-6">
                <div class="form-group pb-2">
                    @php
                        $name = 'email_representant';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::email($name, old($name), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group pb-2">
                    @php
                        $name = 'profession_representant';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
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
        <div class="row py-2">
            <div class="col-sm-12">
                <div class="form-group pb-2">
                    @php
                        $name = 'recommended_by';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
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
    </div>
