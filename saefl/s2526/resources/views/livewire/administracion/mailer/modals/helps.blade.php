<!-- Button trigger modal -->
{{-- <button type="button" class="btn btn-info btn-sm float-right" data-toggle="modal" data-target="#paymentAnyErrors">
  <i class="{{ $icon_menus['ayuda'] ?? ''}} fa-1x"></i>
</button> --}}
<!-- Modal -->
<div class="modal fade" id="paymentAnyErrors" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header alert-info">
                <h5 class="modal-title font-weight-bold">
                    <i class="fa fa-info p-2 border border-light rounded-pill bg-light ml-2" aria-hidden="true"></i>
                    Secuencia de pasos para los procedimientos básicos de esta sección.
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body display-6">
                @include('livewire.academico.mailer.helps.main')
            </div>
        </div>
    </div>
</div>