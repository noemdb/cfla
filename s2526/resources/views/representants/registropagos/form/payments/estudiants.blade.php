{{-- <div class="container"> --}}
<div class="row">
    @for ($i = 1; $i <= 3; $i++)
        <div class=" col-sm-12 col-md-6">
            <div class=" p-2 m-2">

                <div class="row">
                    <div class="col-sm-7 col-md-7 px-1">
                        @php $name_estudiant = 'name_estudiant_'.$i @endphp
                        @php $class = ($errors->has($name_estudiant)) ? 'border border-danger rounded':null ; @endphp
                        <div class="form-group ">
                            {!! Form::text($name_estudiant, old($name_estudiant), ['class' => 'form-control', 'placeholder' => 'Nombre']) !!}
                            @error($name_estudiant)
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        @php $name = 'estudiant_select_'.$i @endphp
                        {!! Form::hidden($name, $value) !!}

                    </div>
                    <div class="col-sm-5 col-md-5 px-1">
                        @php $grado_estudiant = 'grado_estudiant_'.$i @endphp
                        @php $class = ($errors->has($grado_estudiant)) ? 'border border-danger rounded':null ; @endphp
                        <div class="form-group ">
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
    @endfor
</div>
{{-- </div> --}}
