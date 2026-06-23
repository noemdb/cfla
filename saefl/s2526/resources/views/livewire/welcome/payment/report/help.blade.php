{{-- <div class="tutorial container text-center my-5 ratio ratio-4x3">
    <iframe src="http://localhost:2223/service/payment/button/credicard" allowfullscreen style="height: 100vh;"></iframe>
</div> --}}

<!-- Button trigger modal -->
{{-- <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#paymentModalGuia">
    Ver guía
</button> --}}

<a name="" id="" class="btn btn-outline-info btn-sm small fw-bold" href="#" role="button" data-bs-toggle="modal" data-bs-target="#paymentReportModalGuia">Ver guía</a>

<!-- Modal -->
<div class="modal fade" id="paymentReportModalGuia" tabindex="-1" aria-labelledby="payReportButtonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
    {{-- <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered"> --}}
        <div class="modal-content">
            <div class="modal-header alert alert-success">
                <h1 class="modal-title fs-5" id="payReportButtonModalLabel">Guía multimedia - Reportar pagos a través de nuestro portal web.</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ratio ratio-1x1">
                    @include('livewire.welcome.payment.report/steps.partials.media')
                    {{-- <iframe src="https://www.youtube.com/embed/g9qZs23E9Lw" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe> --}}
                </div>
            </div>
            {{-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div> --}}
        </div>
    </div>
</div>
