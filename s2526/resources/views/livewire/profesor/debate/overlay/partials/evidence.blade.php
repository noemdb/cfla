<div class="p-2">

    <div class="text-muted small mb-2 p-2 border rounded">
        <div class="font-weight-bold">Evidencia empírica</div>
        <div>¿Qué es la evidencia empírica en el contexto de un debate académico?</div>
        <div>Se refiere a los datos, resultados de investigaciones, estudios de caso, estadísticas o cualquier otra información objetiva que respalde una afirmación o argumento. En otras palabras, es la evidencia tangible que demuestra que algo funciona o no, o que una determinada práctica tiene un impacto positivo o negativo.</div>
        <div>
            <strong>¿Por qué es importante la evidencia empírica en un debate en la educación?</strong>
            <div>
                * Fortalece los argumentos: La evidencia empírica brinda solidez a las opiniones y propuestas, haciendo que sean más convincentes y difíciles de refutar.<br>
                * Orienta la toma de decisiones: Permite identificar las prácticas educativas más efectivas y basadas en la evidencia.<br>
                * Promueve la mejora continua: La evidencia empírica permite evaluar el impacto de las intervenciones educativas y realizar ajustes para optimizar los resultados.<br>
                * Fomenta el diálogo basado en hechos: Al centrarse en datos objetivos, se promueve un debate más constructivo y menos basado en opiniones subjetivas.<br>
            </div>
        </div>
    </div>

    <div class="container-fluid pt-2">
        <div class="row">
            <div class="col">

                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                @php $name = 'statusEmpiricalEvidence' ; $model= ''.$name @endphp
                                <input type="checkbox" wire:model.defer="statusEmpiricalEvidence" aria-label="Checkbox for following text input">
                            </div>
                        </div>
                        <div class="form-control">
                            <label class="form-check-label small" for="creative">Evidencia Empírica</label>
                            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="text-muted small">Se espera que el debate considere y valore la evidencia empírica que podría sustentar las argumentaciones en la formulaciónm de las preguntas y en sus opciones de respuestas.</div>
                </div>

            </div>

        </div>
    </div>

</div>