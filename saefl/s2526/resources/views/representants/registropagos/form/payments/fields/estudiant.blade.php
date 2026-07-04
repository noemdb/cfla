<div class="container mb-2">
    <div class="row mb-2">
        <div class="col-sm-12 col-md-8">

            <div class=" p-2 m-2">

                <div class="row">
                    <div class="col-sm-7 col-md-7 px-1">
                        @php $name_estudiant = 'name_estudiant_'.$i @endphp
                        @php $class = ($errors->has($name_estudiant)) ? 'border border-danger rounded':null ; @endphp
                        <div class="form-group ">
                            {{-- {!! Form::label($name_estudiant, 'Estudante '.$i, ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!} --}}
                            {!! Form::text($name_estudiant, old($name_estudiant), ['class' => 'form-control', 'placeholder' => 'Nombre']) !!}
                            @error($name_estudiant)
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror

                            @php $name = 'estudiant_select_'.$i @endphp
                            {!! Form::hidden($name, 'SI') !!}
                        </div>

                    </div>
                    <div class="col-sm-5 col-md-5 px-1">
                        @php $grado_estudiant = 'grado_estudiant_'.$i @endphp
                        @php $class = ($errors->has($grado_estudiant)) ? 'border border-danger rounded':null ; @endphp
                        <div class="form-group ">
                            {{-- {!! Form::label($grado_estudiant, ' ', ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!} --}}
                            {!! Form::select($grado_estudiant, $list_grado_seccion, old($grado_estudiant), [
                                'class' => 'form-control ' . $class,
                                'placeholder' => 'Grado/Sección',
                            ]) !!}
                            @error($grado_estudiant)
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                </div>

            </div>

        </div>
        <div class="col-md-4 col-sm-12">

            <div class="row alert-secondary">
                <div class="col-sm-9">
                    <span class="small">
                        Ingrese los datos del estudiante del cual desea registrar el pago
                    </span>
                </div>
                <div class="col-sm-3 text-center rounded p-2">
                    <i class="fa fa-info fa-2x border border-light bg-light rounded-circle p-2" aria-hidden="true"></i>
                </div>
            </div>

        </div>

    </div>
</div>
