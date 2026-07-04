<!-- Button trigger modal -->
{{-- <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#paymentAnyErrors">
  Launch
</button> --}}

<!-- Modal -->
<div class="modal fade" id="paymentAnyErrors" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header alert-danger">
                <h5 class="modal-title font-weight-bold">
                    <i class="fa fa-info p-2 border border-light rounded-pill bg-light ml-2" aria-hidden="true"></i>
                    Se encontrarón errores
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
                <span class=" font-weight-bold">Por favor revise la información siguiente:</span>

                <div class="small pl-2 ml-2">
                    <ol class="ml-2 pl-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ol>
                </div>
                {{-- @include('welcome.payment.partials.anyErrors') --}}
            </div>
        </div>
    </div>
</div>
@push('scripts') <script type="text/javascript">$(document).ready( function() {$('#paymentAnyErrors').modal('show');}); </script> @endpush
