<div class="modal fade" id="ticketRegister" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="" role="document">
        <div class="modal-content">
            <div class="card-header alert-success">
                <h4 class="modal-title">Ticket de registro</h4>
            </div>
            <div class="card-body">

                <span class="font-weight-bold p-2 text-success">Su información fue procesada y registrada exitosamente.</span>

                <div class="text-success text-center">
                    <i class="fa fa-check fa-4x border rounded-circle p-2 m-2" aria-hidden="true"></i>
                </div>

                <ul class="px-2">
                    <li> <span class="font-weight-bold">Número de registro:</span>  {!! $inputs['id'] !!}</li>
                    <li> <span class="font-weight-bold">Fecha:</span> {!! $inputs['date'] !!}</li>
                    <li> <span class="font-weight-bold">Representante:</span> CI-{!! $inputs['ci_representant'] !!} - {!! $inputs['representant_name'] !!}</li>
                    <li> <span class="font-weight-bold">Referencias:</span>  {!! $inputs['number_i_pay'] !!}</li>
                    <li> <span class="font-weight-bold">Monto total:</span> Bs. {!! f_float($inputs['ammount']) !!}</li>
                    <li> <span class="font-weight-bold">Tipo de pago:</span> {!! $inputs['type_pay'] !!}</li>
                </ul>
            </div>
        </div>
    </div>
</div>