<?php

if (!function_exists('money')) {
    function money($amount, $exchange_amount, $show_exchange = false)
    {
        $formatted = 'Bs ' . number_format($amount, 2, ',', '.');
        if ($show_exchange && $exchange_amount !== null) {
            $formatted .= ' [USD ' . number_format($exchange_amount, 2, ',', '.') . ']';
        }
        return $formatted;
    }
}

if (!function_exists('f_float')) {
    function f_float($value, $decimals = 2)
    {
        return number_format($value, $decimals, ',', '.');
    }
}

if (!function_exists('printLine')) {
    function printLine($char, $length = 32)
    {
        return str_repeat($char, $length);
    }
}
?>

{{-- Contenedor principal para dos recibos --}}
<div id="printable-ticket-area">
    <div class="receipts-container">
        {{-- Primer recibo --}}
        <div class="ticket receipt-copy">
            @include('livewire.administracion.registro-pago.ticket-content')
        </div>
        
        {{-- Segundo recibo (copia) --}}
        <div class="ticket receipt-copy">
            @include('livewire.administracion.registro-pago.ticket-content')
        </div>
    </div>
</div>

{{-- Botón de impresión --}}
<div class="d-flex justify-content-center my-2 py-2 no-print">
    <button class="w-25 btn btn-light btn-sm shadow-sm" onclick="printReceipt()">
        <i class="fas fa-print mr-2"></i> Imprimir Recibo (2 Copias)
    </button>
</div>
