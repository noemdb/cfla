<div class=" p-2 border rounded mb-2">
    <h5>Datos del Generales</h5>
    <ul class="list-group list-group-flush">
        <li class="list-group-item">
            <div class="row">

                <div class="col">
                    <div class="col">
                        <div class="form-group pb-2">
                            @php
                                $name = 'coexistence';
                                $model = '' . $name;
                            @endphp
                            <label for="footer"
                                class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                            {!! Form::textarea($name, old($name), [
                                'class' => 'form-control',
                                'placeholder' => $list_comment[$name],
                                'rows' => '2',
                                'required',
                            ]) !!}
                            @error($model)
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>
        </li>

        <li class="list-group-item">

            <div class="text-dark"> ¿Cómo se transporta el escolar para llegar a la escuela? (marque con una x la
                casilla correspondiente) </div>

            <div class="row">

                <div class="col">

                    <div class="row py-1">
                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        @php
                                            $name = 'status_transport_private_vehicle';
                                            $model = '' . $name;
                                        @endphp
                                        <input type="checkbox" name="{{ $name }}" value="1">
                                    </div>
                                </div>
                                <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                                @error($model)
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        @php
                                            $name = 'status_transport_public_vehicle';
                                            $model = '' . $name;
                                        @endphp
                                        <input type="checkbox" name="{{ $name }}" value="1">
                                    </div>
                                </div>
                                <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                                @error($model)
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>

                    <div class="row py-1">
                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        @php
                                            $name = 'status_transport_walking';
                                            $model = '' . $name;
                                        @endphp
                                        <input type="checkbox" name="{{ $name }}" value="1">
                                    </div>
                                </div>
                                <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                                @error($model)
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        @php
                                            $name = 'status_transport_other';
                                            $model = '' . $name;
                                        @endphp
                                        <input type="checkbox" name="{{ $name }}" value="1">
                                    </div>
                                </div>
                                <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                                @error($model)
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>

                    <div class="row py-1">
                        <div class="col">
                            <div class="form-group pb-2">
                                @php
                                    $name = 'transport_other';
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
                </div>

            </div>
        </li>


        <li class="list-group-item">

            <div class="row">

                <div class="col">

                    <div class="row py-1">
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        @php
                                            $name = 'status_vaccination_schedule';
                                            $model = '' . $name;
                                        @endphp
                                        <input type="checkbox" name="{{ $name }}" value="1">
                                    </div>
                                </div>
                                <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                                @error($model)
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </li>

        <li class="list-group-item">

            <div class="row py-1">
                <div class="col">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                @php
                                    $name = 'status_sports_potential';
                                    $model = '' . $name;
                                @endphp
                                <input type="checkbox" name="{{ $name }}" value="1">
                            </div>
                        </div>
                        <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                        @error($model)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row py-1">
                <div class="col">
                    <div class="form-group pb-2>
                        @php
                            $name = 'sports_potential';
                            $model = '' . $name;
                        @endphp
                        <label for="footer"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::select($name, $list_potencial, old($name), [
                            'id' => $name,
                            'class' => 'form-select',
                            'placeholder' => 'Seleccione',
                        ]) !!}
                        @error($model)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row py-1">
                <div class="col">
                    <div class="form-group pb-2>
                        @php
                            $name = 'place_where_he_practices';
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
        </li>
    </ul>

    <div class="row">
        <div class="col-6">
            <div class="form-group pb-2">
                @php
                    $name = 'height';
                    $model = '' . $name;
                @endphp
                <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
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
        <div class="col-6">
            <div class="form-group pb-2">
                @php
                    $name = 'weight';
                    $model = '' . $name;
                @endphp
                <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
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

    <div class="row py-1">
        <div class="col">
            <div class="form-group pb-2">
                @php
                    $name = 'status_brother';
                    $model = '' . $name;
                @endphp
                <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($name, ['true' => 'SI', 'false' => 'NO'], old($name), [
                    'id' => $name,
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row py-1">
        <div class="col">
            <div class="form-group pb-2">
                @php
                    $name = 'order_born';
                    $model = '' . $name;
                @endphp
                <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>

                {!! Form::select(
                    $name,
                    ['U' => 'UNICO', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5'],
                    old($name),
                    ['id' => $name, 'class' => 'form-control', 'placeholder' => 'Seleccione', 'required'],
                ) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row py-1">
        <div class="col">
            <div class="form-group pb-2">
                @php
                    $name = 'blood_type';
                    $model = '' . $name;
                @endphp
                <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($name, $list_blood_type, old($name), [
                    'id' => $name,
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row py-1">
        <div class="col">
            <div class="form-group pb-2">
                @php
                    $name = 'laterality';
                    $model = '' . $name;
                @endphp
                <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($name, ['IZQUIERDA' => 'IZQUIERDA', 'DERECHA' => 'DERECHA'], old($name), [
                    'id' => $name,
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>
