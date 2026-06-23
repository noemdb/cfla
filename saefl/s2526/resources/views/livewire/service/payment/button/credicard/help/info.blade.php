<div class="pb-2" x-data="{ open: false }">

    <div class="text-secondary text-center" @click="open = ! open" role="button">
        Información de esta sección
    </div>
    <div x-show="open" @click.outside="open = false">
        <div class="card card-body small">
            <div class="small text-muted pb-2">
                <div>
                    En esta sección se tiene una herramienta de pago, puedes cancelar tus cuotas vencidas o realizar pagos adelantados.
                    Esta integración con el <b>Consorcio Credicard</b>, nos permite disponer de un punto de pago virtual (TPV).
                    Todas estas transacciones son conciliadas de manera automática.
                </div>
                <div class="ps-1 ms-1 pt-2">
                    <div class="fw-bold">Los banco conectados con Credicard:</div>
                    <div class="ps-1 ms-1">
                        <div class="">Banco de Venezuela</div>
                        <div class="">Bancaribe</div>
                        <div class="">Banco del Tesoro</div>
                        <div class="">Mi Banco</div>
                        <div class="">Bancamiga</div>
                        <div class="">Bancrecer</div>
                        <div class="">BANFANB</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
