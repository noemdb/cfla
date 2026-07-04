<div class="p-2">

    <div class="text-muted small mb-2 p-2 border rounded">
        <div class="font-weight-bold">Integración, ejes transversales</div>
        <div>Los ejes transversales en los debates académicos es importante para fomentar un aprendizaje significativo y
            holístico en los estudiantes. Al conectar diferentes áreas del conocimiento, se promueve una visión más
            completa y compleja del mundo, y se prepara a los estudiantes para enfrentar los desafíos de una sociedad
            cada vez más interconectada.</div>
        <div>
            <strong>Ejemplos de conceptos transversales y posibles integraciones:</strong>
            <div>
                <table class="table table-sm small">
                    <thead>
                        <tr>
                            <th>Concepto transversal</th>
                            <th>Áreas de formación</th>
                            <th>Posibles actividades</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td scope="row">Sostenibilidad</td>
                            <td>Ciencias naturales, sociales, lengua y literatura</td>
                            <td>Proyecto de investigación sobre el impacto del cambio climático en la comunidad local.
                            </td>
                        </tr>
                        <tr>
                            <td scope="row">Ciudadanía</td>
                            <td>Ciencias sociales, lengua y literatura, artes</td>
                            <td>Debate sobre los derechos humanos y la creación de una campaña de sensibilización.</td>
                        </tr>
                        <tr>
                            <td scope="row">Resolución de problemas</td>
                            <td>Matemáticas, ciencias naturales, tecnología</td>
                            <td>Diseño y construcción de un prototipo para resolver un problema real.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="p-2">
        <div class="font-weight-bold small">Ejes transversales, separados por comas</div>
        <div class="form-group">
            @php
                $name = 'crossCutting';
                $model = '' . $name;
            @endphp
            {!! Form::textarea($name, old($name), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => 'Ejes transversales, separados por comas',
                'rows' => '1',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>

</div>
