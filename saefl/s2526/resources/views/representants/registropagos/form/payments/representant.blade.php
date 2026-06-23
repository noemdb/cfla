<div class="container mb-2">
    <div class="row">
        <div class="col-md-8 col-sm-12">
            <div class="form-group">
                @php $class = ($errors->has('ci_representant')) ? 'border border-danger rounded':null ; @endphp
                {!! Form::label('ci_representant', 'Cédula', ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!}
                {!! Form::text('ci_representant', old('ci_representant'), [
                    'class' => 'form-control ' . $class,
                    'placeholder' => 'Cédula',
                ]) !!}
                @error('ci_representant')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group ">
                {!! Form::label('name_representant', 'Nombre', ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!}
                {!! Form::text('name_representant', old('name'), ['class' => 'form-control', 'placeholder' => 'Nombre']) !!}
                @error('name_representant')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group ">
                {!! Form::label('phone', 'Teléfono', ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!}
                {!! Form::text('phone', old('phone'), ['class' => 'form-control', 'placeholder' => 'Teléfono']) !!}
                @error('phone')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>


            @php
                $select_arr = [
                    'Deuda pendiente del Período Escolar 2019 - 2020' =>
                        'Deuda pendiente del Período Escolar 2019 - 2020',
                    'Inscripción Período Escolar 2020 - 2021' => 'Inscripción Período Escolar 2020 - 2021',
                    'Mensualidad(es) anterior(es)' => 'Mensualidad(es) anterior(es)',
                    'Mensualidad actual' => 'Mensualidad actual',
                    'Pago(s) por adelantado' => 'Pago(s) por adelantado',
                    'Otros' => 'Otros',
                ];
            @endphp
            <div class="form-group ">
                @php $class = ($errors->has('type_pay')) ? 'border border-danger rounded':null ; @endphp
                {!! Form::label('type_pay', 'Tipo de pago', ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!}
                {!! Form::select('type_pay', $select_arr, old('type_pay'), [
                    'class' => 'form-control ' . $class,
                    'placeholder' => 'Seleccione',
                ]) !!}
                @error('type_pay')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group ">
                @php $class = ($errors->has('comment')) ? 'border border-danger rounded':null ; @endphp
                {!! Form::label('comment', 'Comentarios', ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!}
                {!! Form::textarea('comment', old('comment'), [
                    'class' => 'form-control ' . $class,
                    'placeholder' => 'Comentarios',
                    'rows' => '2',
                ]) !!}
                @error('comment')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class=" alert-secondary rounded small p-2">
                <div class="text-center p-2">
                    <i class="fa fa-info fa-2x border border-light bg-light rounded-circle p-2" aria-hidden="true"></i>
                    {{-- <i class="fa fa-question fa-2x border rounded-circle p-2" aria-hidden="true"></i> --}}
                </div>
                Ingrese los datos del representante legal registrado en la institución. Posteriormente se realizará una
                verificación del número de cédula, sí no se encuentra no podrá realizar el reporte
            </div>
        </div>
    </div>
</div>
