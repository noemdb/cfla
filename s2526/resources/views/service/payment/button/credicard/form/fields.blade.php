<div class="container flex-grow-1 flex-shrink-0 py-5">
    <div class="mb-5 p-4 bg-white shadow-sm">
        <h3>Asistente de pago</h3>
        <div id="stepper1" class="bs-stepper">
            <div class="bs-stepper-header" role="tablist">
                <div class="step" data-target="#test-l-1">
                    <button type="button" class="step-trigger" role="tab" id="stepper1trigger1"
                        aria-controls="test-l-1">
                        <span class="bs-stepper-circle">1</span>
                        <span class="bs-stepper-label">Cédula</span>
                    </button>
                </div>
                <div class="bs-stepper-line"></div>
                <div class="step" data-target="#test-l-2">
                    <button type="button" class="step-trigger" role="tab" id="stepper1trigger2"
                        aria-controls="test-l-2">
                        <span class="bs-stepper-circle">2</span>
                        <span class="bs-stepper-label">Monto</span>
                    </button>
                </div>
                <div class="bs-stepper-line"></div>
                <div class="step" data-target="#test-l-3">
                    <button type="button" class="step-trigger" role="tab" id="stepper1trigger3"
                        aria-controls="test-l-3">
                        <span class="bs-stepper-circle">3</span>
                        <span class="bs-stepper-label">Enviar</span>
                    </button>
                </div>
            </div>
            <div class="bs-stepper-content">
                <form onSubmit="return false">
                    <div id="test-l-1" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger1">
                        <div class="form-group">
                            <label for="exampleInputEmail1" class=" font-weight-bold">Cédula del representante</label>
                            <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Ingrese CI">
                        </div>
                        <button class="btn btn-primary" onclick="stepper1.next()">Siguiente</button>
                    </div>
                    <div id="test-l-2" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger2">
                        <div class="form-group">
                            <label for="exampleInputPassword1">Monto a pagar</label>
                            <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Ingrese monto">
                        </div>
                        <button class="btn btn-primary" onclick="stepper1.previous()">Previous</button>
                        <button class="btn btn-primary" onclick="stepper1.next()">Siguiente</button>
                    </div>
                    <div id="test-l-3" role="tabpanel" class="bs-stepper-pane text-center"
                        aria-labelledby="stepper1trigger3">
                        <button class="btn btn-primary mt-5" onclick="stepper1.previous()">Previous</button>
                        <button type="submit" class="btn btn-primary mt-5">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
