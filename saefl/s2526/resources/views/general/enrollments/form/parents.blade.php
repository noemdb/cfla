<div class=" p-2 border rounded mb-2">

    <h5>Datos de los padres</h5>

    <ul class="list-group list-group-flush">
        <li class="list-group-item">

            <div class="text-dark"> Datos de la madre: </div>
            <div class="row">
                <div class="col">
                    <div class="form-group pb-2">
                        @php
                            $name = 'mother_name';
                            $model = '' . $name;
                        @endphp
                        <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::text($name, old($name), [
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

            <div class="row">
                <div class="col">
                    <div class="form-group pb-2">
                        @php
                            $name = 'mother_lastname';
                            $model = '' . $name;
                        @endphp
                        <label for="footer"
                            class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::text($name, old($name), [
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
            <div class="row">
                <div class="col">
                    <div class="form-group pb-2">
                        @php
                            $name = 'mother_ci';
                            $model = '' . $name;
                        @endphp
                        <label for="footer"
                            class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::number($name, old($name), [
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

            <div class="row">
                <div class="col">
                    <div class="form-group pb-2">
                        @php
                            $name = 'mother_profession';
                            $model = '' . $name;
                        @endphp
                        <label for="footer"
                            class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::text($name, old($name), ['class' => 'form-control', 'placeholder' => $list_comment[$name]]) !!}
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
                            $model = '' . $name;
                        @endphp
                        <label for="footer"
                            class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::number($name, old($name), [
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

        </li>

        <li class="list-group-item">

            <div class="text-dark"> Datos del padre: </div>
            <div class="row">
                <div class="col">
                    <div class="form-group pb-2">
                        @php
                            $name = 'father_name';
                            $model = '' . $name;
                        @endphp
                        <label for="footer"
                            class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::text($name, old($name), [
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

            <div class="row">
                <div class="col">
                    <div class="form-group pb-2">
                        @php
                            $name = 'father_lastname';
                            $model = '' . $name;
                        @endphp
                        <label for="footer"
                            class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::text($name, old($name), [
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
            <div class="row">
                <div class="col">
                    <div class="form-group pb-2">
                        @php
                            $name = 'father_ci';
                            $model = '' . $name;
                        @endphp
                        <label for="footer"
                            class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::number($name, old($name), [
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
            <div class="row">
                <div class="col">
                    <div class="form-group pb-2">
                        @php
                            $name = 'father_profession';
                            $model = '' . $name;
                        @endphp
                        <label for="footer"
                            class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::text($name, old($name), ['class' => 'form-control', 'placeholder' => $list_comment[$name]]) !!}
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
                            $model = '' . $name;
                        @endphp
                        <label for="footer"
                            class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::number($name, old($name), [
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

        </li>
    </ul>

</div>
