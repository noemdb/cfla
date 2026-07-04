<div>

    <div id="main-btn-payment-cfla" class="bg-light" style="max-width: 50rem !important;">

        <center>

            <h5> Botón de Pago CFLA </h5>

        </center>

        @if ($exchange_rate_ammount)

            <form onSubmit="return false" method="POST">

                <div class="container flex-grow-1 flex-shrink-0 py-2 small">

                    @if ($modeAssistent)

                        @include('livewire.service.payment.button.credicard.steper.main')

                    @endif

                    @if ($modeConnected)

                        @include('livewire.service.payment.button.credicard.connected.main')

                    @endif

                </div>

            </form>

            <div class="continer text-start mb-4">
                @include('livewire.service.payment.button.credicard.help.cardAccepted')

            </div>

            <div class="container text-start mb-4">
                @include('livewire.service.payment.button.credicard.help.info')
            </div>

        @else

            <div class="alert alert-warning small m-1">
                Disculpe, la tasa de cambio publicada por el Banco Central de Venezuela correspondiente para hoy,
                aún no ha sido registrada en nuestro sistema por motivos externos, lo que significa que no se pueden procesar pagos a través de
                este servicio. Le pedimos que lo intente más adelante. Saludos cordiales.
            </div>

        @endif

    </div>

    <script type="text/javascript">

        function filterFloat(evt, input) {
            // Backspace = 8, Enter = 13, ‘0′ = 48, ‘9′ = 57, ‘.’ = 46, ‘-’ = 43
            var key = window.Event ? evt.which : evt.keyCode;
            var chark = String.fromCharCode(key);
            var tempValue = input.value + chark;
            if (key >= 48 && key <= 57) {
                if (filter(tempValue) === false) {
                    return false;
                } else {
                    return true;
                }
            } else {
                if (key == 8 || key == 13 || key == 0) {
                    return true;
                } else if (key == 46) {
                    if (filter(tempValue) === false) {
                        return false;
                    } else {
                        return true;
                    }
                } else {
                    return false;
                }
            }
        }
    
        function filter(__val__) {
            var preg = /^([0-9]+\.?[0-9]{0,2})$/;
            if (preg.test(__val__) === true) {
                return true;
            } else {
                return false;
            }
    
        }
    
    </script>

</div>



