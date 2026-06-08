<div class="pb-2" x-data="{ open: false }">

    <div class="text-secondary text-center" @click="open = ! open" role="button">
        <div class="small fw-bold text-dark">Tarjetas de Dédito aceptadas</div>
    </div>
    <div x-show="open" @click.outside="open = false">
        <div class="card card-body small">
            <div class="d-flex justify-content-center" >
                <div class="me-auto ps-2 text-center">
                    <div class="d-flex justify-content-center" >
                        <img class="border border-light shadow-sm rounded img-fluid p-1 bg-light" src="{{ asset('images/thumbnails/issuingBank/bancamiga.png') }}" alt="" width="100rem">
                    </div>
                </div>
                <div class="me-auto ps-2 text-center">
                    <div class="d-flex justify-content-center" >
                        <img class="border border-light shadow-sm rounded img-fluid p-1 bg-light" src="{{ asset('images/thumbnails/issuingBank/bancaribe.png') }}" alt="" width="100rem">
                    </div>
                </div>
                <div class="me-auto ps-2 text-center">
                    <div class="d-flex justify-content-center" >
                        <img class="border border-light shadow-sm rounded img-fluid p-1 bg-light" src="{{ asset('images/thumbnails/issuingBank/bancrecer.png') }}" alt="" width="100rem">
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center" >
                <div class="me-auto ps-2 text-center">
                    <div class="d-flex justify-content-center" >
                        <img class="border border-light shadow-sm rounded img-fluid p-1 bg-light" src="{{ asset('images/thumbnails/issuingBank/banfanb.png') }}" alt="" width="100rem">
                    </div>
                </div>
                <div class="me-auto ps-2 text-center">
                    <div class="d-flex justify-content-center" >
                        <img class="border border-light shadow-sm rounded img-fluid p-1 bg-light" src="{{ asset('images/thumbnails/issuingBank/mibanco.png') }}" alt="" width="100rem">
                    </div>
                </div>
                <div class="me-auto ps-2 text-center">
                    <div class="d-flex justify-content-center" >
                        <img class="border border-light shadow-sm rounded img-fluid p-1 bg-light" src="{{ asset('images/thumbnails/issuingBank/tesoro.png') }}" alt="" width="100rem">
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
