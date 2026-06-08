<div for="name_representant" class="fw-bolder mb-0 text-start">Representante:</div>

<div class="alert alert-secondary text-left p-2">
    <div class="d-flex justify-content-start">
        {{ $name_representant ?? null }} CI: {{ $ci_representant ?? null }}
    </div>
</div>

@if ($exchange_ammount_expire_bill > 0)
    <div class=" d-flex justify-content-between my-2 fw-bolder">
        <div class="btn-group btn-group-sm w-100" role="group" aria-label="Basic mixed styles example">
            <button type="button" class="btn btn-light fw-bolder w-25">Deuda vencida:</button>
            <button type="button" class="btn btn-danger fw-bolder w-25">Bs {{ $exchange_rate_ammount ? f_float($ammount) : 'STDCR' }}</button>
            <button type="button" class="btn btn-success fw-bolder w-25">USD {{ f_float($exchange_ammount_expire_bill) }}</button>
            {{-- <button type="button" class="btn btn-dark fw-bolder w-25">TDC: {{ $exchange_rate_ammount ? f_float($exchange_rate_ammount) : 'STDCR' }}</button> --}}
        </div>
    </div>

    <div class="d-flex justify-content-end">
        <div class="form-check form-switch">
            <input wire:model="totalPay" class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
            <label class="form-check-label" for="flexSwitchCheckDefault">Pago {{ $totalPay ? 'total' : 'parcial' }}</label>
        </div>
    </div>

@else

    <div class="text-dark d-flex justify-content-center align-items-center gap-1">
        <span>Usted no tiene deuda vencida, está </span> <span class="badge bg-success fw-bold p-2" style="font-size: 0.8rem !important">Solvente</span>
    </div>

    <hr>

    <span class="text-muted fw-bold">Puede realizar pagos adelantados a sus cuotas no venciadas.</span>

    <div class="py-2" role="alert">
        <div class=" d-flex  my-2 fw-bolder">
            <div wire:click="setAmmountPayUnexpired()" class="btn-group btn-group-sm w-100" role="group" aria-label="Basic mixed styles example">
                <button type="button" class="btn btn-light fw-bolder w-25">Deuda no vencida:</button>
                <button type="button" class="btn btn-warning fw-bolder w-25">Bs {{ $exchange_rate_ammount ? f_float($ammount_unexpired) : 'STDCR' }}</button>
                <button type="button" class="btn btn-success fw-bolder w-25">USD {{ f_float($exchange_ammount_unexpired_bill) }}</button>
            </div>
        </div>

    </div>

@endif


