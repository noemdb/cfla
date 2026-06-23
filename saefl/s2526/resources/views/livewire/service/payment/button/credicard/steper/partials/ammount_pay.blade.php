<div class="d-flex">
    <div class="flex-fill">
        <div class="input-group input-group">
            <span class="input-group-text small" id="basic-addon1">Bs.</span>
            <input {{ $totalPay ? 'readonly' : null }} type="text" wire:model="ammount_pay" wire:click="enableAmmountPay()"  class="form-control form-control-lg fw-bold {{ $totalPay ? 'bg-light' : null }}" placeholder="Ingrese monto"onkeypress="return filterFloat(event,this);" id="ammount_pay">
            <span class="input-group-text text-success fw-bold p-1 small" id="basic-addon1">USD {{ $ammount_pay_exchange }}</span>
        </div>
    </div>
</div>

<div class="d-flex justify-content-start">
    @error('ammount_pay') <span class="text-danger small d-block text-right">{{ $message }}</span> @enderror
</div>

@if (!$totalPay)
    <div class="mb-2 gap-1">
        <span class="small text-muted text-left">Separación decimal por punto (.)</span>
        <span class="small text-muted text-left">Admitidos sólo dos (2) decimales. Ej 45.00</span>
    </div>
@endif

