<div class="p-2">

    <div class="text-muted small mb-2 p-2 border rounded">
        <div class="font-weight-bold">Estilos Cognitivos</div>
        <div>Se refieren a las diferentes formas en que las personas abordan la información, la procesan y la utilizan para resolver problemas.</div>
        <div>Se refiere a los datos, resultados de investigaciones, estudios de caso, estadísticas o cualquier otra información objetiva que respalde una afirmación o argumento. En otras palabras, es la evidencia tangible que demuestra que algo funciona o no, o que una determinada práctica tiene un impacto positivo o negativo.</div>
        <div>
            <strong>Beneficios de integrar diferentes estilos de cognitivos:</strong>
            <div>
                * Mayor profundidad en el análisis: Al abordar el tema desde múltiples perspectivas, se obtiene una comprensión más completa y matizada.<br>
                * Fomento de la creatividad: Se generan ideas nuevas y originales para abordar los desafíos de la educación inclusiva.<br>
                * Desarrollo de habilidades críticas: Los participantes aprenden a evaluar información, a identificar argumentos sólidos y a tomar decisiones informadas.<br>
                * Mayor engagement: Los debates se vuelven más interesantes y estimulantes cuando se promueve la participación activa y la generación de ideas.<br>
            </div>
        </div>
    </div>

    <div class="container-fluid pt-2">

        <div class="row">

            <div class="col-sm-12 col-md-4 px-1">
                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input type="checkbox" wire.model.defer="statusCognitiveInductive" aria-label="Checkbox for following text input">
                            </div>
                        </div>
                        <div class="form-control">
                            <label class="form-check-label small" for="analytical">Analítico</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-4 px-1">
                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input type="checkbox" wire.model.defer="statusCognitiveCreativo" aria-label="Checkbox for following text input">
                            </div>
                        </div>
                        <div class="form-control">
                            <label class="form-check-label small" for="creative">Creativo</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-12 col-md-4 px-1">
                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input type="checkbox" wire.model.defer="statusCognitiveAnalytical" aria-label="Checkbox for following text input">
                            </div>
                        </div>
                        <div class="form-control">
                            <label class="form-check-label small" for="synthetic">Sintético</label>
                        </div>
                    </div>
                </div>
            </div>

            

            <div class="col-sm-12 col-md-4 px-1">
                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input type="checkbox" wire.model.defer="statusCognitiveCritical" aria-label="Checkbox for following text input">
                            </div>
                        </div>
                        <div class="form-control">
                            <label class="form-check-label small" for="creative">Crítico</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-4 px-1">
                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input type="checkbox" wire.model.defer="statusCognitiveSynthetic" aria-label="Checkbox for following text input">
                            </div>
                        </div>
                        <div class="form-control">
                            <label class="form-check-label small" for="inductive">Inductivo</label>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

{{-- inductivo, sintético, analítico, creativo, crítico --}}
{{-- inductive, synthetic, analytical, creative, critical --}}

{{-- statusInductive,statusSynthetic,statusAnalytical,statusCreativo,statusCritical --}}