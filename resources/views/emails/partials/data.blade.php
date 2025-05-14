<div style="font-family: 'Courier New', monospace; max-width: 300px; margin: 0 auto; padding: 15px; border: 1px dashed #ccc; background-color: #fff;">
    <div style="text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px;">
        <h4 style="margin: 0; font-size: 16px; font-weight: bold;">TICKET DE REGISTRO</h4>
        <span style="font-size: 12px; color: #666;">{{ $inputs->date ?? null }}</span>
    </div>

    <div style="text-align: center; margin: 10px 0;">
        <span style="font-size: 14px; color: #28a745;">✓ Registro Exitoso</span>
    </div>

    <div style="font-size: 12px; line-height: 1.4;">
        <div style="margin-bottom: 5px;">
            <span style="font-weight: bold;">No. Registro:</span>
            <span style="float: right;">{{ $inputs->id ?? null }}</span>
        </div>
        <div style="margin-bottom: 5px;">
            <span style="font-weight: bold;">Representante:</span>
            <span style="float: right;">{{ $inputs->representant_name ?? null }}</span>
        </div>
        <div style="margin-bottom: 5px;">
            <span style="font-weight: bold;">CI:</span>
            <span style="float: right;">{{ $inputs->ci_representant ?? null }}</span>
        </div>
        <div style="margin-bottom: 5px;">
            <span style="font-weight: bold;">Referencia:</span>
            <span style="float: right;">{{ $inputs->number_i_pay ?? null }}</span>
        </div>
        <div style="margin-bottom: 5px;">
            <span style="font-weight: bold;">Tipo:</span>
            <span style="float: right;">{{ $inputs->type_pay ?? null }}</span>
        </div>
    </div>

    <div style="border-top: 1px dashed #000; margin-top: 10px; padding-top: 10px;">
        <div style="font-size: 14px; font-weight: bold; text-align: right;">
            <span>Total: Bs. {{ number_format($inputs->ammount, 2, ',', '.') ?? null }}</span>
        </div>
    </div>

    <div style="text-align: center; margin-top: 15px; font-size: 10px; color: #666;">
        <div>¡Gracias por su pago!</div>
        <div>Conserve este ticket</div>
    </div>
</div>
