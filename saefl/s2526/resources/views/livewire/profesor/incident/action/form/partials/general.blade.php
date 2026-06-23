{{-- 'estudiant_id','coexistence','status_transport_private_vehicle','status_transport_public_vehicle','status_transport_walking','status_transport_other','status_vaccination_schedule',
'status_sports_potential','place_where_he_practices', --}}

<ul class="list-group list-group-flush">
    <li class="list-group-item">
        <div class="row">

            <div class="col">

                <div class="col">
                    <div class="form-group">
                        @php
                            $name = 'coexistence';
                            $model = 'student_record.' . $name;
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
                {{-- <div class="form-group">
		            <label for="coexistence" class=" font-weight-bold m-0 small">{{$list_comment['coexistence'] ?? ''}}</label>
		            {!! Form::textarea('coexistence', old('coexistence'), ['wire:model.defer'=>'coexistence','class' => 'form-control','placeholder'=>$list_comment['coexistence'],'rows'=>"2"]) !!}
		            @error('name')<span class="text-danger small">{{ $message }}</span> @enderror
		        </div>                         --}}
            </div>

        </div>
    </li>

    <li class="list-group-item">
        <div class="text-dark"> ¿Cómo se transporta el escolar para llegar a la escuela? (marque con una x la casilla
            correspondiente) </div>

        <div class="row">

            <div class="col">

                <div class="row py-1">
                    <div class="col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    @php
                                        $name = 'status_transport_private_vehicle';
                                        $model = 'student_record.' . $name;
                                    @endphp
                                    <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
                                </div>
                            </div>
                            <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                            @error($model)
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    @php
                                        $name = 'status_transport_public_vehicle';
                                        $model = 'student_record.' . $name;
                                    @endphp
                                    <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
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
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    @php
                                        $name = 'status_transport_walking';
                                        $model = 'student_record.' . $name;
                                    @endphp
                                    <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
                                </div>
                            </div>
                            <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                            @error($model)
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    @php
                                        $name = 'status_transport_other';
                                        $model = 'student_record.' . $name;
                                    @endphp
                                    <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
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
                        <div class="form-group">
                            @php
                                $name = 'transport_other';
                                $model = 'student_record.' . $name;
                            @endphp
                            <label for="footer"
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
                                        $model = 'student_record.' . $name;
                                    @endphp
                                    <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
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
                                $model = 'student_record.' . $name;
                            @endphp
                            <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
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
                <div class="form-group">
                    @php
                        $name = 'sports_potential';
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

        <div class="row py-1">
            <div class="col">
                <div class="form-group">
                    @php
                        $name = 'place_where_he_practices';
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
