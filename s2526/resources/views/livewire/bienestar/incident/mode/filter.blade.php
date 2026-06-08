<div class=" rounded border">

    <h5 class="alert alert-info text-dark font-weight-bolder rounded">
        Otras opciones de búsquedas
        <button type="button" class="close" wire:click='close()'> <span aria-hidden="true">×</span> </button>
    </h5>

    {{-- <div class="alert alert-secondary"><h5>Otras opciones de búsquedas</h5></div> --}}

    <div class="p-1">
        <ul class="list-group list-group-flush">

            <li class="list-group-item">

                <div class="">

                    <div class=" text-center font-weight-bold border-bottom">Fecha de registro.</div>

                    <div class="d-flex justify-content-between">
                        <div>
                            @php
                                $name = 'finicial';
                                $model = '' . $name;
                            @endphp
                            {!! Form::label($model, $list_comment[$name], ['class' => 'm-0 font-weight-bold text-muted']) !!}
                            {!! Form::date($model, old($model), ['wire:model.defer' => $model, 'class' => 'form-control', 'id' => $model]) !!}
                        </div>
                        <div>
                            @php
                                $name = 'ffinal';
                                $model = '' . $name;
                            @endphp
                            {!! Form::label($model, $list_comment[$name], ['class' => 'm-0 font-weight-bold text-muted']) !!}
                            {!! Form::date($model, old($model), ['wire:model.defer' => $model, 'class' => 'form-control', 'id' => $model]) !!}
                        </div>
                    </div>
                </div>

            </li>

            <li class="list-group-item">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            @php
                                $name = 'status_aggression';
                                $model = '' . $name;
                            @endphp
                            <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
                        </div>
                    </div>
                    <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                </div>
            </li>

            <li class="list-group-item">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            @php
                                $name = 'status_pedagogical';
                                $model = '' . $name;
                            @endphp
                            <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
                        </div>
                    </div>
                    <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                </div>
            </li>

            <li class="list-group-item">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            @php
                                $name = 'status_announcement';
                                $model = '' . $name;
                            @endphp
                            <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
                        </div>
                    </div>
                    <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                </div>
            </li>

            <li class="list-group-item">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            @php
                                $name = 'status_notify';
                                $model = '' . $name;
                            @endphp
                            <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
                        </div>
                    </div>
                    <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                </div>
            </li>

            <li class="list-group-item">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            @php
                                $name = 'status_notify_agreement';
                                $model = '' . $name;
                            @endphp
                            <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
                        </div>
                    </div>
                    <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                </div>
            </li>

            <li class="list-group-item">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            @php
                                $name = 'status_close_filter';
                                $model = '' . $name;
                            @endphp
                            <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
                        </div>
                    </div>
                    <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                </div>
            </li>

            <li class="list-group-item">


                <div class="">

                    <div class=" text-center font-weight-bold border-bottom">Fecha de la convocatoria al representante.
                    </div>

                    {{-- <div class="d-flex justify-content-between"> --}}
                    <div>
                        @php
                            $name = 'date_announcement';
                            $model = '' . $name;
                        @endphp
                        {!! Form::label($model, $list_comment[$name], ['class' => 'm-0 font-weight-bold text-muted']) !!}
                        {!! Form::date($model, old($model), ['wire:model.defer' => $model, 'class' => 'form-control', 'id' => $model]) !!}
                    </div>
                    {{-- </div> --}}
                </div>

            </li>


            <li class="list-group-item">
                <div class="btn-group btn-group-sm btn-block">
                    {!! Form::button('Buscar', ['class' => 'form-control btn btn-info', 'wire:click' => 'applyFilter()']) !!}
                    <a class="btn btn-dark" href="{{ route('bienestars.incidents.index') }}" role="button"><i
                            class="{{ $icon_menus['redo'] ?? '' }} text-light" aria-hidden="true"></i></a>
                    {{-- {!! Form::button('Limpiar',['class' => 'form-control btn btn-dark','wire:click'=>"clearFilter()"]) !!} --}}
                </div>
            </li>
        </ul>
    </div>

</div>
