<div>
    <div class="my-2 mx-1 border rounded py-2 px-1 shadow-sm">

        <center><img class="mb-0" src="{{ asset('images/logo/report-payment.png') }}" alt="" width="144" height="144"></center>

        <h4 class="text-center fw-bold">Reporte de pago</h4>
        <h3 class="text-center" style="color:#143D8D"> <span>Asistente</span> </h3>


        @if ($currentStep == 0)

            @include('livewire.welcome.payment.report.steps.welcome')

        @else

            @include('livewire.welcome.payment.report.steps.main')

        @endif

    </div>

</div>

