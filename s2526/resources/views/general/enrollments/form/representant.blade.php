<div class=" p-2 border rounded mb-2">
    <h5>Datos del representante</h5>
    <div class="container-fluid">
        {{-- <div class="row">
            <div class="col"> <strong>Información Académica.</strong> </div>
        </div> --}}
        <div class="row py-2">
            <div class="col-sm-4">
                <div class="form-group pb-2">
                    @php
                        $name = 'ci_representant';
                        $model = '' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::number($name, $enrollment->ci_representant, [
                        'class' => 'form-control alert alert-secondary p-2 fw-bold',
                        'readonly',
                        'placeholder' => $list_comment[$name],
                        'required',
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
                        $model = '' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::text($name, $enrollment->name_representant, [
                        'class' => 'form-control alert alert-secondary p-2 fw-bold',
                        'readonly',
                        'placeholder' => $list_comment[$name],
                        'required',
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
                        $model = '' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::select($model, $list_relationship, $enrollment->relationship, [
                        'class' => 'form-select',
                        'id' => $model,
                        'placeholder' => 'Selecciones',
                        'required',
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
                        $model = '' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::number($name, $enrollment->phone_representant, [
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                        'required',
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
                        $name = 'email_representant';
                        $model = '' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::email($name, $enrollment->email_representant, [
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                        'required',
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
                        $model = '' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::text($name, $enrollment->recommended_by, [
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
</div>
