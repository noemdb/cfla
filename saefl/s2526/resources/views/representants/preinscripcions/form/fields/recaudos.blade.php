<span class=" font-weight-bold text-dark ">
    Documentos para formalización de la inscripción.
</span>
<div class="px-2 rounded border pb-2 pt-2 mt-2">

    <span class=" font-weight-bold text-primary">
        Requeridos
    </span>
    <div class="px-2 rounded border mb-2 alert-primary">
        <div class="input-group pb-2 py-2">
            <div class="custom-file">
                {!! Form::file('planilla_inscripcion', ['class' => 'custom-file-input', 'required']) !!}
                <label class="custom-file-label" for="inputGroupFile01">Selecciona <span
                        class=" text-muted font-weight-bold">Planilla de inscripción</span></label>
            </div>
        </div>
        <div class="input-group pb-2">
            <div class="custom-file">
                {!! Form::file('contrato_servicio', ['class' => 'custom-file-input', 'required']) !!}
                <label class="custom-file-label" for="inputGroupFile01">Selecciona <span
                        class=" text-muted font-weight-bold">Contrato de Servicio/Compromiso de Pago</span></label>
            </div>
        </div>
        <div class="input-group pb-2">
            <div class="custom-file">
                {!! Form::file('normas_convivencia', ['class' => 'custom-file-input', 'required']) !!}
                <label class="custom-file-label" for="inputGroupFile01">Selecciona <span
                        class=" text-muted font-weight-bold">Normas de Convivencia</span></label>
            </div>
        </div>
    </div>

    <span class=" font-weight-bold text-info">
        Opcionales
    </span>
    <div class="px-2 rounded border mb-2 alert-info">

        <div class="row">
            <div class="col-12">
                <div class="input-group pb-2 py-2">
                    <div class="custom-file">
                        {!! Form::file('partida_nacimineto', ['class' => 'custom-file-input']) !!}
                        <label class="custom-file-label" for="inputGroupFile01">Selecciona <span
                                class=" text-muted font-weight-bold">Partida de Nacimineto del Estudiante</span></label>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="input-group pb-2 py-2">
                    <div class="custom-file">
                        {!! Form::file('foto_carnet_estudiant', ['class' => 'custom-file-input']) !!}
                        <label class="custom-file-label" for="inputGroupFile01">Selecciona <span
                                class=" text-muted font-weight-bold">Foto tipo carnet del Estudiante</span></label>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-12">
                <div class="input-group pb-2">
                    <div class="custom-file">
                        {!! Form::file('cedula_indentidad_estudiant', ['class' => 'custom-file-input']) !!}
                        <label class="custom-file-label" for="inputGroupFile01">Selecciona <span
                                class=" text-muted font-weight-bold">Cédula de Identidad del Estudiante</span></label>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="input-group pb-2">
                    <div class="custom-file">
                        {!! Form::file('ficha_estudiante', ['class' => 'custom-file-input']) !!}
                        <label class="custom-file-label" for="inputGroupFile01">Selecciona <span
                                class=" text-muted font-weight-bold">Ficha del Estudiante</span></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="input-group pb-2">
                    <div class="custom-file">
                        {!! Form::file('cedula_indentidad_representant', ['class' => 'custom-file-input']) !!}
                        <label class="custom-file-label" for="inputGroupFile01">Selecciona <span
                                class=" text-muted font-weight-bold">Cédula de Identidad del
                                Representante</span></label>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="input-group pb-2">
                    <div class="custom-file">
                        {!! Form::file('foto_carnet_estudiant', ['class' => 'custom-file-input']) !!}
                        <label class="custom-file-label" for="inputGroupFile01">Selecciona <span
                                class=" text-muted font-weight-bold">Foto tipo carnet del Representante</span></label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="input-group pb-2 py-2">
                    <div class="custom-file">
                        {!! Form::file('ficha_representante', ['class' => 'custom-file-input']) !!}
                        <label class="custom-file-label" for="inputGroupFile01">Selecciona <span
                                class=" text-muted font-weight-bold">Ficha del Representante</span></label>
                    </div>
                </div>
            </div>
            <div class="col-12">
            </div>
        </div>

    </div>

</div>
